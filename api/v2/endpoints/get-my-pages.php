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
$offset = (!empty($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0 ? Wo_Secure($_POST['offset']) : 0);
$limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50 ? Wo_Secure($_POST['limit']) : 20);

$required_fields =  array(
                        'my_pages',
                        'liked_pages'
                    );
if (!empty($_POST['type']) && in_array($_POST['type'], $required_fields)) {
	if ($_POST['type'] == 'my_pages') {
		$pages = Wo_GetMyPagesAPI($limit,$offset);
		foreach ($pages as $key => $page) {
		    $pages[$key]['likes'] = Wo_CountPageLikes($page['page_id']);
		}
		$response_data = array(
		                        'api_status' => 200,
		                        'data' => $pages
		                    );
	}
	elseif ($_POST['type'] == 'liked_pages') {
		if (!empty($_POST['user_id'])) {
			$user_id = Wo_Secure($_POST['user_id']);
			$user = Wo_UserData($user_id);
			if (!empty($user)) {
				$pages = Wo_GetLikes($user_id,'profile',$limit,$offset);
				foreach ($pages as $key => $page) {
				    $pages[$key]['likes'] = Wo_CountPageLikes($page['page_id']);
				}
				$response_data = array(
		                        'api_status' => 200,
		                        'data' => $pages
		                    );
			}
			else{
				$error_code    = 5;
			    $error_message = 'user not found';
			}
		}
		else{
			$error_code    = 4;
		    $error_message = 'user_id (POST) is missing';
		}
	}
}
else{
    $error_code    = 4;
    $error_message = 'type can not be empty';
}


