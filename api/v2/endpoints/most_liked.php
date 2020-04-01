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

$dt = (!empty($_POST['dt']) ? Wo_Secure($_POST['dt']) : 0);
$lasttotal = (!empty($_POST['lasttotal']) ? Wo_Secure($_POST['lasttotal']) : 0);
$after_post_id = (!empty($_POST['after_post_id']) ? Wo_Secure($_POST['after_post_id']) : 0);
$limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50 ? Wo_Secure($_POST['limit']) : 0);


$posts = array();
$posts = Wo_GetPosts(array('filter_by' => 'most_liked','publisher_id' => 0 , 'dt' => $dt, 'lasttotal' => $lasttotal , 'after_post_id' => $after_post_id , 'limit' => $limit));
foreach ($posts as $key => $value) {
	foreach ($non_allowed as $key2 => $value2) {
       unset($posts[$key]['publisher'][$value2]);
    }
    foreach ($value['get_post_comments'] as $key3 => $comment) {
        foreach ($non_allowed as $key4 => $value4) {
          unset($posts[$key]['get_post_comments'][$key3]['publisher'][$value4]);
        }
    }
}
$response_data = array(
                    'api_status' => 200,
                    'data'         => $posts
                );