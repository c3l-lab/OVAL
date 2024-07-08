// This is a copy version from source:
// https://github.com/brownhci/WebGazer/tree/master/www


window.onload = async function () {
    let cameraAllowed = true;
    //start the webgazer tracker
    await webgazer.setRegression('ridge') /* currently must set regression and tracker */
        //.setTracker('clmtrackr')
        .setGazeListener(function (data, clock) {
            //   console.log(data); /* data is an object containing an x and y key which are the x and y prediction coordinates (no bounds limiting) */
            //   console.log(clock); /* elapsed time in milliseconds since webgazer.begin() was called */
        })
        .saveDataAcrossSessions(true)
        .begin()
        .catch((e) => {
            alert("Please allow us to use your camera and reload this page.");
            cameraAllowed = false;
        });

    if (!cameraAllowed) {
        document.body.addEventListener('click', function (event) {
            alert("Camera is not allow, please allow us to use your camera and reload this page");
            event.preventDefault();  // Prevents default behavior of the click
            event.stopPropagation(); // Stops the click from propagating to other elements
        }, true);

        return;
    }
    webgazer.showVideoPreview(true) /* shows all video previews */
        .showPredictionPoints(true) /* shows a square every 100 milliseconds where current prediction is */
        .applyKalmanFilter(true); /* Kalman Filter defaults to on. Can be toggled by user. */

    //Set up the webgazer video feedback.
    var setup = function () {

        //Set up the main canvas. The main canvas is used to calibrate the webgazer.
        var canvas = document.getElementById("plotting_canvas");
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        canvas.style.position = 'fixed';
    };
    setup();
};

document.addEventListener('DOMContentLoaded', async function () {
    if (!(await isCalibrated())) {
        document.getElementById("recalibrate").remove();
        document.getElementById("previous_ca").remove();
    }
});


// Set to true if you want to save the data even if you reload the page.
window.saveDataAcrossSessions = true;

window.onbeforeunload = function () {
    webgazer.end();
}

/**
 * Restart the calibration process by clearing the local storage and reseting the calibration point
 */
window.Restart = function Restart() {
    webgazer.clearData();
    ClearCalibration();
    PopUpInstruction();
}

var PointCalibrate = 0;
var CalibrationPoints = {};

// Find the help modal
var helpModal;

/**
 * Clear the canvas and the calibration button.
 */
function ClearCanvas() {
    document.querySelectorAll('.Calibration').forEach((i) => {
        i.style.setProperty('display', 'none');
    });
    var canvas = document.getElementById("plotting_canvas");
    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
}

/**
 * Show the instruction of using calibration at the start up screen.
 */
function PopUpInstruction() {
    ClearCanvas();
    swal({
        title: "Calibration",
        text: "Please click on each of the 13 points on the screen. You must click on each point 5 times till it goes yellow. This will calibrate your eye movements. (There are 1 points on the face cam)",
        buttons: {
            cancel: false,
            confirm: true
        }
    }).then(isConfirm => {
        ShowCalibrationPoint();
    });

}
/**
  * Show the help instructions right at the start.
  */
function helpModalShow() {
    if (!helpModal) {
        helpModal = new bootstrap.Modal(document.getElementById('helpModal'))
    }
    helpModal.show();
}

function calcAccuracy() {
    swal({
        title: "Calibration Successfully",
        allowOutsideClick: false,
        buttons: {
            confirm: true,
        }
    }).then(isConfirm => {
        if (isConfirm) {
            window.history.back();
        }
    });
}

function calPointClick(node) {
    const id = node.id;

    if (!CalibrationPoints[id]) { // initialises if not done
        CalibrationPoints[id] = 0;
    }
    CalibrationPoints[id]++; // increments values

    if (CalibrationPoints[id] == 5) { //only turn to yellow after 5 clicks
        node.style.setProperty('background-color', 'yellow');
        node.setAttribute('disabled', 'disabled');
        PointCalibrate++;
    } else if (CalibrationPoints[id] < 5) {
        //Gradually increase the opacity of calibration points when click to give some indication to user.
        var opacity = 0.2 * CalibrationPoints[id] + 0.2;
        node.style.setProperty('opacity', opacity);
    }

    if (PointCalibrate >= 13) {
        document.querySelectorAll('.Calibration').forEach((i) => {
            i.style.setProperty('display', 'none');
        });

        var canvas = document.getElementById("plotting_canvas");
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

        calcAccuracy();
    }
}

/**
 * Load this function when the index page starts.
* This function listens for button clicks on the html page
* checks that all buttons have been clicked 5 times each, and then goes on to measuring the precision
*/
//$(document).ready(function(){
function docLoad() {
    ClearCanvas();
    helpModalShow();

    // click event on the calibration buttons
    document.querySelectorAll('.Calibration').forEach((i) => {
        i.addEventListener('click', () => {
            calPointClick(i);
        })
    })
};
window.addEventListener('load', docLoad);

/**
 * Show the Calibration Points
 */
function ShowCalibrationPoint() {
    document.querySelectorAll('.Calibration').forEach((i) => {
        i.style.removeProperty('display');
    });
}

/**
* This function clears the calibration buttons memory
*/
function ClearCalibration() {
    // Clear data from WebGazer

    document.querySelectorAll('.Calibration').forEach((i) => {
        i.style.setProperty('background-color', 'red');
        i.style.setProperty('opacity', '0.2');
        i.removeAttribute('disabled');
    });

    CalibrationPoints = {};
    PointCalibrate = 0;
}

// sleep function because java doesn't have one, sourced from http://stackoverflow.com/questions/951021/what-is-the-javascript-version-of-sleep
function sleep(time) {
    return new Promise((resolve) => setTimeout(resolve, time));
}

/*
 * This function calculates a measurement for how precise 
 * the eye tracker currently is which is displayed to the user
 */
function calculatePrecision(past50Array) {
    var windowHeight = window.innerHeight;
    var windowWidth = window.innerWidth;

    // Retrieve the last 50 gaze prediction points
    var x50 = past50Array[0];
    var y50 = past50Array[1];

    // Calculate the position of the point the user is staring at
    var staringPointX = windowWidth / 2;
    var staringPointY = windowHeight / 2;

    var precisionPercentages = new Array(50);
    calculatePrecisionPercentages(precisionPercentages, windowHeight, x50, y50, staringPointX, staringPointY);
    var precision = calculateAverage(precisionPercentages);

    // Return the precision measurement as a rounded percentage
    return Math.round(precision);
};

/*
 * Calculate percentage accuracy for each prediction based on distance of
 * the prediction point from the centre point (uses the window height as
 * lower threshold 0%)
 */
function calculatePrecisionPercentages(precisionPercentages, windowHeight, x50, y50, staringPointX, staringPointY) {
    for (x = 0; x < 50; x++) {
        // Calculate distance between each prediction and staring point
        var xDiff = staringPointX - x50[x];
        var yDiff = staringPointY - y50[x];
        var distance = Math.sqrt((xDiff * xDiff) + (yDiff * yDiff));

        // Calculate precision percentage
        var halfWindowHeight = windowHeight / 2;
        var precision = 0;
        if (distance <= halfWindowHeight && distance > -1) {
            precision = 100 - (distance / halfWindowHeight * 100);
        } else if (distance > halfWindowHeight) {
            precision = 0;
        } else if (distance > -1) {
            precision = 100;
        }

        // Store the precision
        precisionPercentages[x] = precision;
    }
}

/*
 * Calculates the average of all precision percentages calculated
 */
function calculateAverage(precisionPercentages) {
    var precision = 0;
    for (x = 0; x < 50; x++) {
        precision += precisionPercentages[x];
    }
    precision = precision / 50;
    return precision;
}

/*
* Sets store_points to true, so all the occuring prediction
* points are stored
*/
function store_points_variable() {
    webgazer.params.storingPoints = true;
}

/*
 * Sets store_points to false, so prediction points aren't
 * stored any more
 */
function stop_storing_points_variable() {
    webgazer.params.storingPoints = false;
}

if (document.readyState !== 'loading') {
    fn();
}

document.addEventListener('DOMContentLoaded', () => {
    /**
    * This function occurs on resizing the frame
    * clears the canvas & then resizes it (as plots have moved position, can't resize without clear)
    */
    function resize() {
        var canvas = document.getElementById('plotting_canvas');
        var context = canvas.getContext('2d');
        context.clearRect(0, 0, canvas.width, canvas.height);
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    };
    window.addEventListener('resize', resize, false);
});

function isCalibrated() {
    return new Promise((resolve, reject) => {
        // Open a connection to the IndexedDB database named "localforage"
        const request = indexedDB.open("localforage", 2);

        request.onerror = (event) => {
            reject("Failed to open DB: " + event.target.errorCode);
        };

        request.onupgradeneeded = (event) => {
            // Create the object store if this is the first time
            const db = event.target.result;
            if (!db.objectStoreNames.contains("keyvaluepair")) {
                db.createObjectStore("keyvaluepair", { keyPath: "id" });
            }
        };

        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction("keyvaluepairs", "readonly");
            const store = transaction.objectStore("keyvaluepairs");
            const getAllRequest = store.get('webgazerGlobalData');

            getAllRequest.onerror = (event) => {
                reject("Failed to fetch data: " + event.target.errorCode);
            };

            getAllRequest.onsuccess = (event) => {
                if (Array.isArray(event.target.result) && event.target.result.length > 45) {
                    return resolve(true);
                }

                return resolve(false);
            };
        };
    });
}

