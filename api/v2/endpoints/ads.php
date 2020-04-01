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
$ad_media_types = array(
    'video/mp4',
    'video/mov',
    'video/mpeg',
    'video/flv',
    'video/avi',
    'video/webm',
    'video/quicktime',
    'image/png',
    'image/jpeg',
    'image/gif'
);
$required_fields =  array(
                        'create',
                        'delete',
                        'edit',
                        'fetch_ads',
                        'fetch_ad_by_id'
                    );

$limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50 ? Wo_Secure($_POST['limit']) : 20);
$offset = (!empty($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0 ? Wo_Secure($_POST['offset']) : 0);

if (!empty($_POST['type']) && in_array($_POST['type'], $required_fields)) {

    if ($_POST['type'] == 'create') {

        $_POST['audience-list'] = explode(',', $_POST['audience-list']);
        $request   = array();
        $request[] = (empty($_POST['name']) || empty($_POST['website']));
        $request[] = (empty($_POST['headline']) || empty($_POST['description']));
        $request[] = (empty($_POST['audience-list']) || empty($_POST['gender']));
        $request[] = (empty($_POST['bidding']) || empty($_FILES['media']));
        $request[] = (empty($_POST['appears']));
        $request[] = ($wo['user']['wallet'] == 0 || $wo['user']['wallet'] == '0.00');
        if (in_array(true, $request)) {
            $error_code    = 5;
            $error_message = 'Please check your details.';
        } else {
            if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 100) {
                $error_code    = 6;
                $error_message = 'Please enter a valid company name!';
            } else if (!filter_var($_POST['website'], FILTER_VALIDATE_URL) || $_POST['website'] > 3000) {
                $error_code    = 7;
                $error_message = 'Please enter a valid link!';
            } else if (strlen($_POST['headline']) < 5 || strlen($_POST['headline']) > 200) {
                $error_code    = 7;
                $error_message = 'Please enter a valid title';
            }
            if (!in_array($_FILES["media"]["type"], $ad_media_types)) {
                $error_code    = 8;
                $error_message = 'Media file is invalid. Please select a valid image or video';
            } else if (gettype($_POST['audience-list']) != 'array' || count($_POST['audience-list']) < 1) {
                $error_code    = 5;
                $error_message = 'Please check your details.';
            } else if ($_POST['bidding'] != 'clicks' && $_POST['bidding'] != 'views') {
                $error_code    = 5;
                $error_message = 'Please check your details.';
            } else if (!in_array($_POST['appears'], array(
                    'post',
                    'sidebar',
                    'video'
                ))) {
                $error = $error_icon . $wo['lang']['please_check_details'];
            } else if (in_array($_POST['appears'], array(
                    'post',
                    'sidebar'
                ))) {
                $img_types = array(
                    'image/png',
                    'image/jpeg',
                    'image/gif'
                );
                if (!in_array($_FILES["media"]["type"], $img_types)) {
                    $error_code    = 9;
                    $error_message = 'Media file is invalid. Please select a valid image';
                }
            } else if (in_array($_POST['appears'], array(
                    'video'
                ))) {
                $img_types = array(
                    'video/mp4',
                    'video/mov',
                    'video/avi'
                );
                if (!in_array($_FILES["media"]["type"], $img_types)) {
                    $error_code    = 10;
                    $error_message = 'Media file is invalid. Please select a valid video';
                }
            } else if ($_FILES["media"]["size"] > $wo['config']['maxUpload'] || true) {
                $maxUpload = Wo_SizeUnits($wo['config']['maxUpload']);
                $error_code    = 11;
                $error_message = str_replace('{file_size}', $maxUpload, "File size error: The file exceeds allowed the limit ({file_size}) and can not be uploaded.");
            }
        }
        if (empty($error_message)) {
            $registration_data             = array(
                'name' => Wo_Secure($_POST['name']),
                'url' => Wo_Secure($_POST['website']),
                'headline' => Wo_Secure($_POST['headline']),
                'description' => Wo_Secure($_POST['description']),
                'location' => Wo_Secure($_POST['location']),
                'audience' => Wo_Secure(implode(',', $_POST['audience-list'])),
                'gender' => Wo_Secure($_POST['gender']),
                'bidding' => Wo_Secure($_POST['bidding']),
                'posted' => time(),
                'appears' => Wo_Secure($_POST['appears']),
                'user_id' => Wo_Secure($wo['user']['user_id'])
            );
            $fileInfo                      = array(
                'file' => $_FILES["media"]["tmp_name"],
                'name' => $_FILES['media']['name'],
                'size' => $_FILES["media"]["size"],
                'type' => $_FILES["media"]["type"],
                'types' => 'jpg,png,bmp,gif,mp4,avi,mov',
                'compress' => false
            );
            $media                         = Wo_ShareFile($fileInfo);
            $registration_data['ad_media'] = $media['filename'];
            $last_id                       = $db->insert(T_USER_ADS, $registration_data);
            $get_ad_data = Wo_GetUserAdData($last_id);
            foreach ($non_allowed as $key4 => $value4) {
              unset($get_ad_data['user_data'][$value4]);
            }
            $response_data = array(
                                'api_status' => 200,
                                'data' => $get_ad_data
                            );
        }
    }
    if ($_POST['type'] == 'edit') {

        $_POST['audience-list'] = explode(',', $_POST['audience-list']);
        $request   = array();
        $request[] = (empty($_POST['ad_id']) || !is_numeric($_POST['ad_id']));
        $request[] = (empty($_POST['name']) || empty($_POST['website']));
        $request[] = (empty($_POST['headline']) || empty($_POST['description']));
        $request[] = ($_POST['ad_id'] < 1 || empty($_POST['gender']));
        $request[] = (empty($_POST['bidding']) || empty($_POST['location']));
        $request[] = (empty($_POST['audience-list']) || !is_array($_POST['audience-list']));
        if (in_array(true, $request)) {
            $error_code    = 5;
            $error_message = 'Please check your details.';
        } else {
            if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 100) {
                $error_code    = 6;
                $error_message = 'Please enter a valid company name!';
            } else if (!filter_var($_POST['website'], FILTER_VALIDATE_URL) || $_POST['website'] > 3000) {
                $error_code    = 7;
                $error_message = 'Please enter a valid link!';
            } else if (strlen($_POST['headline']) < 5 || strlen($_POST['headline']) > 200) {
                $error_code    = 7;
                $error_message = 'Please enter a valid title';
            }
            if (!in_array($_POST['bidding'], array(
                'clicks',
                'views'
            ))) {
                $error_code    = 5;
                $error_message = 'Please check your details.';
            }
            $img_types = array(
                'image/png',
                'image/jpeg',
                'image/gif',
                'image/jpg'
            );
            $video_types = array(
                    'video/mp4',
                    'video/mov',
                    'video/avi'
                );
            if (!empty($_FILES["media"]) && (!in_array($_FILES["media"]["type"], $img_types) && !in_array($_FILES["media"]["type"], $video_types)) ) {
                $error_code    = 9;
                $error_message = 'Media file is invalid. Please select a valid image or video';
            }
            if (!empty($_FILES["media"]) && $_FILES["media"]["size"] > $wo['config']['maxUpload']) {
                $maxUpload = Wo_SizeUnits($wo['config']['maxUpload']);
                $error_code    = 11;
                $error_message = str_replace('{file_size}', $maxUpload, "File size error: The file exceeds allowed the limit ({file_size}) and can not be uploaded.");
            }
        }
        if (empty($error_message)) {
            $adid        = Wo_Secure($_POST['ad_id']);
            $get_ad_data = Wo_GetUserAdData($adid);
            if (!empty($get_ad_data)) {

                $update_data = array(
                    'name' => Wo_Secure($_POST['name']),
                    'url' => Wo_Secure($_POST['website']),
                    'headline' => Wo_Secure($_POST['headline']),
                    'description' => Wo_Secure($_POST['description']),
                    'location' => Wo_Secure($_POST['location']),
                    'audience' => Wo_Secure(implode(',', $_POST['audience-list'])),
                    'gender' => Wo_Secure($_POST['gender']),
                    'bidding' => Wo_Secure($_POST['bidding']),
                    'posted' => time()
                );
                

                if (!empty($_FILES["media"])) {
                    $fileInfo                      = array(
                        'file' => $_FILES["media"]["tmp_name"],
                        'name' => $_FILES['media']['name'],
                        'size' => $_FILES["media"]["size"],
                        'type' => $_FILES["media"]["type"],
                        'types' => 'jpg,png,bmp,gif,mp4,avi,mov',
                        'compress' => false
                    );
                    $media                         = Wo_ShareFile($fileInfo);
                    if (!empty($media['filename'])) {
                        $update_data['ad_media'] = $media['filename'];
                        $user_ad = $db->where('id',$adid)->getOne(T_USER_ADS);
                        if (!empty($user_ad->ad_media)) {
                            @unlink($user_ad->ad_media);
                        }
                    }
                }

                
                $user_id     = $wo['user']['id'];
                $db->where("id", $adid)->where("user_id", $user_id)->update(T_USER_ADS, $update_data);
                $response_data = array(
                                    'api_status' => 200,
                                    'message' => "Your Ad successfully edited."
                                );
            }
            else{
                $error_code    = 12;
                $error_message = 'Ad not found';
            }
        }
    }

    if ($_POST['type'] == 'delete') {
        if (empty($_POST['ad_id']) || !is_numeric($_POST['ad_id']) || $_POST['ad_id'] < 1) {
            $error_code    = 5;
            $error_message = 'ad_id can not be empty.';
        }
        else{

            $ad_id = Wo_Secure($_POST['ad_id']);
            $get_ad_data = Wo_GetUserAdData($ad_id);
            if (!empty($get_ad_data)) {
                Wo_DeleteUserAd($ad_id);
                $response_data = array(
                                    'api_status' => 200,
                                    'message' => "Your Ad successfully deleted."
                                );
            }
            else{
                $error_code    = 12;
                $error_message = 'Ad not found';
            }
        }
    }

    if ($_POST['type'] == 'fetch_ads') {
        $ads = Wo_GetMyAds(array('limit' => $limit,
                                 'offset' => $offset));
        if (!empty($ads)) {
            foreach ($ads as $key => $value) {
                foreach ($non_allowed as $key4 => $value4) {
                  unset($ads[$key]['user_data'][$value4]);
                }
            }
        }
        else{
            $ads = array();
        }
            
        $response_data = array(
                                'api_status' => 200,
                                'data' => $ads
                            );
    }

    if ($_POST['type'] == 'fetch_ad_by_id') {

        if (empty($_POST['ad_id']) || !is_numeric($_POST['ad_id']) || $_POST['ad_id'] < 1) {
            $error_code    = 5;
            $error_message = 'ad_id can not be empty.';
        }
        else{
            $ad_id = Wo_Secure($_POST['ad_id']);
            $get_ad_data = Wo_GetUserAdData($ad_id);
            if (!empty($get_ad_data)) {
                foreach ($non_allowed as $key4 => $value4) {
                  unset($get_ad_data['user_data'][$value4]);
                }
            }
            else{
                $get_ad_data = array();
            }
            
            $response_data = array(
                                'api_status' => 200,
                                'data' => $get_ad_data
                            );
        }
    }
    
}
else{
    $error_code    = 4;
    $error_message = 'type can not be empty';
}