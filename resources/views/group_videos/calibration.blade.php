<!DOCTYPE html>
<!-- This is a copy from webgazer, source: https://github.com/brownhci/WebGazer/tree/master/www -->
<!--
This is an example HTML that shows how WebGazer can be used on a website.
This file provides the additional features:
  * An integrated, intuitive and sleek action bar with an informative "help" module accessible at all times
  * Structured 9-point calibration system
  * Accuracy measure of predictions based on calibration process
  * Video feedback regarding face positioning
  * Improved eye predictions visible to the user
Instructions on use can be found in the README repository.
-->
<html>

<head>
    <META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=utf-8">
    <TITLE>WebGazer Demo</TITLE>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/calibration.webgazer.css') }}">
    <!-- <script src="./tensorflow.js"></script> -->
</head>

<body LANG="en-US" LINK="#0000ff" DIR="LTR">
    <canvas id="plotting_canvas" width="500" height="500" style="cursor:crosshair;"></canvas>
    <script src="{{ asset('js/plugin/webgazer.js') }}"></script>
    <script src="{{ asset('js/plugin/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/plugin/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/calibration.webgazer.js') }}"></script>
    <!-- Calibration points -->
    <div class="calibrationDiv">
        <input type="button" class="Calibration" id="Pt1"></input>
        <input type="button" class="Calibration" id="Pt2"></input>
        <input type="button" class="Calibration" id="Pt3"></input>
        <input type="button" class="Calibration" id="Pt4"></input>
        <input type="button" class="Calibration" id="Pt5"></input>
        <input type="button" class="Calibration" id="Pt6"></input>
        <input type="button" class="Calibration" id="Pt7"></input>
        <input type="button" class="Calibration" id="Pt8"></input>
        <input type="button" class="Calibration" id="Pt9"></input>
        <input type="button" class="Calibration" id="Pt10"></input>
        <input type="button" class="Calibration" id="Pt11"></input>
        <input type="button" class="Calibration" id="Pt12"></input>
        <input type="button" class="Calibration" id="Pt13"></input>
    </div>

    <!-- Modal -->
    <div id="helpModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body">
                    <img src="{{ asset('img/calibration.png') }}" width="100%" height="100%"
                        alt="webgazer demo instructions"></img>
                </div>
                <div class="modal-footer">
                    <button id="previous_ca" type="button" class="btn btn-default"
                        style="background-color: blanchedalmond" data-bs-dismiss="modal"
                        onclick="window.history.back()">Use Previous
                        Calibration</button>
                    <button type="button" id='start_calibration' class="btn btn-primary" data-bs-dismiss="modal"
                        onclick="Restart()">Calibrate</button>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
