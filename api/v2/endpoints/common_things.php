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

$offset = (!empty($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0 ? Wo_Secure($_POST['offset']) : 0);
$limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50 ? Wo_Secure($_POST['limit']) : 20);



$commons = Wo_GetCommonUsers(array('limit' => $limit,'after' => $offset));
if (!empty($commons)) {
	foreach ($commons as $key => $value) {
		foreach ($non_allowed as $key4 => $value4) {
	      unset($commons[$key]['user_data'][$value4]);
	    }

	    $common = array('count' => 0, 'relationship' => 0 , 'school' => 0, 'working' => 0, 'birthday' => 0, 'country' => 0, 'city' => 0);
        if (!empty($value['user_data']['relationship_id'])) {
            if ($wo['user']['relationship_id'] == $value['user_data']['relationship_id']) {
                $common['count'] = $common['count'] + 1;
                $common['relationship'] = 1;
            }
        }

        if (!empty($value['user_data']['school'])) {
            if ($wo['user']['school'] == $value['user_data']['school']) {
                $common['count'] = $common['count'] + 1;
                $common['school'] = 1;
            }
        }

        if (!empty($value['user_data']['working'])) {
            if ($wo['user']['working'] == $value['user_data']['working']) {
                $common['count'] = $common['count'] + 1;
                $common['working'] = 1;
            }
        }

        if (!empty($value['user_data']['birthday'])) {
            if ($wo['user']['birthday'] == $value['user_data']['birthday'] && $wo['user']['birthday'] != '0000-00-00') {
                $common['count'] = $common['count'] + 1;
                $common['birthday'] = 1;
            }
        }

        if (!empty($value['user_data']['country_id'])) {
            if ($wo['user']['country_id'] == $value['user_data']['country_id']) {
                $common['count'] = $common['count'] + 1;
                $common['country'] = 1;
            }
        }

        if (!empty($value['user_data']['city'])) {
            if ($wo['user']['city'] == $value['user_data']['city']) {
                $common['count'] = $common['count'] + 1;
                $common['city'] = 1;
            }
        }

        $commons[$key]['common'] = $common;
	}
}
	

$response_data = array(
                        'api_status' => 200,
                        'data' => $commons
                    );