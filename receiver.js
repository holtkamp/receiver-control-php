let volumeChangedHandler = function (result) {
    if (result.valid) {
        //var units = "dBi";
        let units = "";
        let volumeLabelSelector = "#volumeLabelZoneNumber" + result.zoneNumber;
        $(volumeLabelSelector).html(result.message + units);

        let volumeSliderSelector = "#volumeSliderZoneNumber" + result.zoneNumber;
        $(volumeSliderSelector).val(result.message);
    }
};

let asynchronousRequest = function (data, callback) {
    $.ajax({
        url: "/",
        method: "POST",
        async: true,
        data: data,
        dataType: "json",
        success: function (result) {
            console.log("Success: ", result);

            if (callback === undefined) {
                console.log("no direct callback provided");
                return;
            }

            console.log("Invoking callback on result: ", callback);

            callback(result);
        },
        error: function (xhr, status, thrown) {
            console.log(xhr);
            console.log(status);
            console.log(thrown);
        }
    });
};


$(document).ready(function () {
    initializeVolumeSliders();
    initializeButtonEventHandling();
    initializeSliderEventHandling();
    initializeSelectEventHandling();
});

function initializeButtonEventHandling() {
    $('button[data-command-on-click]').click(function () {
        console.log('Clicked button', $(this), 'with data', $(this).data());
        let callbackFunction = eval($(this).data().callback);
        asynchronousRequest($(this).data(), callbackFunction);
    });
}

function initializeSliderEventHandling() {
    $('input[data-command-on-change]').change(function () {
        $(this).data('volume', parseFloat($(this).val()));
        console.log('Changed input', $(this), 'with value', $(this).val(), 'data', $(this).data());

        let callbackFunction = eval($(this).data().callback);
        asynchronousRequest($(this).data(), callbackFunction);
    });
}

function initializeSelectEventHandling() {
    $('input[data-command-on-select]').click(function () {
        $(this).data('option', parseFloat($(this).val()));
        console.log('Changed input', $(this), 'with value', $(this).val(), 'data', $(this).data());

        let callbackFunction = eval($(this).data().callback);
        asynchronousRequest($(this).data(), callbackFunction);
    });
}

function initializeVolumeSliders() {
    initializeVolumeSlider(1);
    initializeVolumeSlider(2);
}

function initializeVolumeSlider(zoneNumber) {
    asynchronousRequest(
        {
            command: 'ReceiverControl\\Command\\Volume\\Get',
            zoneNumber: zoneNumber,
        },
        volumeChangedHandler
    );

}