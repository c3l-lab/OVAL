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
    <script src="https://webgazer.cs.brown.edu/webgazer.js" defer></script>
    <script src="{{ asset('js/plugin/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/plugin/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/calibration.webgazer.js') }}"></script>

    <nav id="webgazerNavbar" class="navbar navbar-expand-lg navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <!-- The hamburger menu button -->
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#myNavbar">
                    <span class="navbar-toggler-icon">Menu</span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <!-- Accuracy -->
                    <li id="Accuracy"><a>Not yet Calibrated</a></li>
                    <li><a onclick="Restart()" href="#">Recalibrate</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a class="helpBtn" onclick="helpModalShow()" href="#"><span
                                class="glyphicon glyphicon-cog"></span> Help</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a class="helpBtn" onclick="window.history.back()"><span class="glyphicon glyphicon-cog"></span>
                            Return</a></li>
                </ul>
            </div>
        </div>
    </nav>
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
                    <button id="closeBtn" type="button" class="btn btn-default" data-bs-dismiss="modal">Close & load
                        saved model </button>
                    <button type="button" id='start_calibration' class="btn btn-primary" data-bs-dismiss="modal"
                        onclick="Restart()">Calibrate</button>
                </div>
            </div>

        </div>
    </div>
</body>

</html>