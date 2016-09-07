<?php
function select_files($dir) {
    // removed in ver 1.01 the globals
    $teller = 0;
    if ($handle = opendir($dir)) {
        $mydir = "<p>These are the files in the directory:</p>\n";
        $mydir .= "<form name=\"form1\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
        $mydir .= "  <select name=\"file_in_folder\" multiple='multiple'>\n";
        $mydir .= "    <option value=\"\" selected>...\n";
        while (false !== ($file = readdir($handle))) {
            $files[] = $file;
        }
        closedir($handle);
        sort($files);
        foreach ($files as $val) {
            if (is_file($dir.$val)) { // show only real files (ver. 1.01)
                $mydir .= "    <option value=\"".$val."\">";
                $mydir .= (strlen($val) > 30) ? substr($val, 0, 30)."...\n" : $val."\n";
                $teller++;
            }
        }
        $mydir .= "  </select>";
        $mydir .= "<input type=\"submit\" name=\"download\" value=\"Download\">";
        $mydir .= "</form>\n";
    }
    if ($teller == 0) {
        echo "No files!";
    } else {
        echo $mydir;
    }
}

function del_file($file) {
    $delete = @unlink($file);
    clearstatcache();
    if (@file_exists($file)) {
        $filesys = str_replace("/","\\",$file);
        $delete = @system("del $filesys");
        clearstatcache();
        if (@file_exists($file)) {
            $delete = @chmod ($file, 0775);
            $delete = @unlink($file);
            $delete = @system("del $filesys");
        }
    }
}
function get_oldest_file($directory) {
    if ($handle = opendir($directory)) {
        while (false !== ($file = readdir($handle))) {
            if (is_file($directory.$file)) { // add only files to the array (ver. 1.01)
                $files[] = $file;
            }
        }
        if (count($files) <= 12) {
            return;
        } else {
            foreach ($files as $val) {
                if (is_file($directory.$val)) {
                    $file_date[$val] = filemtime($directory.$val);
                }
            }
        }
    }
    closedir($handle);
    asort($file_date, SORT_NUMERIC);
    reset($file_date);
    $oldest = key($file_date);
    return $oldest;
}
function parse_size($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    }
    else {
        return round($size);
    }
}

function genterateTable($dir,$opdir)
{
    $i=0;
    $content = '';
    $content .= '    <div class="Table">
            <div class="Title">
                <p>Files List</p>
            </div>
            <div class="Heading">
                <div class="Cell">
                    <p>Name</p>
                </div>
                <div class="Cell">
                    <p>Actual File Details</p>
                </div>
                <div class="Cell">
                    <p>Compressed File Details</p>
                </div>
            </div>';

    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if (is_file($dir . $file)) {$i++;
                $content .= "<div class=\"Row\">";
                    $content .= "<div class=\"Cell\"><p>" . $file . "</p></div>";

                    $content .= "<div class=\"Cell\"><span>".returnFileSize($dir . $file)."</span>";
                    $content .= "<form name=\"form1\" method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\" class='download_form'>\n";
                    $content .= "<input type=\"hidden\" name=\"file_in_folder\" value=\"".$file."\">";
                    $content .= "<input type=\"hidden\" name=\"folder_name\" value='input'>";
                    $content .= "<input type=\"submit\" name=\"download\" value=\"Download\">";
                    $content .= "</form>\n";
                    $content .= "</div>";

                    $content .= "<div class=\"Cell\">";
                    if (is_file($opdir . $file)) {
                        $content .= "<span>".returnFileSize($opdir . $file)."</span>";
                        $content .= "<form name=\"form1\" method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\" class='download_form'>\n";
                        $content .= "<input type=\"hidden\" name=\"file_in_folder\" value=\"".$file."\">";
                        $content .= "<input type=\"hidden\" name=\"folder_name\" value='output'>";
                        $content .= "<input type=\"submit\" name=\"download\" value=\"Download\">";
                        $content .= "</form>\n";

                    }else{
                        $content .= "Under processing";
                    }
                    $content .= "</div>";
                $content .= "</div>";
            }
        }
        closedir($handle);

    }
    if($i == 0)
    $content .= "<div class=\"Row\"><p>No Files Found</p></div>";
    $content .= '</div>';
    echo $content;
}
function returnFileSize($filename){
    $fileSize = filesize($filename);
    if($fileSize < 1024){
        $size = $fileSize;
        $ext = "B";
    }elseif( ($fileSize/1024) < 1024){
        $size = ($fileSize/1024);
        $ext = "KB";
    }else{
        $size = ($fileSize/1024)/1024;
        $ext = "MB";
    }

    $size = round($size,2);
    return $size." ".$ext;
}
?>