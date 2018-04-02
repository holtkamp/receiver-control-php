var powerChange = function (result) {
    var powerStatus = $('#powerStatus');
    var switchable = $('.switchable');
    var unSwitchable = $('.unSwitchable');
    powerStatus.removeClass('powerBlack');
    if (result.valid) {
        if (result.message === 'PWON') {
            powerStatus.addClass('powerGreen').removeClass('powerRed powerYellow');
            switchable.prop('disabled', false);
            unSwitchable.prop('disabled', true);
        }
        if (result.message === 'PWSTANDBY') {
            powerStatus.addClass('powerRed').removeClass('powerGreen powerYellow');
            switchable.prop('disabled', true);
            unSwitchable.prop('disabled', false);
        }
    } else {
        powerStatus.addClass('powerYellow').removeClass('powerGreen powerRed');
        switchable.prop('disabled', true);
        unSwitchable.prop('disabled', true);
    }
};

let volumeChange = function (result) {
    if (result.valid) {
        //var units = "dBi";
        let units = "";
        let volumeLabelSelector = "#volumeLabelZoneNumber" + result.zoneNumber;
        $(volumeLabelSelector).html(result.message + units);

        let volumeSliderSelector = "#volumeSliderZoneNumber" + result.zoneNumber;
        $(volumeSliderSelector).val(result.message);
    }
};

let functionChange = function (result) {
    if (result.valid) {
        $('#functionStatus').html(result.message);
        if (!($("button[id='" + result.message.trim() + "']").length)) {
            $('#functionHistory').append(makeFunctionButton(result.message.trim()));
        }
    }
};

var makeFunctionButton = function (message) {
    var parts = message.split('-');
    var div = document.createElement('div');
    var button = document.createElement('button');
    button.setAttribute('id', message);
    button.setAttribute('value', parts[0].trim());
    button.classList.add('switchable', 'functionSetButton');
    button.innerHTML = message;
    if ($('.switchable').prop('disabled')) {
        button.setAttribute('disabled', 'disabled');
    }
    div.appendChild(button);

    return div;
};

var commandAjax = function (command, data, callback) {
    console.log(commandAjax, data);
    if (typeof data === 'undefined') {
        data = [];
    }
    $.ajax({
        url: "/controller.php",
        method: 'POST',
        async: true,
        data: {
            command: command,
            data: data
        },
        dataType: "json",
        success: function (result) {
            console.log("Success: ", command, result);
            callback(result);
        },
        error: function (xhr, status, thrown) {
            console.log(xhr);
            console.log(status);
            console.log(thrown);
        }
    });
};

var commandAjaxRaw = function (data, callback) {
    $.ajax({
        url: "/controller.php",
        method: 'POST',
        async: true,
        data: data,
        dataType: "json",
        success: function (result) {
            console.log("Success: ", result);

            if(callback === undefined){
                console.log("no callback provided");
            }
            else{
                console.log("Invoking callback on result: ", callback);
                callback(result);
            }

        },
        error: function (xhr, status, thrown) {
            console.log(xhr);
            console.log(status);
            console.log(thrown);
        }
    });
};


$(document).ready(function () {
    var data = {
        command: 'ReceiverControl\\Command\\Volume\\Get',
        zoneNumber: 1,
        callback: 'volumeChange'
    };
    commandAjaxRaw(data, volumeChange); //Fetch the "current" volume setting for zone 1
    //commandAjax('functionStatus', '', functionChange); //Not supported on a Denon?

    $('button[data-command-on-click]').click(function () {
        console.log('Clicked button', $(this), 'with data', $(this).data());
        let callbackFunction = eval($(this).data().callback);
        commandAjaxRaw($(this).data(), callbackFunction);
    });
    $('input[data-command-on-change]').change(function () {
        $(this).data('volume', parseFloat($(this).val()));
        console.log('Changed input', $(this), 'with value', $(this).val(), 'data', $(this).data());

        let callbackFunction = eval($(this).data().callback);
        commandAjaxRaw($(this).data(), callbackFunction);
    });

    $('#functionUp').click(function () {
        commandAjax('functionUp', '', functionChange);
    });

    $('#functionDown').click(function () {
        commandAjax('functionDown', '', functionChange);
    });

    $('#functionHistory').on("click", "button.functionSetButton", function () {
        commandAjax('functionSet', this.value, functionChange);
    });

});