<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2016 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
$json_error_data   = array();
$json_success_data = array();
if (empty($_GET['type']) || !isset($_GET['type'])) {
    $json_error_data = array(
        'api_status' => '400',
        'api_text' => 'failed',
        'api_version' => $api_version,
        'errors' => array(
            'error_id' => '1',
            'error_text' => 'Bad request, no type specified.'
        )
    );
    header("Content-type: application/json");
    echo json_encode($json_error_data, JSON_PRETTY_PRINT);
    exit();
}
$type = Wo_Secure($_GET['type'], 0);
if ($type == 'set_c') {
    if (empty($_GET['c'])) {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '3',
                'error_text' => 'No cookie was provided.'
            )
        );
    } 
    if (empty($json_error_data)) {
        $cookie = Wo_Secure($_GET['c']);
        $user_id  = Wo_GetUserFromSessionID($cookie);
        if (empty($user_id)) {
        	$json_error_data = array(
                'api_status' => '400',
                'api_text' => 'failed',
                'api_version' => $api_version,
                'errors' => array(
                    'error_id' => '6',
                    'error_text' => 'Invalid cookie or the cookie is not exists.'
                )
            );
            header("Content-type: application/json");
            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        }
        $user_login_data = Wo_UserData($user_id);
        if (empty($user_login_data)) {
            $json_error_data = array(
                'api_status' => '400',
                'api_text' => 'failed',
                'api_version' => $api_version,
                'errors' => array(
                    'error_id' => '6',
                    'error_text' => 'Username is not exists.'
                )
            );
            header("Content-type: application/json");
            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        } else {
            $_SESSION['user_id'] = $cookie;
            setcookie("user_id", $cookie, time() + (10 * 365 * 24 * 60 * 60));
            header("Location: " . Wo_SeoLink('index.php?link1=get_news_feed'));
            exit();
        }
    } else {
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
if ($type == 'delete_story') {
    if (empty($_POST['user_id'])) {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '3',
                'error_text' => 'No user id sent.'
            )
        );
    } else if (empty($_POST['story_id'])) {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '5',
                'error_text' => 'No story id sent.'
            )
        );
    } else if (empty($_POST['s'])) {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '5',
                'error_text' => 'No session sent.'
            )
        );
    }
    if (empty($json_error_data)) {
        $user_id         = $_POST['user_id'];
        $s               = Wo_Secure($_POST['s']);
        $user_login_data = Wo_UserData($user_id);
        $wo['lang'] = Wo_LangsFromDB($user_login_data['language']);
        if (empty($user_login_data)) {
            $json_error_data = array(
                'api_status' => '400',
                'api_text' => 'failed',
                'api_version' => $api_version,
                'errors' => array(
                    'error_id' => '6',
                    'error_text' => 'Username is not exists.'
                )
            );
            header("Content-type: application/json");
            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        } else if ($wo['loggedin'] == false) {
            $json_error_data = array(
                'api_status' => '400',
                'api_text' => 'failed',
                'api_version' => $api_version,
                'errors' => array(
                    'error_id' => '6',
                    'error_text' => 'Session id is wrong.'
                )
            );
            header("Content-type: application/json");
            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        } else {
            $story_id     = $_POST['story_id'];
            $delete = Wo_DeleteStatus($story_id);
            if ($delete) {
                $json_success_data = array(
                    'api_status' => 200,
                    'api_text' => 'success',
                    'api_version' => $api_version
                );
            } else {
                $json_error_data = array(
                    'api_status' => '400',
                    'api_text' => 'failed',
                    'api_version' => $api_version,
                    'errors' => array(
                        'error_id' => '9',
                        'error_text' => 'Can\'t delete the story.'
                    )
                );
                header("Content-type: application/json");
                echo json_encode($json_error_data, JSON_PRETTY_PRINT);
                exit();
            }
        }
    } else {
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
header("Content-type: application/json");
echo json_encode($json_success_data);
exit();
?>