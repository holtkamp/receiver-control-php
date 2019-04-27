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
    console.log("Invoking with data", data);
    $.ajax({
        url: "/" + data.command ? data.command : '',
        method: "POST",
        async: true,
        //Required because the default "application/x-www-form-urlencoded; charset=UTF-8" is not detected by Slim ServerRequestFactory
        //@link https://github.com/slimphp/Slim-Psr7/issues/38
        contentType: "application/x-www-form-urlencoded",
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
    initializeCommandHandling();
});

function initializeCommandHandling() {
    $('[data-command]')
        .click(function () {
            console.log('Clicked button', $(this), 'with data', $(this).data());
            let callbackFunction = eval($(this).data().callback);
            asynchronousRequest($(this).data(), callbackFunction);
        })
        .change(function () {
            console.log('Changed input', $(this), 'with value', $(this).val(), 'data', $(this).data());
            $(this).data('volume', parseFloat($(this).val()));
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
