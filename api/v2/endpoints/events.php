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
                        'edit',
                        'delete'
                    );
if (!empty($_POST['type']) && in_array($_POST['type'], $required_fields)) {
    $required_event_fields = array(
        'event_name',
        'event_location',
        'event_description',
        'event_start_date',
        'event_end_date',
        'event_start_time',
        'event_end_time',
        'event_id'
    );
    if ($_POST['type'] == 'edit') {
        foreach ($required_event_fields as $key => $value) {
            if (empty($_POST[$value]) && empty($error_code)) {
                $error_code    = 3;
                $error_message = $value . ' (POST) is missing';
            }
        }
        if (empty($error_code)) {
            $event_id          = Wo_Secure($_POST['event_id']);
            $event_name        = Wo_Secure($_POST['event_name']);
            $event_location    = Wo_Secure($_POST['event_location']);
            $event_description = Wo_Secure($_POST['event_description']);
            $event_start_date  = Wo_Secure($_POST['event_start_date']);
            $event_end_date    = Wo_Secure($_POST['event_end_date']);
            $event_start_time  = Wo_Secure($_POST['event_start_time']);
            $event_end_time    = Wo_Secure($_POST['event_end_time']);
            if (Is_EventOwner($event_id, $user = false, $admin = false)) {
                $registration_data = array(
                    'name' => $event_name,
                    'location' => $event_location,
                    'description' => $event_description,
                    'start_date' => $event_start_date,
                    'start_time' => $event_start_time,
                    'end_date' => $event_end_date,
                    'end_time' => $event_end_time
                );
                $result            = Wo_UpdateEvent($event_id, $registration_data);
                if ($result) {
                    if (!empty($_FILES["event-cover"]["tmp_name"])) {
                        $temp_name = $_FILES["event-cover"]["tmp_name"];
                        $file_name = $_FILES["event-cover"]["name"];
                        $file_type = $_FILES['event-cover']['type'];
                        $file_size = $_FILES["event-cover"]["size"];
                        Wo_UploadImage($temp_name, $file_name, 'cover', $file_type, $event_id, 'event');
                    }
                    $response_data = array(
                                    'api_status' => 200,
                                    'message_data' => 'Event successfully edited'
                                );
                }
            }
            else{
                $error_code    = 5;
                $error_message = 'You are not the event owner';
            }
        }
    }
    if ($_POST['type'] == 'delete') {

        if (empty($_POST['event_id'])) {
            $error_code    = 3;
            $error_message = 'event_id (POST) is missing';
        } 
        if (empty($error_code)) {
            $event_id          = Wo_Secure($_POST['event_id']);
            if (Is_EventOwner($event_id, $user = false, $admin = false)) {
                if (Wo_DeleteEvent($event_id)) {
                    $response_data = array(
                                    'api_status' => 200,
                                    'message_data' => 'Event successfully deleted'
                                );
                }
            }
            else{
                $error_code    = 5;
                $error_message = 'You are not the event owner';
            }
        }
    }
}
else{
    $error_code    = 4;
    $error_message = 'type can not be empty';
}









