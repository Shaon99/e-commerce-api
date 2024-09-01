<?php
function removeFile($path)
{
    return file_exists($path) && is_file($path) ? @unlink($path) : false;
}

function filePath($folder_name)
{
    return 'images/' . $folder_name;
}

function imageUploadWithoutCrop($file, $folderName, $oldFile)
{
    if ($oldFile) {
        @unlink(filePath($folderName) . '/' . $oldFile);
    }
    $baseUrl = env('APP_URL');

    $fileName = $baseUrl.'/'.filePath($folderName).'/'.uniqid() . '.' . $file->getClientOriginalExtension();
    $file->move(filePath($folderName), $fileName);

    return $fileName;
}
