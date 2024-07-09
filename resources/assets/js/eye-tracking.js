const gazeConsent = document.cookie.match(new RegExp('(^| )allow_gaze_tracking=([^;]+)'));

if (gazeConsent && gazeConsent[2] === "true") {
    const guessElement = (x, y) => {
        if (!window.cRect || !window.vRect || !window.aRect) {
            return "O";
        }

        if (
            x >= window.cRect.left &&
            x <= window.cRect.right &&
            y >= window.cRect.top &&
            y <= window.cRect.bottom
        ) {
            return "C";
        }

        if (
            x >= window.aRect.left &&
            x <= window.aRect.right &&
            y >= window.aRect.top &&
            y <= window.aRect.bottom
        ) {
            return "A";
        }

        if (
            x >= window.vRect.left &&
            x <= window.vRect.right &&
            y >= window.vRect.top &&
            y <= window.vRect.bottom
        ) {
            return "V";
        }

        return "O";
    }

    window.onload = async function () {
        webgazer.showVideoPreview(false)
            .applyKalmanFilter(true)
            .saveDataAcrossSessions(true)
            .showPredictionPoints(false);

        const frequency = 500;
        let sumX = 0;
        let sumY = 0;
        let count = 0;
        let cameraAllowed = true;

        await webgazer.setGazeListener(function (data, elapsedTime) {
            if (data) {
                sumX += data.x + window.scrollX;
                sumY += data.y + window.scrollY;
                count++;
            }
        })
            .setRegression('ridge')
            .begin()
            .catch((e) => {
                alert("Please allow us to use your camera and reload this page.");
                cameraAllowed = false;
            });

        document.getElementById('webgazerGazeDot').remove();

        if (cameraAllowed) {
            const dot = document.createElement('div');
            dot.style.position = 'absolute';
            dot.style.width = '10px';
            dot.style.height = '10px';
            dot.style.borderRadius = '50%';
            dot.style.backgroundColor = 'red';
            dot.style.zIndex = 99999;

            const flag = document.createElement('span');
            flag.textContent = 'C';
            flag.style.position = 'absolute';
            flag.style.color = 'black';
            flag.style.zIndex = 99999;

            let records = [];
            const trackGaze = (x, y, el) => {
                records.push({ x, y, timestamp: Date.now(), el });

                if (records.length < 10) return;

                const tmp = records;
                records = [];
                $.ajax({
                    type: "POST",
                    url: "/trackings/eye_tracking_store",
                    data: { data: tmp },
                    error: function (request, status, error) {
                        console.log("Error on tracking: ");
                        console.log(request.responseText);
                    },
                });
            }
            window.addEventListener('beforeunload', function (e) {
                $.ajax({
                    type: "POST",
                    url: "/trackings/eye_tracking_store",
                    data: { data: records },
                    error: function (request, status, error) {
                        console.log("Error on tracking: ");
                        console.log(request.responseText);
                    },
                });
            });

            window.getElPosition = (element) => {
                const rect = element.getBoundingClientRect();
                const top = rect.top + window.scrollY;
                const left = rect.left + window.scrollX;
                const right = rect.right + window.scrollX;
                const bottom = rect.bottom + window.scrollY;

                return { top, left, right, bottom };
            }

            window.cRect = window.getElPosition($("#right-side")[0]); // comment sizes
            const $leftSize = $("#left-side .video-width");
            window.vRect = window.getElPosition($leftSize[0]); // annotation sizes
            window.aRect = window.getElPosition($leftSize[1]); // video sizes

            setInterval(() => {
                if (count == 0) return;
                dot.remove();
                flag.remove();
                const middleX = sumX / count;
                const middleY = sumY / count;
                const el = guessElement(middleX, middleY);

                dot.style.left = `${middleX}px`;
                dot.style.top = `${middleY}px`;
                document.body.appendChild(dot);

                flag.style.left = `${middleX}px`;
                flag.style.top = `${middleY + 10}px`;
                flag.textContent = el;
                document.body.appendChild(flag);

                trackGaze(middleX, middleY, el);

                // Reset for the next interval
                sumX = 0;
                sumY = 0;
                count = 0;
            }, frequency);


            setTimeout(() => {
                webgazer.saveDataAcrossSessions(false);
            }, 5000);
        }
    }
}

