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


if (empty($_POST['user_id'])) {
    $error_code    = 3;
    $error_message = 'user_id (POST) is missing';
}
else if (empty($_POST['type'])) {
    $error_code    = 3;
    $error_message = 'type (POST) is missing';
}
else if (!in_array($_POST['type'], array_keys($wo['family']))) {
    $error_code    = 4;
    $error_message = 'type is wrong';
}
else{
	$registration_data_array = array(
        0 => array(
            'member_id' => Wo_Secure($_POST['user_id']),
            'member' => Wo_Secure($_POST['type']),
            'active' => 0,
            'user_id' => Wo_Secure($wo['user']['id']),
            'requesting' => Wo_Secure($wo['user']['id'])
        )
    );
    if (in_array($_GET['type'], $relationship_type)) {
        $registration_data_array[] = array(
            'member_id' => Wo_Secure($wo['user']['id']),
            'member' => Wo_Secure($_POST['type']),
            'active' => 0,
            'user_id' => Wo_Secure($_POST['user_id']),
            'requesting' => Wo_Secure($wo['user']['id'])
        );
    }
    foreach ($registration_data_array as $registration_data) {
        Wo_RegisterFamilyMember($registration_data);
    }
    $member_data = Wo_UserData($_POST['user_id']);
    if (!empty($member_data)) {
        $notification_data_array = array(
            'recipient_id' => $_POST['user_id'],
            'type' => 'added_u_as',
            'user_id' => $wo['user']['id'],
            'text' => $wo['lang']['sent_u_request'] . $wo['lang'][$wo['family'][Wo_Secure($_POST['type'])]],
            'url' => 'index.php?link1=timeline&u=' . $member_data['username'] . '&type=requests'
        );

        Wo_RegisterNotification($notification_data_array);

        $response_data = array(
                                'api_status' => 200,
                                'message' => 'Your request was successfully sent!'
                            );
    }
}