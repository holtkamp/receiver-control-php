<html>
<head>
    <link rel="stylesheet" href="./node_modules/jquery-ui-dist/jquery-ui.min.css">
    <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.css">
    <link rel="stylesheet" href="./node_modules/open-iconic/font/css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="receiver.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
</head>
<body>
<div class="container-fluid">
    <br/>
    <div class="jumbotron">

        <div class="row">
            <div class="col">
                <button class="switchable btn btn-danger" data-command="<?php echo \ReceiverControl\Command\Power\Off::class;?>"><span class="oi oi-power-standby"></span> Off</button>
            </div>
            <div class="col text-center ">
                <h1>Main</h1>
            </div>
            <div class="col text-right">
                <button class="unSwitchable btn btn-success" data-command="<?php echo \ReceiverControl\Command\Power\On::class;?>"><span class="oi oi-power-standby"></span> On</button>
            </div>
        </div>
        <br/>
        <div  class="row">
            <div class="col">
                <div id="volumeSlider">
                    <div id="custom-handle" class="ui-slider-handle"></div>
                </div>
            </div>
        </div>

        <br/>
        <div class="row">
            <div class="col">
                <button class="switchable btn btn-lg" data-command="<?php echo \ReceiverControl\Command\Volume\Down::class;?>"><span class="oi oi-volume-low"></span></button>
            </div>
            <div class="col text-center">
                <button class="switchable btn btn-lg" data-command="<?php echo \ReceiverControl\Command\Volume\Down::class;?>"><span class="oi oi-volume-off"></span></button>
            </div>
            <div class="col text-right">
                <button class="switchable btn btn-lg" data-command="<?php echo \ReceiverControl\Command\Volume\Up::class;?>"><span class="oi oi-volume-high"></span></button>
            </div>
        </div>
    </div>

    <div class="jumbotron">
        <div class="row">
            <div class="col">
                <button class="switchable btn btn-danger" data-zone-number="2" data-command="<?php echo \ReceiverControl\Command\Power\Off::class;?>"><span class="oi oi-power-standby"></span> Off</button>
            </div>
            <div class="col text-center">
                <h1>2nd</h1>
            </div>
            <div class="col text-right">
                <button class="unSwitchable btn btn-success" data-zone-number="2" data-command="<?php echo \ReceiverControl\Command\Power\On::class;?>"><span class="oi oi-power-standby"></span> On</button>
            </div>
        </div>

        <br/>

        <div  class="row">
            <div class="col">
                <input type="range" step="0.5" min="0" max="50" data-zone-number="2" onchange="console.log(parseFloat(this.value), $(this).data())"/>
            </div>
        </div>

        <br/>

        <div class="row">
            <div class="col">
                <button class="switchable btn btn-lg" data-zone-number="2" data-command="<?php echo \ReceiverControl\Command\Volume\Down::class;?>"><span class="oi oi-volume-low"></span></button>
            </div>
            <div class="col text-center">
                <button class="switchable btn btn-lg" data-zone-number="2" data-command="<?php echo \ReceiverControl\Command\Volume\Mute::class;?>"><span class="oi oi-volume-off"></span></button>
            </div>
            <div class="col text-right">
                <button class="switchable btn btn-lg" data-zone-number="2" data-command="<?php echo \ReceiverControl\Command\Volume\Up::class;?>"><span class="oi oi-volume-high"></span></button>
            </div>
        </div>
    </div>



    <!--
    <div>
        <button id="functionUp" class="switchable" disabled="disabled">Next Function</button>
        <button id="functionDown" class="switchable" disabled="disabled">Previous Function</button>
        <button id="functionStatus" disabled="disabled"></button>
        <div id="functionHistory"></div>
    </div>
    -->
</div>
<script src="./node_modules/jquery/dist/jquery.min.js"></script>
<script src="./node_modules/jquery-ui-dist/jquery-ui.min.js"></script>
<script src="./node_modules/bootstrap/dist/js/bootstrap.js"></script>
<script src="receiver.js"></script>
</body>
</html>

