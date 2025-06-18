<?php
/*
    Omer Faruk Katkat
    For image generation.

*/

function getImageUrl($id = null, $type = 'default', $image_path = null, $size = 200) {
    $placeholder_seed = urlencode("{$type}_{$id}");
    return "https://picsum.photos/seed/{$placeholder_seed}/{$size}/{$size}";
}
?>