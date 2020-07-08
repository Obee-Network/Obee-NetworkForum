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
    'api_status' => 400,
);

$required_fields =  array(
                        'fetch',
                        'send',
                    );
if (!empty($_POST['type']) && in_array($_POST['type'], $required_fields)) {
    if ($_POST['type'] == 'fetch') {
    	$offset = (!empty($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0 ? Wo_Secure($_POST['offset']) : 0);
        $limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50 ? Wo_Secure($_POST['limit']) : 20);
    	$gifts = Wo_GetAllGifts($limit,$offset);
    	foreach ($gifts as $key => $value) {
    		$gifts[$key]['media_file'] = Wo_GetMedia($value['media_file']);
    		$gifts[$key]['time_text'] = Wo_Time_Elapsed_String($value['time']);
    	}
    	$response_data = array(
                                'api_status' => 200,
                                'data' => $gifts
                            );
    }
    else if ($_POST['type'] == 'send') {
    	if (empty($_POST['id']) && !is_numeric($_POST['id']) && $_POST['id'] < 1) {
    		$error_code    = 3;
            $error_message = 'id (POST) is missing';
    	}
    	elseif (empty($_POST['user_id']) && !is_numeric($_POST['user_id']) && $_POST['user_id'] < 1) {
    		$error_code    = 3;
            $error_message = 'user_id (POST) is missing';
    	}
    	else{
    		$id = Wo_Secure($_POST['id']);
    		$query_one = " SELECT * FROM " . T_GIFTS." WHERE `id` = ".$id;
    		$sql = mysqli_query($sqlConnect, $query_one);
    		$fetched_data = mysqli_fetch_assoc($sql);
		    if (!empty($fetched_data)) {
		        $from     = $wo['user']['id'];
		        $to    = Wo_Secure($_POST['user_id']);
		        $user_data = Wo_UserData($to);
		        if (!empty($user_data)) {
			        $gift_id  = $id;
			        $gift_img = $fetched_data['media_file'];
			        $query    = mysqli_query($sqlConnect, " INSERT INTO " . T_USERGIFTS . " (`from`,`to`,`gift_id`,`time`) VALUES ('{$from}','{$to}','{$gift_id}','" . time() . "')");
			        $user     = $wo['user'];
			        if ($query) {
			            $text                    = "";
			            $type2                   = "gift_" . $gift_id;
			            $notification_data_array = array(
			                'recipient_id' => $to,
			                'post_id' => $from,
			                'type' => 'gift',
			                'text' => $text,
			                'type2' => $type2,
			                'url' => 'index.php?link1=timeline&mode=opengift&gift_img=' . urlencode($gift_img) . '&u=' . $user['username']
			            );
			            Wo_RegisterNotification($notification_data_array);
			            $response_data = array(
	                                    'api_status' => 200,
	                                    'message' => 'Gift successfully sent'
	                                );
			        }
			        else{
			        	$error_code    = 7;
			            $error_message = 'Something Wrong';
			        }
			    }
			    else{
			    	$error_code    = 6;
		            $error_message = 'User not found';
			    }
		    }
		    else{
		    	$error_code    = 5;
	            $error_message = 'Gift not found';
		    }

    	}
    }
}
else{
    $error_code    = 4;
    $error_message = 'type can not be empty';
}
