$('document').ready(function () {
	$("#course-name").text(course_name);
	$("#group-name").text(group_name);

	/*------ show tooltip hint ------*/
	$('.tooltip_class').tooltip( {position:'top'} );

	/*------ bind event to button ------*/
	$(".student_views").each(function () {
		$(this).on("click", function () {
			var $userlist = $(this).attr("userlist");
			var $group_video_id = $(this).attr("groupvideoid");

			$.ajax({
				url: "/get_student_view",
				type: "GET",
				data: {
					// user_id: $userlist,
					group_video_id: $group_video_id
				},
				error: function (xhr, status, errorThrown) {
					if (status === 'error') {
						//error handler
						console.log("error /get_student_view - "+errorThrown);//////
					}
				},
				success: function (res) {
					$(".analytics_wrap").empty();
					$("#download_csv").off('click');

					var output_arr = [];

					var head = "<tr>" +
								"<th>Surname</th>" +
								"<th>First name</th>" +
								"<th>User ID</th>" +
								"<th>The portion/percentage of video watched</th>" +
								"<th>First time the video was played</th>" +
								"<th>Last time the video was played</th>" +
								"<th>Number of Page Displays</th>" +
								"<th>Number of annotations viewed</th>" +
								"<th>Was the annotaions file downloaded</th>" +
							 "</tr>";
					
					var output_head = ['Surname', 'First Name', 'User ID', 'The portion/percentage of video watched', 'First time the video was played', 'Last time the video was played', 'Number of Page Displays', 'Number of annotations viewed', 'Average time spent viewing each annotation', 'Was the annotaions file downloaded'];
					output_arr.push(output_head);

					$(".analytics_wrap").append(head);

					for(var i = 0; i < res.length; i++){
						var item = "<tr>"+
									 "<td>" + res[i].surname + "</td>" +
									 "<td>" + res[i].first_name + "</td>" +
									 "<td>" + res[i].student_id + "</td>" +
									 "<td>" + parseFloat(res[i].portion)*100 + "%" + "</td>" +
									 "<td>" + res[i].first_play + "</td>" +
									 "<td>" + res[i].last_play + "</td>" +
									 "<td>" + res[i].comment_view + "</td>" +
									 "<td>" + res[i].annotations_num + "</td>" +
									 "<td>" + res[i].annotations_download_status + "</td>" +
								   "</tr>";

						var output_item =[res[i].surname, 
											res[i].first_name, 
											res[i].student_id, 
											parseFloat(res[i].portion)*100 + "%", 
											res[i].first_play, 
											res[i].last_play, 
											"<td>" + res[i].comment_view + "</td>" +
											res[i].annotations_num, 
											res[i].annotations_download_status];
						output_arr.push(output_item);

						$(".analytics_wrap").append(item);		   
					}

					$("#analytics_modal").modal({ backdrop: 'static', keyboard: false });

					$("#download_csv").on('click', function(){
						exportToCsv('student_views_report.csv', output_arr);
					});

					/*------ bind event to second table ------*/
					$("#download_detail_csv").hide();
					
					$(".analytics_extra_wrap").empty();
					$("#download_detail_csv").off('click');

				}

			})
		})
	});

	$(".annotations_column").each(function (){
		$(this).on("click", function () {
			var $userlist = $(this).attr("userlist");
			var $group_video_id = $(this).attr("groupvideoid");

			$.ajax({
				url: "/get_annotations_column",
				type: "GET",
				data: {
					user_id: $userlist,
					group_video_id: $group_video_id
				},
				error: function (xhr, status, errorThrown) {
					if (status === 'error') {
						//error handler
					}
				},
				success: function (res) {
					$(".analytics_wrap").empty();
					$("#download_csv").off('click');

					var output_arr = [];

					var head = "<tr>" +
								"<th>Surname</th>" +
								"<th>First name</th>" +
								"<th>User ID</th>" +
								"<th>Number of annotations made</th>" +
								"<th>Average annotation length (word count)</th>" +
								"<th>Number of annotations edited</th>" +
								"<th>Number of annotations viewed</th>" +
							   "</tr>";
					
					var output_head = ['Surname', 
										'First Name', 
										'User ID', 
										'Number of annotations made', 
										'Average annotation length (word count)', 
										'Number of annotations edited', 
										'Number of annotations viewed'];

					output_arr.push(output_head);

					$(".analytics_wrap").append(head);

					for(var i = 0; i < res.length; i++){
						var item = "<tr>"+
									 "<td>" + res[i].surname + "</td>" +
									 "<td>" + res[i].first_name + "</td>" +
									 "<td>" + res[i].student_id + "</td>" +
									 "<td>" + res[i].annotation_num + "</td>" +
									 "<td>" + res[i].annotation_average_length + "</td>" +
									 "<td>" + res[i].annotation_edited_num + "</td>" +
									 "<td>" + res[i].annotation_viewed_num + "</td>" +
								   "</tr>";

						var output_item =[res[i].surname, 
											res[i].first_name, 
											res[i].student_id, 
											res[i].annotation_num, 
											res[i].annotation_average_length,
											res[i].annotation_edited_num,
											res[i].annotation_viewed_num,
										 ];
						output_arr.push(output_item);

						$(".analytics_wrap").append(item);		   
					}

					$("#analytics_modal").modal({ backdrop: 'static', keyboard: false });

					$("#download_csv").on('click', function(){
						exportToCsv('annotation_report.csv', output_arr);
					});

					/*------ bind event to second table ------*/
					$("#download_detail_csv").hide();
					
					$(".analytics_extra_wrap").empty();
					$("#download_detail_csv").off('click');

				}

			})
		})
	});

	$(".comment_column").each(function (){
		$(this).on("click", function () {
			var $userlist = $(this).attr("userlist");
			var $group_video_id = $(this).attr("groupvideoid");

			$.ajax({
				url: "/get_comment_column",
				type: "GET",
				data: {
					user_id: $userlist,
					group_video_id: $group_video_id
				},
				error: function (xhr, status, errorThrown) {
					if (status === 'error') {
						//error handler
					}
				},
				success: function (res) {
					$(".analytics_wrap").empty();
					$("#download_csv").off('click');

					var output_arr = [];

					var head = "<tr>" +
								"<th>Surname</th>" +
								"<th>First name</th>" +
								"<th>User ID</th>" +
								"<th>Number of comments made</th>" +
								"<th>Average comment length (word count)</th>" +
								"<th>Number of comments edited</th>" +
								// "<th>Number of comments viewed</th>" +
								// "<th>Average time spent viewing each comment</th>" +
							   "</tr>";
					
					var output_head = ['Surname',
										'First Name',
										'User ID',
										'Number of comments made',
										'Average comment length (word count)',
										'Number of comments edited'
										// 'Number of comments viewed'
										// 'Average time spent viewing each comment'
									];

					output_arr.push(output_head);

					$(".analytics_wrap").append(head);

					for(var i = 0; i < res.length; i++){
						var item = "<tr>"+
									 "<td>" + res[i].surname + "</td>" +
									 "<td>" + res[i].first_name + "</td>" +
									 "<td>" + res[i].student_id + "</td>" +
									 "<td>" + res[i].comment_num + "</td>" +
									 "<td>" + parseInt(res[i].comment_average_length) + "</td>" +
									 "<td>" + res[i].comment_edited_num + "</td>" +
									//  "<td>" + res[i].comment_viewed_num + "</td>" +
									//  "<td>" + seconds_to_HMS(res[i].comment_average_time) + "</td>" +
								   "</tr>";

						var output_item =[res[i].surname, 
											res[i].first_name, 
											res[i].student_id, 
											res[i].comment_num, 
											parseInt(res[i].comment_average_length),
											res[i].comment_edited_num
											// res[i].comment_viewed_num
											// seconds_to_HMS(res[i].comment_average_time)
										 ];
						output_arr.push(output_item);

						$(".analytics_wrap").append(item);		   
					}

					$("#analytics_modal").modal({ backdrop: 'static', keyboard: false });

					$("#download_csv").on('click', function(){
						exportToCsv('comment_report.csv', output_arr);
					});

					/*------ bind event to second table ------*/
					$("#download_detail_csv").hide();
					
					$(".analytics_extra_wrap").empty();
					$("#download_detail_csv").off('click');


				}

			})
		})
	});

	$(".quiz_question").each(function (){
		$(this).on("click", function () {
			var $userlist = $(this).attr("userlist");
			var $group_video_id = $(this).attr("groupvideoid");

			$.ajax({
				url: "/get_quiz_question",
				type: "GET",
				data: {
					user_id: $userlist,
					group_video_id: $group_video_id
				},
				error: function (xhr, status, errorThrown) {
					if (status === 'error') {
						//error handler
					}
				},
				success: function (res) {

					/*------ bind event to first table ------*/
					$(".analytics_wrap").empty();
					$("#download_csv").off('click');

					var output_arr = [];

					var $head = $("<tr></tr>");

					$head.append("<th>Surname</th>" +
					   				"<th>First name</th>" +
					   				"<th>User ID</th>" +
									"<th>Duration of video viewed</th>" +
									"<th>Quiz score</th>"
					  				);
					


					var output_head = ['Surname', 
										'First Name', 
										'User ID', 
										'Duration of video viewed', 
										'Quiz score'
									  ];

					for(var i = 0; i < res[0].quiz_name_list.length; i++){
						//var str = "Quiz Question " + i + 1 + " : " + res[0].quiz_name_list[i].name + " (attempt number)";
						var str =  res[0].quiz_name_list[i].name + " (attempt number)";
						var th = "<th itemname=" + res[0].quiz_name_list[i].name.replace(/ /g,"_") + " >" + str + "</th>"
						
						$head.append(th);
						output_head.push(str);
					}

					output_arr.push(output_head);

					$(".analytics_wrap").append($head);

					for(var i = 0; i < res.length; i++){
						var $item = $("<tr></tr>");

						$item.append("<td>" + res[i].surname + "</td>" +
										"<td>" + res[i].first_name + "</td>" +
										"<td>" + res[i].student_id + "</td>" +
										"<td>" + parseFloat(res[i].portion).toFixed(2)*100 + "%" + "</td>" +
										"<td>" + parseFloat(res[i].score_ratio).toFixed(2)*100 + "%" + "</td>");
						
						var output_item =[res[i].surname, 
											res[i].first_name, 
											res[i].student_id, 
											parseFloat(res[i].portion).toFixed(2)*100, 
											parseFloat(res[i].score_ratio).toFixed(2)*100
										 ];
						

						$(".analytics_wrap tbody tr th").each(function(){
							var item_name = $(this).attr("itemname");

							if(item_name){

								var answer_attempt_length = res[i].answer_attempt.length;

								if(answer_attempt_length > 0){

									var trigger = 0;

									for (var j = 0; j < res[i].answer_attempt.length; j++) {

										if (item_name === res[i].answer_attempt[j].name.replace(/ /g, "_")) {
											var td = "<td>" + res[i].answer_attempt[j].counter + "</td>";

											$item.append(td);
											output_item.push(res[i].answer_attempt[j].counter);		
											trigger = 1;

										}
									}

									if(trigger === 0){

										var td = "<td></td>"
										$item.append(td);
										output_item.push("");
	

									}


								}else{

									var td = "<td></td>"
									$item.append(td);
									output_item.push("");

								}

							}
							
						});
						
						output_arr.push(output_item);

						$(".analytics_wrap").append($item);		   
					}

					$("#download_csv").on('click', function(){
						exportToCsv('quiz_question_report.csv', output_arr);
					});

					/*------ bind event to second table ------*/
					$("#download_detail_csv").hide();
					$("#download_detail_csv").show();

					$(".analytics_extra_wrap").empty();
					$("#download_detail_csv").off('click');

					var $extra_head = $("<tr></tr>");

					$extra_head.append("<th>Surname</th>" +
								  "<th>First name</th>" +
								  "<th>User ID</th>");

					for(var i = 0; i < res[0].quiz_name_list.length; i++){
						var str =  res[0].quiz_name_list[i].name + " (attempt percentage)";
						var th = "<th itemname=" + res[0].quiz_name_list[i].name.replace(/ /g,"_") + " >" + str + "</th>"
						
						$extra_head.append(th);
					}

					$(".analytics_extra_wrap").append($extra_head);

					for(var i = 0; i < res.length; i++){
						var $item = $("<tr></tr>");

						$item.append("<td>" + res[i].surname + "</td>" +
										"<td>" + res[i].first_name + "</td>" +
										"<td>" + res[i].student_id + "</td>");
						

						$(".analytics_extra_wrap tbody tr th").each(function(){
							var item_name = $(this).attr("itemname");

							if(item_name){
								var quiz_poistion = 0;

								for(var x = 0; x < $(".analytics_wrap tbody tr th").length; x++ ){
									var temp_item_name =  $(".analytics_wrap tbody tr th").eq(x).attr("itemname");
									if(temp_item_name === item_name){
										quiz_poistion = x;
									}
								}

								var total = 0;
								
								$(".analytics_wrap tbody tr").each(function(){
									total += Number($(this).find('td').eq(quiz_poistion).text());
								});

	
								$item.append("<td>" + (Number($(".analytics_wrap tbody tr").eq(i+1).find("td").eq(quiz_poistion).text())/total)*100 + " % " + "</td>");

								
							}
							
						});
						

						$(".analytics_extra_wrap").append($item);		   
					}

					/*------ output all student record data ------*/
					$("#download_detail_csv").on('click', function(){

						$.ajax({
							url: "/get_all_student_record",
							type: "GET",
							data: {
								user_id: $userlist,
								group_video_id: $group_video_id
							},
							error: function (xhr, status, errorThrown) {
								if (status === 'error') {
									//error handler
								}
							},
							success: function (res) {

								var detail_output_arr = [];

								var detail_output_head = ['Surname', 
										'First Name', 
										'User ID', 
										'Quiz Name', 
										'Question Name',
										'User Answer'
									];
								
								detail_output_arr.push(detail_output_head);
								
								for(var i = 0; i < res.length; i++){
									for(var j = 0; j < res[i].student_record_list.length; j++){
										var data = JSON.parse(res[i].student_record_list[j].quiz_data);
										for(var k = 0; k < data.items.length; k++){
											var detail_output_item = [res[i].surname,
																	  res[i].first_name,
																	  res[i].student_id,
																	  decodeText(data.name),
																	  decodeText(data.items[k].title),
																	  decodeText(String(data.items[k].user_ans))];
											detail_output_arr.push(detail_output_item);
										}
									}
								}

								exportToCsv('student_all_record_report.csv', detail_output_arr);
								
							}
						});

					});

					/*------ show modal ------*/
					$("#analytics_modal").modal({ backdrop: 'static', keyboard: false });

				}

			})
		})
	});

	$(".key_point").each(function(){
		$(this).on("click", function(){
			var $userlist = $(this).attr("userlist");
			var $group_video_id = $(this).attr("groupvideoid");

			$.ajax({
				url: "/get_key_point",
				type: "GET",
				data: {
					user_id: $userlist,
					group_video_id: $group_video_id
				},
				error: function (xhr, status, errorThrown) {
					if (status === 'error') {
						//error handler
					}
				},
				success: function (res) {
					$(".analytics_wrap").empty();
					$("#download_csv").off('click');

					var output_arr = [];

					var $head = $("<tr></tr>");


					$head.append("<th>Surname</th>" +
					   				"<th>First name</th>" +
					   				"<th>User ID</th>" +
					   				"<th>Comment</th>" +
									"<th>Point</th>" +
									"<th>Confidence rating</th>");
					
					var output_head = ['Surname', 
										'First Name', 
										'User ID', 
										'Comment', 
										'Point',
										'Confidence rating'
									  ];

					output_arr.push(output_head);

					$(".analytics_wrap").append($head);

					for(var i = 0; i < res.length; i++){
						
						if(res[i].key_info.length > 0){

							var output_item =[res[i].surname, 
												res[i].first_name, 
												res[i].student_id
											 ];

							for (var j = 0; j < res[i].key_info.length; j++) {

								var $item = $("<tr></tr>");

								if (j === 0) {

									$item.append("<td>" + res[i].surname + "</td>" +
										"<td>" + res[i].first_name + "</td>" +
										"<td>" + res[i].student_id + "</td>");

								} else {

									$item.append("<td></td>" +
										"<td></td>" +
										"<td></td>");

								}

								var condifence_rating = "";

								switch (parseInt(res[i].key_info[j].level)) {
									case 1:
										condifence_rating = "very low";
										break;
									case 2:
										condifence_rating = "low";
										break;
									case 3:
										condifence_rating = "medium";
										break;
									case 4:
										condifence_rating = "high";
										break;
									case 5:
										condifence_rating = "very high";
										break;
									default:
										condifence_rating = "no rating";
										break;
								}

								$item.append("<td>" + res[i].key_info[j].comments_description + "</td>" +
									"<td>" + res[i].key_info[j].points_description + "</td>" +
									"<td>" + condifence_rating + "</td>"
								);

								output_item.push(res[i].key_info[j].comments_description,
												  res[i].key_info[j].points_description,
												  condifence_rating
												);

								$(".analytics_wrap").append($item);

								output_arr.push(output_item);

							}

						}else{

							var output_item =[res[i].surname, 
												res[i].first_name, 
												res[i].student_id,
												"",
												"",
												""
											 ];

							var $item = $("<tr></tr>");

							$item.append("<td>" + res[i].surname + "</td>" +
											"<td>" + res[i].first_name + "</td>" +
											"<td>" + res[i].student_id + "</td>" +
										    "<td></td>" +
											"<td></td>" +
											"<td></td>");

							$(".analytics_wrap").append($item);

							output_arr.push(output_item);

						}

					}

					$("#analytics_modal").modal({ backdrop: 'static', keyboard: false });

					$("#download_csv").on('click', function(){
						exportToCsv('key_point_report.csv', output_arr);
					});

					/*------ bind event to second table ------*/
					$("#download_detail_csv").hide();

					$(".analytics_extra_wrap").empty();
					$("#download_detail_csv").off('click');

				}

			})
		});
	});

	/*------ modal open event ------*/
	$('#analytics_modal').on('show.bs.modal', function (e) {
		//draw_chart();
	});

});

function exportToCsv(filename, rows) {
	var processRow = function (row) {
		var finalVal = '';
		for (var j = 0; j < row.length; j++) {
			var innerValue = row[j] === null ? '' : row[j].toString();
			if (row[j] instanceof Date) {
				innerValue = row[j].toLocaleString();
			};
			var result = innerValue.replace(/"/g, '""');
			if (result.search(/("|,|\n)/g) >= 0)
				result = '"' + result + '"';
			if (j > 0)
				finalVal += ',';
			finalVal += result;
		}
		return finalVal + '\n';
	};

	var csvFile = '';
	for (var i = 0; i < rows.length; i++) {
		csvFile += processRow(rows[i]);
	}

	var blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
	if (navigator.msSaveBlob) { // IE 10+
		navigator.msSaveBlob(blob, filename);
	} else {
		var link = document.createElement("a");
		if (link.download !== undefined) { // feature detection
			// Browsers that support HTML5 download attribute
			var url = URL.createObjectURL(blob);
			link.setAttribute("href", url);
			link.setAttribute("download", filename);
			link.style.visibility = 'hidden';
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}
	}
}

function draw_chart(){

	// set the dimensions and margins of the graph
	var margin = { top: 20, right: 20, bottom: 100, left: 50 },
		width = $(window).width() - margin.left - margin.right - 180,
		height = 500 - margin.top - margin.bottom;

	// // parse the date / time
	//var parseTime = d3.timeParse("%Y-%m-%d %H:%M:%S");;

	var svg = d3.selectAll(".chart_canvas").append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append("g")
		.attr("transform",
		"translate(" + margin.left + "," + margin.top + ")");

	var data = [{
	    "date": 0.56,
	    "letter": "A",
	    "frequency": 3
	},{
	    "date": 0.67,
	    "letter": "A",
	    "frequency": 4
	},{
	    "date": 0.10,
	    "letter": "A",
	    "frequency": 2
	},{
	    "date": 0.20,
	    "letter": "A",
	    "frequency": 4
	}]

	// data.forEach(function (d) {
	// 	d.date = parseTime(d.date);
	// });

	var x = d3.range([1, 0]),
		y = d3.scaleLinear()
			.range([height, 0]);

	var g = svg.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");


	var zoom = d3.zoom()
		.scaleExtent([1, 40])
		.translateExtent([[-100, -100], [width + 90, height + 100]])
		.on("zoom", zoomed);


	x.domain([0, d3.max(data, function (d) { return d.date; })]),
	y.domain([0, d3.max(data, function (d) { return d.frequency; })]);

	var tooltip = d3.select(".chart_canvas")
		.append("div")
		.attr('class', "tooltip_style")
		.style("position", "absolute")
		.style("z-index", "9999")
		.style("visibility", "hidden")
		.style("color", "black")

	g.append("g")
		.attr("class", "axis axis--x")
		.attr("transform", "translate(0," + height + ")")
		.call(d3.axisBottom(x)
			.tickFormat(d3.timeFormat("%Y-%m-%d %H:%M")))
		.selectAll("text")
		.style("text-anchor", "end")
		.attr("transform", "rotate(0)")

	g.append("g")
		.attr("class", "axis axis--y")
		.call(d3.axisLeft(y).ticks(5))
		.append("text")
		.attr("transform", "rotate(-90)")
		.attr("y", 6)
		.attr("dy", "0.71em")
		.attr("text-anchor", "end")
		.text("Frequency");

	g.selectAll(".bar")
		.data(data)
		.enter().append("rect")
		.attr("bar_value", function (d) { return d.frequency; })
		.attr("bar_time", function (d) { return d.date; })
		.attr("class", "bar")
		.attr("x", function (d) { return x(d.date); })
		.attr("y", function (d) { return y(d.frequency); })
		.attr("width", 10)
		.attr("height", function (d) { return height - y(d.frequency); });

	g.selectAll("rect")
		.on("mouseover", function (d, i) {

			tooltip.style("visibility", "visible");

			var xPos = parseFloat(d3.select(this).attr("x"));
			var yPos = parseFloat(d3.select(this).attr("y"));


			tooltip.style("top", (event.pageY - 10) + "px").style("left", (event.pageX + 10) + "px");

			var x_p = document.createElement("p");
			x_p.innerHTML = "Time period : " + d3.select(this).attr("bar_time").substring(0, 24);

			var y_p = document.createElement("p");
			y_p.innerHTML = "In this hour, total view number is : " + d3.select(this).attr("bar_value")

			d3.select(".tooltip_style")
				.append(function () { return x_p; })
				.append(function () { return y_p; })

		})
		.on("mouseout", function () {

			tooltip.style("visibility", "hidden");
			d3.select(".tooltip_style")
				.selectAll("*")
				.remove();


		})


	function zoomed() {
		view.attr("transform", d3.event.transform);
		gX.call(xAxis.scale(d3.event.transform.rescaleX(x)));
		gY.call(yAxis.scale(d3.event.transform.rescaleY(y)));
	}

	
}

/*------ general function ------*/
function seconds_to_HMS(second) {
    second = Number(second);
    var h = Math.floor(second / 3600);
    var m = Math.floor(second % 3600 / 60);
    var s = Math.floor(second % 3600 % 60);

    var hDisplay = h > 0 ? h + (h == 1 ? " hour : " : " hours : ") : "";
    var mDisplay = m > 0 ? m + (m == 1 ? " minute : " : " minutes : ") : "";
    var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
    return hDisplay + mDisplay + sDisplay; 
}
/*------ end general function ------*/

/*------ string process functions ------*/
function encodeText(txt) {
	return txt.replace(/\&/g, '&amp;').replace(/\</g, '&lt;').replace(/\>/g, '&gt;').replace(/\"/g, '&quot;').replace(/\'/g, '&apos;');
}
function decodeText(txt) {
	return txt.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&apos;/g, "'");
}
/*------ string process functions ------*/






