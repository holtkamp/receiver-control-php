var powerChange = function (result) {
    var powerStatus = $('#powerStatus');
    var switchable = $('.switchable');
    var unSwitchable = $('.unSwitchable');
    var volumeSlider = $('#volumeSlider');
    powerStatus.removeClass('powerBlack');
    if (result.valid) {
        if (result.message === 'PWON') {
            powerStatus.addClass('powerGreen').removeClass('powerRed powerYellow');
            switchable.prop('disabled', false);
            unSwitchable.prop('disabled', true);
            volumeSlider.slider("enable");
        }
        if (result.message === 'PWSTANDBY') {
            powerStatus.addClass('powerRed').removeClass('powerGreen powerYellow');
            switchable.prop('disabled', true);
            unSwitchable.prop('disabled', false);
            volumeSlider.slider("disable");
        }
    } else {
        powerStatus.addClass('powerYellow').removeClass('powerGreen powerRed');
        switchable.prop('disabled', true);
        unSwitchable.prop('disabled', true);
        volumeSlider.slider("disable");
    }
};

var volumeChange = function (result) {
    if (result.valid) {
        //var units = "dBi";
        var units = "";
        if (result.message === 'Mute') {
            units = '';
            $("#custom-handle").text(result.message);
        } else {
            $('#volumeSlider').slider("value", result.message);
        }
        $('#volumeStatus').html(result.message + units);
    }
};

var functionChange = function (result) {
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
            callback(result);
        },
        error: function (xhr, status, thrown) {
            console.log(xhr);
            console.log(status);
            console.log(thrown);
        }
    });
};


var sliderSetup = function () {
    var handle = $("#custom-handle");
    var updateHandleText = function (ui) {
        handle.text(ui.value);
    };
    $('#volumeSlider').slider({
        max: 50.0,
        min: 0.0,
        orientation: "horizontal",
        step: 0.5,
        value: 30.0,
        //disabled: true,
        create: function () {
            handle.text($(this).slider("value"));
        },
        change: function (event, ui) {
            updateHandleText(ui);
        },
        slide: function (event, ui) {
            updateHandleText(ui);
        },
        stop: function (event, ui) {
            console.log(event, ui);
            commandAjax('volumeSet', ui.value, volumeChange)
        }
    });
};

$(document).ready(function () {
    sliderSetup();
    //commandAjax('powerStatus', '', powerChange);
    commandAjax('volumeStatus', [], volumeChange); //Fetch the "current" volume setting
    //commandAjax('functionStatus', '', functionChange); //Not supported on a Denon?

    $('button[data-command]').click(function () {
        console.log('Clicked button', $(this), 'with data', $(this).data());
        commandAjaxRaw($(this).data(), powerChange);
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