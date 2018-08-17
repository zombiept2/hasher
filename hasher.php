<?php
$expiration_seconds = (60 * 60 * 24 * 1); // 1 day
$expiration_seconds = (5); // 5 sec
remove_files_from_dir_older_than_x_seconds(dirname(__file__).'/tmp/', $expiration_seconds); // 1 day
$status = 200;
$message = '';
$filepath = '';
$filesize = '';
$filesize_readable = '';
$filename = '';
$md5_hash = '';
if (isset($_FILES['upload_file'])) 
{
    if (move_uploaded_file($_FILES['upload_file']['tmp_name'], "tmp/" . $_FILES['upload_file']['name']))
    {
        $filename = $_FILES['upload_file']['name'];
        $filepath = "tmp/" . $_FILES['upload_file']['name'];
        $status = 200;
        $message = "File hashed!";
        $filesize = filesize($filepath);
        $filesize_readable = ReadableFilesize($filesize);
        $md5_hash = md5_file($filepath);
        unlink($filepath);
    }
} 
else 
{
    $status = 403;
    $message = "No files uploaded...";
}
$output = array(
    'message' => $message,
    'filename' => $filename,
    'filepath' => $filepath,
    'filesize' => $filesize,
    'filesize_readable' => $filesize_readable,
    'md5_hash' => $md5_hash
);
echo _response($output, $status);
exit;
function _response($data, $status = 200) 
{
    header("HTTP/1.1 " . $status . " " . _requestStatus($status));
    return json_encode($data);
}
function _requestStatus($code) 
{
    $status = array(  
        200 => 'OK',
        403 => 'Invalid',
        404 => 'Not Found',   
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
    ); 
    return ($status[$code])?$status[$code]:$status[500]; 
}
function remove_files_from_dir_older_than_x_seconds($dir,$seconds = 3600) 
{
    $files = glob(rtrim($dir, '/')."/*");
    $now   = time();
    foreach ($files as $file) 
    {
        if (is_file($file)) 
        {
            if ($now - filemtime($file) >= $seconds) 
            {
                $ext = pathinfo($file, PATHINFO_EXTENSION); // To get extension
                $base_filename = pathinfo($file, PATHINFO_FILENAME); // File name without extension
                $filename = $base_filename . '.' . $ext;
                if ($filename != 'index.php')
                {
                    unlink($file);
                }
            }
        } 
        else 
        {
            remove_files_from_dir_older_than_x_seconds($file,$seconds);
        }
    }
}
function ReadableFilesize($bytes, $decimals = 2) 
{
    $size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}