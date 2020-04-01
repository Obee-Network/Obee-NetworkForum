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
                        'create',
                        'remove',
                        'fetch'
                    );
if (!empty($_POST['type']) && in_array($_POST['type'], $required_fields)) {
    if ($_POST['type'] == 'create') {
        if (empty($_POST['user_id']) || !is_numeric($_POST['user_id']) || $_POST['user_id'] < 1) {
            $error_code    = 4;
            $error_message = 'user_id (POST) is missing';
        }
        if (!empty($_POST['user_id']) && $wo['user']['id'] == $_POST['user_id']) {
            $error_code    = 6;
            $error_message = 'you can not poke your self';
        }
        if (empty($error_code) && Wo_IsPoked(Wo_Secure($_POST['user_id']), $wo['user']['id'])) {
            $error_code    = 7;
            $error_message = 'this user is poked';
        }

        if (empty($error_code)) {
            if (Wo_IsPoked($wo['user']['id'],Wo_Secure($_POST['user_id']))) {
                $received_user_id = $wo['user']['id'];
                $send_user_id = Wo_Secure($_POST['user_id']);
                mysqli_query($sqlConnect, "DELETE FROM " . T_POKES . " WHERE `received_user_id` = '{$received_user_id}' AND `send_user_id` = {$send_user_id}");
            }
            $received_user_id = Wo_Secure($_POST['user_id']);
            $send_user_id     = $wo['user']['id'];
            $query = mysqli_query($sqlConnect, " INSERT INTO " . T_POKES . " (`received_user_id`,`send_user_id`) VALUES ({$received_user_id},{$send_user_id})");
            $poke_id = mysqli_insert_id($sqlConnect);
            if ($query) {
                $text                    = "";
                $type2                   = "poke";
                $notification_data_array = array(
                    'recipient_id' => $received_user_id,
                    'post_id' => $send_user_id,
                    'type' => 'poke',
                    'text' => $text,
                    'type2' => $type2,
                    'url' => 'index.php?link1=poke'
                );
                Wo_RegisterNotification($notification_data_array);
            }
            $poke = Wo_GetPokeById($poke_id);
            foreach ($non_allowed as $key => $value) {
                unset($poke['user_data'][$value]);
            }
            $response_data = array(
                                'api_status' => 200,
                                'message_data' => 'user successfully poked',
                                'data'         => $poke
                            );
        }
    }

    if ($_POST['type'] == 'remove') {
        if (empty($_POST['poke_id']) || !is_numeric($_POST['poke_id']) || $_POST['poke_id'] < 1) {
            $error_code    = 4;
            $error_message = 'poke_id (POST) is missing';
        }
        $poke = Wo_GetPokeById(Wo_Secure($_POST['poke_id']));
        if (empty($error_code) && empty($poke)) {
            $error_code    = 9;
            $error_message = 'poke not found';
        }
        if (empty($error_code) && !empty($poke) && $poke['send_user_id'] != $wo['user']['id']) {
            $error_code    = 10;
            $error_message = 'you are not the poke owner';
        }

        if (empty($error_code) && !empty($poke)) {
            $poke_id  = $poke['id'];
            mysqli_query($sqlConnect, "DELETE FROM " . T_POKES . " WHERE `id` = {$poke_id}");
            $response_data = array(
                                'api_status' => 200,
                                'message_data' => 'poke successfully deleted'
                            );
        }
    }

    if ($_POST['type'] == 'fetch') {
        $user_id       = Wo_Secure($wo['user']['id']);
        $query         = " SELECT * FROM " . T_POKES . " WHERE `received_user_id` = {$user_id}";
        $sql_query = mysqli_query($sqlConnect, $query);
        $pokes     = array();
        while ($fetched_data = mysqli_fetch_assoc($sql_query)) {
            if (!empty($fetched_data)) {
                $fetched_data['user_data'] = Wo_UserData($fetched_data['send_user_id']);
                foreach ($non_allowed as $key => $value) {
                    unset($fetched_data['user_data'][$value]);
                }
                if (!empty($fetched_data['user_data'])) {
                    $pokes[] = $fetched_data;
                }
            }
        }
        $response_data = array(
                                'api_status' => 200,
                                'data'         => $pokes
                            );
    }


}
else{
    $error_code    = 5;
    $error_message = 'type can not be empty';
}