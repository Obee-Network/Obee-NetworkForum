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
$response_data   = array(
    'api_status' => 400
);

$required_fields = array(
    'user_id',
    'code'
);
foreach ($required_fields as $key => $value) {
    if (empty($_POST[$value]) && empty($error_code)) {
        $error_code    = 3;
        $error_message = $value . ' (POST) is missing';
    }
}
if (empty($error_code)) {
	$confirm_code = $_POST['code'];
	$user_id      = $_POST['user_id'];
	$confirm_code = $db->where('user_id', $user_id)->where('email_code', md5($confirm_code))->getValue(T_USERS, 'count(*)');
    if (empty($confirm_code)) {
    	$error_code    = 3;
        $error_message = 'Wrong confirmation code.';
    }
    else{
        $time           = time();
        $cookie         = '';
        $access_token   = sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
        $timezone       = 'UTC';
        $create_session = mysqli_query($sqlConnect, "INSERT INTO " . T_APP_SESSIONS . " (`user_id`, `session_id`, `platform`, `time`) VALUES ('{$user_id}', '{$access_token}', 'phone', '{$time}')");
        if (!empty($_POST['timezone'])) {
            $timezone = Wo_Secure($_POST['timezone']);
        }
        $add_timezone = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `timezone` = '{$timezone}' WHERE `user_id` = {$user_id}");
        // if (!empty($_POST['device_id'])) {
        //     $device_id = Wo_Secure($_POST['device_id']);
        //     $update    = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `device_id` = '{$device_id}' WHERE `user_id` = '{$user_id}'");
        // }
        if (!empty($_POST['android_m_device_id'])) {
            $device_id  = Wo_Secure($_POST['android_m_device_id']);
            $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `android_m_device_id` = '{$device_id}' WHERE `user_id` = '{$user_id}'");
        }
        if (!empty($_POST['ios_m_device_id'])) {
            $device_id  = Wo_Secure($_POST['ios_m_device_id']);
            $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `ios_m_device_id` = '{$device_id}' WHERE `user_id` = '{$user_id}'");
        }
        if (!empty($_POST['android_n_device_id'])) {
            $device_id  = Wo_Secure($_POST['android_n_device_id']);
            $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `android_n_device_id` = '{$device_id}' WHERE `user_id` = '{$user_id}'");
        }
        if (!empty($_POST['ios_n_device_id'])) {
            $device_id  = Wo_Secure($_POST['ios_n_device_id']);
            $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `ios_n_device_id` = '{$device_id}' WHERE `user_id` = '{$user_id}'");
        }
        if ($create_session) {
            $response_data = array(
                'api_status' => 200,
                'timezone' => $timezone,
                'access_token' => $access_token,
                'user_id' => $user_id,
            );
        }





    }

}