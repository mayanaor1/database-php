<?php

//Download an image from the specified URL and saves it to a local file.
function saveImageFromUrl($imageUrl, $imageFileName) {
    
    $imageData = file_get_contents($imageUrl);
    if ($imageData === false) {
        die('Unable to retrieve the image from ' . $imageUrl);
    }

    file_put_contents($imageFileName, $imageData);

    return $imageFileName;

}


$imageUrl = 'https://cdn2.vectorstock.com/i/1000x1000/23/81/default-avatar-profile-icon-vector-18942381.jpg';
$imageFileName = 'image.jpg';

saveImageFromUrl($imageUrl, $imageFileName);

