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
if (empty($_POST['user_id'])) {
    $error_code    = 3;
    $error_message = 'user_id (POST) is missing';
}

$limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50 ? Wo_Secure($_POST['limit']) : 20);
$following_offset = (!empty($_POST['following_offset']) && is_numeric($_POST['following_offset']) && $_POST['following_offset'] > 0 ? Wo_Secure($_POST['following_offset']) : 0);
$followers_offset = (!empty($_POST['followers_offset']) && is_numeric($_POST['followers_offset']) && $_POST['followers_offset'] > 0 ? Wo_Secure($_POST['followers_offset']) : 0);
if (!empty($_POST['type'])) {
	$types = explode(",", $_POST['type']);
	$user_id = Wo_Secure($_POST['user_id']);
	$f_data = array('following' => [],'followers' => []);
	if (in_array('following', $types)) {
		$following = Wo_GetFollowing($user_id, 'profile', $limit,$following_offset);
		foreach ($following as $key2 => $user_list) {

			$lastseen = ($user_list['lastseen'] > (time() - 60)) ? 'on' : 'off';
            $following[$key2] = $user_list;
            $following[$key2]['lastseen_unix_time'] = $user_list['lastseen'];
            $following[$key2]['lastseen_time_text'] = Wo_Time_Elapsed_String($user_list['lastseen']);
            $following[$key2]['lastseen'] = $lastseen;
            $following[$key2]['user_platform'] = Wo_GetPlatformFromUser_ID($user_list['user_id']);
            $following[$key2]['is_following'] = (Wo_IsFollowing($user_list['user_id'],$wo['user']['user_id'])) ? 1 : 0;

			foreach ($non_allowed as $key => $value) {
	            unset($following[$key2][$value]);
	        }
		}
		
		$f_data['following'] = $following;
	}

	if (in_array('followers', $types)) {
		$following = Wo_GetFollowers($user_id, 'profile', $limit,$followers_offset);
		foreach ($following as $key2 => $user_list) {

			$lastseen = ($user_list['lastseen'] > (time() - 60)) ? 'on' : 'off';
            $following[$key2] = $user_list;
            $following[$key2]['lastseen_unix_time'] = $user_list['lastseen'];
            $following[$key2]['lastseen_time_text'] = Wo_Time_Elapsed_String($user_list['lastseen']);
            $following[$key2]['lastseen'] = $lastseen;
            $following[$key2]['user_platform'] = Wo_GetPlatformFromUser_ID($user_list['user_id']);
            $following[$key2]['is_following'] = (Wo_IsFollowing($user_list['user_id'],$wo['user']['user_id'])) ? 1 : 0;

			foreach ($non_allowed as $key => $value) {
	            unset($following[$key2][$value]);
	        }
		}
		
		$f_data['followers'] = $following;
	}
	$response_data = array(
			    'api_status' => 200,
			    'data' => $f_data
			);

}
else{
	$error_code    = 4;
    $error_message = 'type can not be empty';
}