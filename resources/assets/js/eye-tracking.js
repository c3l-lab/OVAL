const gazeConsent = document.cookie.match(new RegExp('(^| )allow_gaze_tracking=([^;]+)'));
// const sessionData = {
//     os: (navigator?.userAgentData?.platform || navigator?.platform) ?? 'undetected',
//     docWidth: document.documentElement.scrollWidth,
//     docHeight: document.documentElement.scrollWidth,
//     sectionId: 1
// };
if (gazeConsent && gazeConsent[2] === "true") {
    window.onload = async function () {
        webgazer.showVideoPreview(false)
            .showPredictionPoints(true)
            .applyKalmanFilter(true)
            .saveDataAcrossSessions(true)
            .showPredictionPoints(false);

        const dot = document.createElement('div');
        dot.style.position = 'absolute';
        dot.style.width = '10px';
        dot.style.height = '10px';
        dot.style.borderRadius = '50%';
        dot.style.backgroundColor = 'red';
        dot.style.zIndex = 99999;

        let records = [];
        const trackGaze = (x, y) => {
            records.push({ x, y, timestamp: Date.now() });

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

        if (cameraAllowed) {
            setInterval(() => {
                if (count == 0) return;
                dot.remove();
                const middleX = sumX / count;
                const middleY = sumY / count;

                dot.style.left = `${middleX}px`;
                dot.style.top = `${middleY}px`;
                document.body.appendChild(dot);

                trackGaze(middleX, middleY);

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

