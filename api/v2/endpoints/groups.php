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
                        'get_requests',
                        'accept_request',
                        'delete_request'
                    );
if (!empty($_POST['type']) && in_array($_POST['type'], $required_fields)) {
    if ($_POST['type'] == 'get_requests') {
        if (empty($_POST['group_id'])) {
            $error_code    = 3;
            $error_message = 'group_id (POST) is missing';
        }
        if (empty($error_code)) {
            $group_id   = Wo_Secure($_POST['group_id']);
            $group_data = Wo_GroupData($group_id);
            if (empty($group_data)) {
                $error_code    = 5;
                $error_message = 'Group not found';
            } elseif ($group_data['user_id'] != $wo['user']['id']) {
                $error_code    = 6;
                $error_message = 'You are not the group owner';
            }
             else {
                $offset = (!empty($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0 ? Wo_Secure($_POST['offset']) : 0);
                $limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50 ? Wo_Secure($_POST['limit']) : 20);
                $requests = Wo_GetGroupRequestsWithOffset(array('group_id' => $group_id,
                                                                'limit' => $limit,
                                                                'offset' => $offset));
                foreach ($requests as $key => $value) {
                    if (!empty($requests)) {
                        foreach ($non_allowed as $key4 => $value4) {
                          unset($requests[$key]['user_data'][$value4]);
                        }
                    }
                }
                $response_data = array(
                                    'api_status' => 200,
                                    'data' => $requests
                                );
            }
        }
    }
    if ($_POST['type'] == 'accept_request') {
        if (empty($_POST['group_id'])) {
            $error_code    = 3;
            $error_message = 'group_id (POST) is missing';
        } 
        elseif (empty($_POST['user_id'])) {
            $error_code    = 3;
            $error_message = 'user_id (POST) is missing';
        }
        if (empty($error_code)) {
            $user_id   = Wo_Secure($_POST['user_id']);
            $group_id   = Wo_Secure($_POST['group_id']);
            $group_data = Wo_GroupData($group_id);
            if (empty($group_data)) {
                $error_code    = 5;
                $error_message = 'Group not found';
            } elseif ($group_data['user_id'] != $wo['user']['id']) {
                $error_code    = 6;
                $error_message = 'You are not the group owner';
            }
             else {
                if (Wo_AcceptJoinRequest($user_id, $group_id) === true) {
                    $response_data = array(
                        'api_status' => 200,
                        'message_data' => 'Request successfully accepted'
                    );
                }
                else{
                    $error_code    = 7;
                    $error_message = 'The Request not found';
                }
            }
        }
    }
    if ($_POST['type'] == 'delete_request') {
        if (empty($_POST['group_id'])) {
            $error_code    = 3;
            $error_message = 'group_id (POST) is missing';
        } 
        elseif (empty($_POST['user_id'])) {
            $error_code    = 3;
            $error_message = 'user_id (POST) is missing';
        }
        if (empty($error_code)) {
            $user_id   = Wo_Secure($_POST['user_id']);
            $group_id   = Wo_Secure($_POST['group_id']);
            $group_data = Wo_GroupData($group_id);
            if (empty($group_data)) {
                $error_code    = 5;
                $error_message = 'Group not found';
            } elseif ($group_data['user_id'] != $wo['user']['id']) {
                $error_code    = 6;
                $error_message = 'You are not the group owner';
            }
             else {
                if (Wo_DeleteJoinRequest($user_id, $group_id) === true) {
                    $response_data = array(
                        'api_status' => 200,
                        'message_data' => 'Request successfully deleted'
                    );
                }
                else{
                    $error_code    = 7;
                    $error_message = 'The Request not found';
                }
            }
        }
    }
}
else{
    $error_code    = 4;
    $error_message = 'type can not be empty';
}









