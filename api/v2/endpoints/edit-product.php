<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2018 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
$response_data = array(
    'api_status' => 400
);

$required_fields = array(
    'product_id',
    'product_title',
    'product_category',
    'product_description',
    'product_price',
    'product_location'
);

foreach ($required_fields as $key => $value) {
    if (empty($_POST[$value]) && empty($error_code)) {
        $error_code    = 3;
        $error_message = $value . ' (POST) is missing';
    }
}
if (empty(Wo_GetPostIDFromProdcutID($_POST['product_id'])) && empty($error_message)) {
    $error_code    = 4;
    $error_message = 'Product not found';
}

if (empty($error_code)) {
    $product_title       = Wo_Secure($_POST['product_title']);
    $product_category    = Wo_Secure($_POST['product_category']);
    $product_description = Wo_Secure($_POST['product_description']);
    $product_location    = Wo_Secure($_POST['product_location']);
    $product_price       = Wo_Secure($_POST['product_price']);
    $lat       = (!empty($_POST['lat'])) ? Wo_Secure($_POST['lat']) : 0;
    $lng       = (!empty($_POST['lng'])) ? Wo_Secure($_POST['lng']) : 0;
    $product_type        = (!empty($_POST['product_type'])) ? 1 : 0;
    
    if ($product_price == '0.00') {
        $error_code    = 4;
        $error_message = 'Please choose a price for your product';
    } else if (!is_numeric($product_price)) {
        $error_code    = 5;
        $error_message = 'Please choose a correct value for your price';
    }
    
    if (isset($_FILES['images']['name'])) {
        $allowed = array(
            'gif',
            'png',
            'jpg',
            'jpeg'
        );
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            $new_string = pathinfo($_FILES['images']['name']);
            if (!in_array(strtolower($new_string['extension']), $allowed)) {
                $error_code    = 6;
                $error_message = 'Image format is not supported, (jpg, png, gif, jpeg) are supported';
            }
        }
    }
    
    if (empty($error_code)) {
        $product_data_array = array(
            'name' => $product_title,
            'category' => $product_category,
            'description' => $product_description,
            'price' => $product_price,
            'location' => $product_location,
            'type' => $product_type,
            'lat' => $lat,
            'lng' => $lng
        );
        $product_data       = Wo_UpdateProductData($_POST['product_id'], $product_data_array);
        $product_id         = $_POST['product_id'];
        $id = Wo_GetPostIDFromProdcutID($product_id);
        if (count($_FILES['postPhotos']) > 0 && !empty($id) && $id > 0) {
            $fileInfo = array(
                'file' => $_FILES["postPhotos"]["tmp_name"],
                'name' => $_FILES['postPhotos']['name'],
                'size' => $_FILES["postPhotos"]["size"],
                'type' => $_FILES["postPhotos"]["type"],
                'types' => 'jpg,png,jpeg,gif'
            );
            $file     = Wo_ShareFile($fileInfo, 1);
            if (!empty($file)) {
                $media_album = Wo_RegisterProductMedia($product_id, $file['filename']);
            }
        }








        // if (isset($_FILES['postPhotos']['name'])) {
        //     if (count($_FILES['postPhotos']['name']) > 0 && !empty($id) && $id > 0) {
        //         for ($i = 0; $i < count($_FILES['postPhotos']['name']); $i++) {
        //             $fileInfo = array(
        //                 'file' => $_FILES["postPhotos"]["tmp_name"][$i],
        //                 'name' => $_FILES['postPhotos']['name'][$i],
        //                 'size' => $_FILES["postPhotos"]["size"][$i],
        //                 'type' => $_FILES["postPhotos"]["type"][$i],
        //                 'types' => 'jpg,png,jpeg,gif'
        //             );
        //             $file     = Wo_ShareFile($fileInfo, 1);
        //             if (!empty($file)) {
        //                 $media_album = Wo_RegisterProductMedia($product_id, $file['filename']);
        //             }
        //         }
        //     }
        // }
        $response_data = array(
            'api_status' => 200,
            'message' => "Product successfully edited."
        );
    }
}