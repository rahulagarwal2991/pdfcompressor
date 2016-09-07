<?php
include("global_config.php");
$iDir = scandir($folder);
$oDir = scandir($oFolder);
$tDir = scandir($tmpFolder);

if(count($iDir)){
    foreach ($iDir as $key => $value)
    {
        @unlink($folder.$value);
    }
}
if(count($oDir)){
    foreach ($oDir as $key => $value)
    {
        @unlink($oFolder.$value);
    }
}
if(count($tDir)){
    foreach ($tDir as $key => $value)
    {
        @unlink($tmpFolder.$value);
    }
}
?>
