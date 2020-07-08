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

$required_fields =  array(
                        'post',
                        'comment',
                        'reply'
                    );

$limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50 ? Wo_Secure($_POST['limit']) : 20);

$like = (!empty($_POST['like']) && is_numeric($_POST['like']) && $_POST['like'] > 0 ? Wo_Secure($_POST['like']) : 0);
$love = (!empty($_POST['love']) && is_numeric($_POST['love']) && $_POST['love'] > 0 ? Wo_Secure($_POST['love']) : 0);
$haha = (!empty($_POST['haha']) && is_numeric($_POST['haha']) && $_POST['haha'] > 0 ? Wo_Secure($_POST['haha']) : 0);
$wow = (!empty($_POST['wow']) && is_numeric($_POST['wow']) && $_POST['wow'] > 0 ? Wo_Secure($_POST['wow']) : 0);
$sad = (!empty($_POST['sad']) && is_numeric($_POST['sad']) && $_POST['sad'] > 0 ? Wo_Secure($_POST['sad']) : 0);
$angry = (!empty($_POST['angry']) && is_numeric($_POST['angry']) && $_POST['angry'] > 0 ? Wo_Secure($_POST['angry']) : 0);

if (!empty($_POST['type']) && in_array($_POST['type'], $required_fields)) {
	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0) {

		$react_array = array('like' => 'Like','love' => 'Love' ,'haha' => 'HaHa' ,'wow' => 'WoW' ,'sad' => 'Sad' ,'angry' => 'Angry' );

		if (!empty($_POST['reaction'])) {
			$col = Wo_Secure($_POST['type']);
			if ($_POST['type'] == 'reply') {
				$col = 'replay';
			}
			$id = Wo_Secure($_POST['id']);
			$all = array();
			foreach (explode(",", $_POST['reaction']) as $i => $react) {
				if (in_array($react,array_keys($react_array))) {
					if ($react == 'like') {
						$users = Wo_GetPostReactionUsers($id, $react_array[$react] ,$limit,$like,$col);
					}
					if ($react == 'love') {
						$users = Wo_GetPostReactionUsers($id, $react_array[$react] ,$limit,$love,$col);
					}
					if ($react == 'haha') {
						$users = Wo_GetPostReactionUsers($id, $react_array[$react] ,$limit,$haha,$col);
					}
					if ($react == 'wow') {
						$users = Wo_GetPostReactionUsers($id, $react_array[$react] ,$limit,$wow,$col);
					}
					if ($react == 'sad') {
						$users = Wo_GetPostReactionUsers($id, $react_array[$react] ,$limit,$sad,$col);
					}
					if ($react == 'angry') {
						$users = Wo_GetPostReactionUsers($id, $react_array[$react] ,$limit,$angry,$col);
					}

					$all[$react] = array();
					foreach ($users as $key1 => $value) {
						foreach ($non_allowed as $key => $value2) {
					       unset($users[$key][$value2]);
					    }
					    $users[$key1]['lastseen_time_text'] = Wo_Time_Elapsed_String($users[$key1]['lastseen']);
					    $all[$react] = $users;
					}
				}
			}
				
			$response_data = array(
			    'api_status' => 200,
			    'data' => $all
			);
		}
		else{
			$error_code    = 6;
	        $error_message = 'reaction can not be empty';
		}
	}
	else{
		$error_code    = 5;
        $error_message = 'id must be numeric and greater than 0';
	}
}
else{
	$error_code    = 4;
    $error_message = 'type can not be empty';
}

 ?>