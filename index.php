<html>
<head>
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/jquery-ui-dist/jquery-ui.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="receiver.js"></script>
    <link rel="stylesheet" href="node_modules/jquery-ui-dist/jquery-ui.min.css">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="receiver.css">
</head>
<body>
<div>
    <button id="powerOn" class="unSwitchable" disabled="disabled">Power On</button>
    <button id="powerOff" class="switchable" disabled="disabled">Power Off</button>
    <button id="powerStatus" class="powerBlack" disabled="disabled"></button>
</div>
<div>
    <button id="volumeUp" class="switchable" disabled="disabled">Volume Up</button>
    <button id="volumeDown" class="switchable" disabled="disabled">Volume Down</button>
    <button id="volumeMute" class="switchable" disabled="disabled">Mute</button>
    <button id="volumeStatus" disabled="disabled"></button>
</div>
<div>
    <div id="volumeSlider">
        <div id="custom-handle" class="ui-slider-handle"></div>
    </div>
</div>
<div>
    <button id="functionUp" class="switchable" disabled="disabled">Next Function</button>
    <button id="functionDown" class="switchable" disabled="disabled">Previous Function</button>
    <button id="functionStatus" disabled="disabled"></button>
    <div id="functionHistory"></div>
</div>
</body>
</html>

