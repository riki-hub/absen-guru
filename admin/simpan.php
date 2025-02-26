<?php
$croppedImageData = $_POST['cropped_image_data'];
if (!empty($croppedImageData)) {
    $folderPath = "../upload/";
    $image_parts = explode(";base64,", $croppedImageData);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $fileName = uniqid() . '.png';
    $file = $folderPath . $fileName;

    file_put_contents($file, $image_base64);
}
