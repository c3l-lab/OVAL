$(document).ready(function () {
    const form = $('#create-structure-annotation-form form');
    let count = 0;
    let config = undefined;
    let observer = undefined;
    let groupVideoId = 0;

    $(".annotation-config-button").on("click", function () {
        groupVideoId = Number($(this).data("group-video-id"));
    })

    let clonedInputOption = $('#option-input').clone(true);
    clonedInputOption.removeClass("hidden");
    clonedInputOption.removeAttr('id');
    clonedInputOption.addClass('option-input');

    const parentOptionInput = $('#option-input').parent();
    const btnGroup = $("#create-structure-annotation-form .btn-group");

    $('#set-annotation-btn').on('click', function () {
        form.find('.question').remove();
        config = window.group_videos.find((e) => e.id == groupVideoId)?.annotation_config;

        if (config && config.structured_annotations?.length) {
            config.structured_annotations.forEach(function (e, idx) {
                if (e.type === "multiple_choice") {
                    addMultipleChoiceQuestion(e, idx, form);
                } else if (e.type === "short_question") {
                    addShortQuestion(e, idx, form);
                }
            })
        }

        count = form.find('.question').length;
        if (count == 0) {
            $("#remove-question").attr('disabled', 'disabled');
        }
        $('#create-structure-annotation-form').show();
        observer = checkChanges(form, config);
        $('#change_warning').addClass('hidden');
    });

    $('#close-set-annotation-btn').on('click', function () {
        $('#create-structure-annotation-form').hide();
        observer?.disconnect();
    });

    $('#add-multiple-choice-question').on('click', function () {
        btnGroup.addClass('hidden');
        const firstOption = clonedInputOption.clone(true, true);
        // firstOption.find("input[type=radio]").prop("checked", true);
        parentOptionInput.append(firstOption);
        $("#add-multiple-choice-question-sub").removeClass('hidden');
    });

    $('#add-short-question').on('click', function () {
        btnGroup.addClass('hidden');
        $("#add-short-question-sub").removeClass('hidden');
    });

    $('#remove-question').on('click', function () {
        if (count == 1) {
            $(this).attr('disabled', 'disabled');
        }
        form.find('.question').last().remove();
        count--;
    })

    $("#create-structure-annotation-form .cancel-btn").on('click', function () {
        btnGroup.addClass('hidden');
        $(".option-input").remove();
        $("#add-question").removeClass('hidden');
    });

    $("#add-option-sub").on("click", function () {
        parentOptionInput.append(clonedInputOption.clone(true, true));
        $("#remove-option-sub").removeAttr('disabled');
    });

    $("#remove-option-sub").on("click", function () {
        if ((parentOptionInput).find(".option-input").length == 1) {
            $(this).attr('disabled', 'disabled');
        }
        parentOptionInput.find(".option-input").last().remove();
    });

    $('#confirm-multiple-choice-question-sub').on('click', function () {
        const multipleChoiceData = { title: '', options: [], name: `question-${count}` };
        const titleEl = $('#add-multiple-choice-question-sub input[type=text]').eq(0);

        multipleChoiceData.title = titleEl.val();
        titleEl.val("");

        parentOptionInput.find('.option-input').each(function (idx) {
            multipleChoiceData.options.push({
                value: `option-${idx}`,
                description: $(this).find('input[type=text]').val(),
                // ans: $(this).parent().find("input[type=radio]").is(':checked'),
            });
            $(this).remove();
        });

        addMultipleChoiceQuestion(multipleChoiceData, count, form);
        count++;
        btnGroup.addClass('hidden');
        $("#add-question").removeClass('hidden');
        $("#remove-question").removeAttr('disabled');
    });

    $('#confirm-short-question-sub').on('click', function () {
        const titleEl = $("#add-short-question-sub input[type=text]");
        addShortQuestion({ title: titleEl.val(), name: `question-${count}` }, count, form);
        titleEl.val("");
        count++;
        btnGroup.addClass('hidden');
        $("#add-question").removeClass('hidden');
        $("#remove-question").removeAttr('disabled');
    })

    $('#submit-question').on('click', function () {
        $("#create-structure-annotation-form").addClass("modal-loading");
        const formData = getFormData(form);
        $.ajax({
            type: "POST",
            url: `/group_videos/${groupVideoId}/config_structured_annotation`,
            data: { structured_annotations: formData },
            success: function () {
                config.structured_annotations = formData;
            },
            complete: function () {
                $("#create-structure-annotation-form").removeClass("modal-loading")
                $('#create-structure-annotation-form').hide();
                observer.disconnect();
            }
        });
    });
});

function addMultipleChoiceQuestion(multipleChoiceData, id, formToAdd) {
    if ((!multipleChoiceData?.title) || (!multipleChoiceData?.options?.length)) {
        alert("Title and options cannot be empty");
        return;
    }

    const wrapper = $('<div class="question">');
    wrapper.append($(`<h3 data-title="${multipleChoiceData.title}">${id + 1}. ${multipleChoiceData.title}</h3>`));
    multipleChoiceData.options.forEach(e => {
        if (!e.description) {
            alert("Option cannot be empty");
            return;
        }
        const option = $(`<label><input type="radio" name="${multipleChoiceData.name}" value="${e.value}"} disabled>${e.description}</label>`);
        if (e.ans) option.find("input").prop("checked", true);
        wrapper.append(option);
    });
    formToAdd.append(wrapper);

}

function addShortQuestion(shortQuestionData, id, formToAdd) {
    if (!shortQuestionData.title) {
        alert("Title cannot be empty");
        return;
    }

    const wrapper = $('<div class="question">');
    wrapper.append($(`<h3 data-title="${shortQuestionData.title}">${id + 1}. ${shortQuestionData.title}</h3>`));
    wrapper.append($(`<textarea rows="1" name="${shortQuestionData.name}" class="text-input" disabled></textarea>`));
    formToAdd.append(wrapper);
}

function getFormData(form) {
    const submit_data = [];
    form.find(".question").each(function () {
        if ($(this).find("input[type=radio]").length > 0) {
            var options = $(this).find("input[type=radio]").map(function () {
                return {
                    value: $(this).val(),
                    description: $(this).parent().text(),
                    // ans: $(this).is(':checked'),
                };
            }).get();

            submit_data.push({
                type: "multiple_choice",
                title: $(this).find("h3").data("title"),
                name: $(this).find("input[type=radio]").attr("name"),
                options: options,
            });

            return;
        }

        submit_data.push({
            type: "short_question",
            title: $(this).find("h3").data("title"),
            name: $(this).find("textarea").attr("name"),
        });
    });

    return submit_data;
}

function checkChanges(form, config) {
    const observer = new MutationObserver(function (mutationsList, observer) {
        for (let mutation of mutationsList) {
            if (mutation.type === 'childList' || mutation.type === 'subtree') {
                if (JSON.stringify(getFormData(form)) === JSON.stringify(config.structured_annotations)) {
                    $('#change_warning').addClass('hidden');
                } else {
                    $('#change_warning').removeClass('hidden');
                }
            }
        }
    });

    const observerConfig = { attributes: false, childList: true, subtree: true, characterData: false };
    observer.observe(form[0], observerConfig);

    return observer;
}