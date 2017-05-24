<?php
//initiate the composer autoload
require 'vendor/autoload.php';

use Commands\FunctionCommand;
use Commands\PowerCommand;
use Commands\VolumeCommand;

switch ($_POST['command']) {
    case 'powerOn':
        $model = new PowerCommand();
        $view = $model->powerOn();
        break;
    case 'powerOff':
        $model = new PowerCommand();
        $view = $model->powerOff();
        break;
    case 'powerStatus':
        $model = new PowerCommand();
        $view = $model->powerStatus();
        break;
    case 'volumeUp':
        $model = new VolumeCommand();
        $view = $model->volumeUp();
        break;
    case 'volumeDown':
        $model = new VolumeCommand();
        $view = $model->volumeDown();
        break;
    case 'volumeStatus':
        $model = new VolumeCommand();
        $view = $model->volumeStatus();
        break;
    case 'volumeMute':
        $model = new VolumeCommand();
        $view = $model->volumeMute();
        break;
    case 'volumeSet':
        $model = new VolumeCommand();
        $view = $model->volumeSet($_POST['data']);
        break;
    case 'functionStatus':
        $model = new FunctionCommand();
        $view = $model->functionStatus();
        break;
    case 'functionUp':
        $model = new FunctionCommand();
        $view = $model->functionUp();
        break;
    case 'functionDown':
        $model = new FunctionCommand();
        $view = $model->functionDown();
        break;
    case 'functionSet':
        $model = new FunctionCommand();
        $view = $model->functionSet($_POST['data']);
        break;
    default:
        $view = new \Commands\Response(false, 'invalid command');
        break;
}
echo $view->getJSON();