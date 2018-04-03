<div class="jumbotron">
    <div class="row">
        <div class="col">
            <button class="switchable btn btn-danger" data-command-on-click="<?php echo \ReceiverControl\Command\Power\Off::class; ?>"><span class="oi oi-power-standby"></span> Off</button>
        </div>
        <div class="col text-center ">
            <h1>1<sup>st</sup></h1>
        </div>
        <div class="col text-right">
            <button class="unSwitchable btn btn-success" data-command-on-click="<?php echo \ReceiverControl\Command\Power\On::class; ?>"><span class="oi oi-power-standby"></span> On</button>
        </div>
    </div>

    <br/>

    <div class="row">
        <div class="col">
            <label id="volumeLabelZoneNumber1" for="volumeSliderZoneNumber1">Volume</label>
            <input id="volumeSliderZoneNumber1" type="range" step="0.5" min="0" max="50" data-zone-number="1" data-command-on-change="<?php echo \ReceiverControl\Command\Volume\Set::class; ?>" onchange="$('#volumeLabelZoneNumber1').html(this.value);"/>
        </div>
    </div>

    <br/>

    <div class="row">
        <div class="col">
            <button class="switchable btn btn-lg btn-outline-dark" data-command-on-click="<?php echo \ReceiverControl\Command\Volume\Down::class; ?>" data-callback="volumeChangedHandler"><span class="oi oi-volume-low"></span></button>
        </div>
        <div class="col text-center">
            <button class="switchable btn btn-lg btn-outline-dark" data-command-on-click="<?php echo \ReceiverControl\Command\Volume\Mute::class; ?>" data-callback="volumeChangedHandler"><span class="oi oi-volume-off"></span></button>
        </div>
        <div class="col text-right">
            <button class="switchable btn btn-lg btn-outline-dark" data-command-on-click="<?php echo \ReceiverControl\Command\Volume\Up::class; ?>" data-callback="volumeChangedHandler"><span class="oi oi-volume-high"></span></button>
        </div>
    </div>
</div>
