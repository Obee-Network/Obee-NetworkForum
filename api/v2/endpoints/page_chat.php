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
                        'send',
                        'fetch',
                        'get_list'
                    );
if (!empty($_POST['type']) && in_array($_POST['type'], $required_fields)) {

    if ($_POST['type'] == 'send') {
        if (!empty($_POST['page_id']) && is_numeric($_POST['page_id']) && $_POST['page_id'] > 0 && !empty($_POST['recipient_id']) && is_numeric($_POST['recipient_id']) && $_POST['recipient_id'] > 0) {
            $page_data = Wo_PageData($_POST['page_id']);
            if ((!empty($page_data) && $_POST['recipient_id'] != $wo['user']['user_id'] && !empty($_POST['message_hash_id'])) && (!empty($_POST['text']) || !empty($_FILES['file']['name']) || !empty($_POST['image_url']) || !empty($_POST['gif']))) {

                $recipient_id = Wo_Secure($_POST['recipient_id']);
                $mediaFilename = '';
                $mediaName     = '';
                if (isset($_FILES['file']['name'])) {
                    $fileInfo      = array(
                        'file' => $_FILES["file"]["tmp_name"],
                        'name' => $_FILES['file']['name'],
                        'size' => $_FILES["file"]["size"],
                        'type' => $_FILES["file"]["type"]
                    );
                    $media         = Wo_ShareFile($fileInfo);
                    $mediaFilename = $media['filename'];
                    $mediaName     = $media['name'];
                }
                if (!empty($_POST['image_url'])) {
                    $fileend = '_url_image';
                    if (!empty($_POST['sticker_id'])) {
                        $fileend =  '_' . Wo_Secure($_POST['sticker_id']);
                    }
                    $mediaFilename = Wo_ImportImageFromUrl($_POST['image_url'], $fileend);
                }
                $gif = '';
                if (!empty($_POST['gif'])) {
                    if (strpos($_POST['gif'], '.gif') !== false) {
                        $gif = Wo_Secure($_POST['gif']);
                    }
                }

                $message_data = array(
                    'from_id' => Wo_Secure($wo['user']['user_id']),
                    'page_id' => Wo_Secure($_POST['page_id']),
                    'to_id' => $recipient_id,
                    'media' => Wo_Secure($mediaFilename),
                    'mediaFileName' => Wo_Secure($mediaName),
                    'time' => time(),
                    'text' => '',
                    'stickers' => $gif,
                );
                if (!empty($_POST['text'])) {
                    $message_data['text'] = Wo_Secure($_POST['text']);
                }

                $last_id = Wo_RegisterPageMessage($message_data);
                if ($last_id && $last_id > 0) {
                    $message_info = Wo_GetPageMessages(array(
                        'id' => $last_id,
                        'page_id' => Wo_Secure($_POST['page_id'])
                    ));

                    foreach ($non_allowed as $key => $value) {
                       unset($message_info[0]['user_data'][$value]);
                    }
                    if (empty($wo['user']['timezone'])) {
                        $wo['user']['timezone'] = 'UTC';
                    }
                    $timezone = new DateTimeZone($wo['user']['timezone']);
                    $messages = array();
                    foreach ($message_info as $key => $message) {
                        $message['time_text'] = Wo_Time_Elapsed_String($message['time']);
                        $message_po           = 'left';
                        if ($message['from_id'] == $wo['user']['user_id']) {
                            $message_po = 'right';
                        }
                        $message['position'] = $message_po;
                        $message['type']     = Wo_GetFilePosition($message['media']);
                        if ($message['type_two'] == 'contact') {
                            $message['type']   = 'contact';
                        }
                        $message['type']     = $message_po . '_' . $message['type'];
                        $message['file_size'] = 0;
                        if (!empty($message['media'])) {
                            $message['file_size'] = '0MB';
                            if (file_exists($message['file_size'])) {
                                $message['file_size'] = Wo_SizeFormat(filesize($message['media']));
                            }
                            $message['media']     = Wo_GetMedia($message['media']);
                        }
                        if (!empty($message['time'])) {
                            $time_today = time() - 86400;
                            if ($message['time'] < $time_today) {
                                $message['time_text'] = date('m.d.y', $message['time']);
                            } else {
                                $time = new DateTime('now', $timezone);
                                $time->setTimestamp($message['time']);
                                $message['time_text'] = $time->format('H:i');
                            }
                        }
                        $message['message_hash_id'] = $_POST['message_hash_id'];
                        array_push($messages, $message);
                    }

                    $response_data = array(
                        'api_status' => 200,
                        'data' => $messages
                    );
                }
                else{
                   $error_code    = 7;
                   $error_message = 'Something wrong'; 
                }
            }
            else{
                $error_code    = 6;
                $error_message = 'Please check your details.';
            }
        }
        else{
            $error_code    = 5;
            $error_message = 'page_id And recipient_id can not be empty';
        }
    }

    if ($_POST['type'] == 'fetch') {
        if (!empty($_POST['page_id']) && is_numeric($_POST['page_id']) && $_POST['page_id'] > 0 && !empty($_POST['recipient_id']) && is_numeric($_POST['recipient_id']) && $_POST['recipient_id'] > 0 && $_POST['recipient_id'] != $wo['user']['user_id']) {
            $page_id  = Wo_Secure($_POST['page_id']);
            $page_tab = Wo_PageData($page_id);
            if (!empty($page_tab) && is_array($page_tab)) {
                $offset = 0;
                if (!empty($_POST['after']) && empty($_POST['before'])) {
                    $offset = (!empty($_POST['after']) && is_numeric($_POST['after']) && $_POST['after'] > 0 ? Wo_Secure($_POST['after']) : 0);
                }

                if (!empty($_POST['before']) && empty($_POST['after'])) {
                    $offset = (!empty($_POST['before']) && is_numeric($_POST['before']) && $_POST['before'] > 0 ? Wo_Secure($_POST['before']) : 0);
                }
                
                
                $limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50 ? Wo_Secure($_POST['limit']) : 20);
                $new = 0;
                if (!empty($offset) && !empty($_POST['after'])) {
                    $new = true;
                }
                $old = 0;
                if (!empty($offset) && !empty($_POST['before'])) {
                    $old = true;
                }

                $message_info = Wo_GetPageMessages(array(
                                            'page_id' => $page_id,
                                            'from_id' => $page_tab['user_id'],
                                            'to_id'   => !empty($_POST['recipient_id']) ? Wo_Secure($_POST['recipient_id']) : 0,
                                            'limit' => $limit,
                                            'limit_type' => 1,
                                            'offset' => $offset,
                                            'new' => $new,
                                            'old' => $old
                                        ));

                foreach ($non_allowed as $key => $value) {
                   unset($message_info[0]['user_data'][$value]);
                }
                if (empty($wo['user']['timezone'])) {
                    $wo['user']['timezone'] = 'UTC';
                }
                $timezone = new DateTimeZone($wo['user']['timezone']);
                $messages = array();
                foreach ($message_info as $key => $message) {
                    $message['time_text'] = Wo_Time_Elapsed_String($message['time']);
                    $message_po           = 'left';
                    if ($message['from_id'] == $wo['user']['user_id']) {
                        $message_po = 'right';
                    }
                    $message['position'] = $message_po;
                    $message['type']     = Wo_GetFilePosition($message['media']);
                    if ($message['type_two'] == 'contact') {
                        $message['type']   = 'contact';
                    }
                    $message['type']     = $message_po . '_' . $message['type'];
                    $message['file_size'] = 0;
                    if (!empty($message['media'])) {
                        $message['file_size'] = '0MB';
                        if (file_exists($message['file_size'])) {
                            $message['file_size'] = Wo_SizeFormat(filesize($message['media']));
                        }
                        $message['media']     = Wo_GetMedia($message['media']);
                    }
                    if (!empty($message['time'])) {
                        $time_today = time() - 86400;
                        if ($message['time'] < $time_today) {
                            $message['time_text'] = date('m.d.y', $message['time']);
                        } else {
                            $time = new DateTime('now', $timezone);
                            $time->setTimestamp($message['time']);
                            $message['time_text'] = $time->format('H:i');
                        }
                    }
                    array_push($messages, $message);
                }

                $response_data = array(
                    'api_status' => 200,
                    'data' => $messages
                );

            }
            else{
                $error_code    = 6;
                $error_message = 'page not found';
            }
        }
        else{
            $error_code    = 5;
            $error_message = 'page_id And recipient_id can not be empty';
        }
    }

    if ($_POST['type'] == 'get_list') {
        $limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 ? Wo_Secure($_POST['limit']) : 0);
        $offset = '';
        if (!empty($_POST['offset'])) {
            $offset = Wo_Secure($_POST['offset']);
        }
        $search_key = '';
        if (!empty($_POST['search_key'])) {
            $search_key = Wo_Secure($_POST['search_key']);
        }
        $fetch_array = array(
            'user_id' => $wo['user']['id'], 
            'searchQuery' => $search_key, 
            'limit' => $limit,
            'offset' => $offset
        );
        $get = Wo_GetMessagesPagesAPP($fetch_array);
        $pages = array();
        if (!empty($get)) {
            foreach ($get as $key => $value) {
                $page = Wo_PageData($value['message']['page_id']);
                if (!empty($page) && !empty($value['message']) && !empty($value['message']['page_id']) && !empty($value['message']['user_id']) && !empty($value['message']['conversation_user_id'])) {
                    $message = Wo_GetPageMessages(array(
                                                'page_id' => $value['message']['page_id'],
                                                'from_id' => $value['message']['user_id'],
                                                'to_id'   => $value['message']['conversation_user_id'],
                                                'limit' => 1,
                                                'limit_type' => 1
                                            ));
                    if (!empty($message) && !empty($message[0]) && !empty($message[0]['time'])) {
                        $page['last_message'] = $message[0];
                        $timezone = new DateTimeZone($wo['user']['timezone']);
                        $time_today  = time() - 86400;
                        if (mb_strlen($page['last_message']['text']) > 20) {
                            $page['last_message']['text'] = mb_substr($page['last_message']['text'], 0, 20, "UTF-8") . '..';
                        }
                        if ($page['last_message']['time'] < $time_today) {
                            $page['last_message']['date_time'] = date('m.d', $page['last_message']['time']);
                        } else {
                            $time = new DateTime('now', $timezone);
                            $time->setTimestamp($page['last_message']['time']);
                            $page['last_message']['date_time'] = $time->format('H:i');
                        }
                        foreach ($non_allowed as $key4 => $value4) {
                          unset($page['last_message']['user_data'][$value4]);
                        }

                        $pages[] = $page;
                    }
                }
            }
        }
        $response_data = array(
                    'api_status' => 200,
                    'data' => $pages
                );
    }
}
else{
    $error_code    = 4;
    $error_message = 'type can not be empty';
}