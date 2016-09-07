<?php
include("global_config.php");
include $root_path.'/cronHelper/cron.helper.php';
if(($pid = cronHelper::lock()) !== FALSE) {

    /*
     * Cron job code goes here
    */
    $iDir = scandir($folder);
    $oDir = scandir($oFolder);
    $result =  array();
    foreach ($iDir as $key => $value)
    {
        if (!in_array($value,array(".","..")) && !in_array($value,$oDir))
        {
            $result[] = $value;
        }
    }
    if(count($result)){
        foreach($result as $key => $value){
            $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$tmpFolder.escapeshellarg($value)." ".$folder.escapeshellarg($value);
            exec($command);
            @rename($tmpFolder.$value, $oFolder.$value);
        }
    }
    cronHelper::unlock();
}


