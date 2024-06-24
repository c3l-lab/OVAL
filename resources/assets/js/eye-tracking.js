

var gazeConsent = document.cookie.match(new RegExp('(^| )allow_gaze_tracking=([^;]+)'));

if (gazeConsent && gazeConsent[2] === "true") {
    window.onload = async function () {
        webgazer.showVideoPreview(false)
            .showPredictionPoints(true)
            .applyKalmanFilter(true)
            .saveDataAcrossSessions(true)
            .showPredictionPoints(false);

        const frequency = 500;
        const sum = { x: 0, y: 0 };
        let count = 0;
        let cameraAllowed = true;

        await webgazer.setGazeListener(function (data, elapsedTime) {
            if (data) {
                sum.x += data.x + window.scrollX;
                sum.y += data.y + window.scrollY;
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

                const middleX = sum.x / count;
                const middleY = sum.y / count;

                // Create a dot at the median gaze point
                let dot = document.createElement('div');
                dot.style.position = 'absolute';
                dot.style.left = `${middleX}px`;
                dot.style.top = `${middleY}px`;
                dot.style.width = '10px';
                dot.style.height = '10px';
                dot.style.borderRadius = '50%';
                dot.style.backgroundColor = 'red';
                dot.style.zIndex = 99999;
                document.body.appendChild(dot);

                // Make the dot disappear after 1 second
                setTimeout(() => {
                    dot.remove();
                }, 500);

                // Reset for the next interval
                sum.x = 0;
                sum.y = 0;
                count = 0;
            }, frequency);


            setTimeout(() => {
                webgazer.saveDataAcrossSessions(false);
            }, 5000);
        }

    }
}

