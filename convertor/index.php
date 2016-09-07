<?php
include("../global_config.php");
include($root_path."/classes/upload_class.php"); //classes is the map where the class file is stored (one above the root)
include($root_path."/classes/muli_files.php");
include($root_path."/classes/functions.php");

$max_size = 1024*1024*$size_mb; // the max. size for uploading
$upload_max = parse_size(ini_get('upload_max_filesize'));
$post_max_size =parse_size(ini_get('post_max_size'));

if($upload_max <  $max_size || $post_max_size <  $max_size){
    echo "Please 'post_max_size' or 'upload_max_filesize'  upload to ".$size_mb." MB in php ini file";
    die;
}

$multi_upload = new muli_files;
$multi_upload->upload_dir = $folder; // "files" is the folder for the uploaded files (you have to create this folder)
$multi_upload->compress_dir = $oFolder;
$multi_upload->extensions = array(".pdf"); // specify the allowed extensions here
//$multi_upload->extensions = array(".png", ".pdf", ".gif", ".bmp",".jpg",".jpeg"); // specify the allowed extensions here
$multi_upload->message[] = $multi_upload->extra_text(4); // a different standard message for multiple files
//$multi_upload->rename_file = true; // set to "true" if you want to rename all files with a timestamp value
$multi_upload->do_filename_check = "n"; // check filename ...

if(isset($_POST['Submit'])) {
$multi_upload->tmp_names_array = $_FILES['upload']['tmp_name'];
$multi_upload->names_array = $_FILES['upload']['name'];
$multi_upload->error_array = $_FILES['upload']['error'];
$multi_upload->replace = "y";
$multi_upload->upload_multi_files();
}

if (isset($_POST['download'])) {
    $folder_name = $root_path."/files/".$_POST['folder_name']."/";
    $fullPath = $folder_name.$_POST['file_in_folder'];
    if ($fd = fopen ($fullPath, "rb")) {
        $fsize = filesize($fullPath);
        $path_parts = pathinfo($fullPath);
        $ext = strtolower($path_parts["extension"]);
        switch ($ext) {
            case "png":
            case "bmp":
            case "gif":
            case "jpeg":
            case "jpg":
                header("Content-type: image/".$ext."");
                header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
                break;
            case "pdf":
                header("Content-type: application/pdf");
                header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
                break;
            default;
                header("Content-type: application/octet-stream");
                header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
        }
        header("Content-length: $fsize");
        header("Cache-control: private");
        while(!feof($fd)) {
            $buffer = fread($fd, 2048);
            echo $buffer;
        }
    }
    fclose ($fd);
    exit;
}

?>
<?php include "layout/header.php";?>
<div id="main">
    <h2 style="text-align:center;margin-top:10px;">Autoportal.com</h2>
    <p align="center">File Compressor <br>(Files gets removed every midnight)</p>
    <p>Max. filesize: <b><?php echo ($max_size/1024)/1024; ?> MB</b><br>
        <?php
        $ext = "Allowed extensions are: <b>";
        foreach ($multi_upload->extensions as $val) {
            $ext .=  ltrim($val, ".").", ";
        }
        echo rtrim($ext, ", ")."</b>";
        ?>
    </p>
    <form name="form1" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_size; ?>">
        <label for="upload[]">Upload file:</label>
        <input type="file" name="upload[]" size="30" multiple="multiple" class="upload" id="file">
        <input type="hidden" name="replace" value="1">
        <input type="submit" name="Submit" value="Submit" class="submit">
    </form>
    <p><?php echo $multi_upload->show_error_string(); ?></p>
    <p>* It will override the file with same name.</p>


    <?php //echo select_files($folder); ?>
    <?php //echo select_files($oFolder); ?>
</div>


<!-- table-->
<?php echo genterateTable($folder,$oFolder); ?>

<!-- ends -->
<script>


    $(function(){
        var max_upload_size = '<?php echo $max_size?>';
        $('#file').change(function(){
            var combinedSize = 0;
            for(var i=0;i<this.files.length;i++) {
                combinedSize += (this.files[i].size||this.files[i].fileSize);
            }
            if(max_upload_size < combinedSize)
            alert("You can not upload files with size more than <?php echo ($max_size/1024)/1024;  ?> MB");
            return false;
        })
    })
<?php if(isset($_POST['Submit']) && $multi_upload->is_error == false) { ?>
   // window.location.href = window.location.href;

<?php } ?>

</script>
</body>
</html>