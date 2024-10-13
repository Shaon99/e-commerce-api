<?php
function removeFile($path)
{
    return file_exists($path) && is_file($path) ? @unlink($path) : false;
}

function filePath($folder_name)
{
    return 'public/images/' . $folder_name;
}

function imageUploadWithoutCrop($file, $folderName, $oldFile)
{
    if ($oldFile) {
        @unlink(filePath($folderName) . '/' . $oldFile);
    }
    $baseUrl = env('APP_URL');

    $fileName = $baseUrl . '/' . filePath($folderName) . '/' . uniqid() . '.' . $file->getClientOriginalExtension();
    $file->move(filePath($folderName), $fileName);

    return $fileName;
}


//cloudinary upload
function cloudUpload($image, $folder, $old)
{
    preg_replace('/\.[^.]+$/', '.', $image->getClientOriginalName()) . 'webp';

    if ($old) {
        $token = explode('/', $old);
        $token2 = explode('.', $token[sizeof($token) - 1]);
        cloudinary()->destroy('ecom/' . $folder . '/' . $token2[0]);
    }
    $response = cloudinary()->upload($image->getRealPath(), [
        'folder' => 'ecom/' . $folder,
        'transformation' => [
            'crop' => 'fill',
            'quality' => 'auto:best',
            'fetch_format' => 'auto',
        ]
    ])->getSecurePath();

    return $response;
}

// //remove image from cloud
function imageRemoveFromCloud($image, $folder)
{
    $token = explode('/', $image);
    $token2 = explode('.', $token[sizeof($token) - 1]);
    $response = cloudinary()->destroy('ecom/' . $folder . '/' . $token2[0]);
    return $response;
}
