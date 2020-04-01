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
$json_error_data     = array();
$json_success_data   = array();
$json_success_data_2 = array();
$type                = Wo_Secure($_GET['type'], 0);
if ($type == 'new_post') {
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
        $wo['lang']      = Wo_LangsFromDB($user_login_data['language']);
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

            if (!empty($_POST['postText']) && Wo_IsUrl($_POST['postText'])) {


                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $_POST["postText"], $match)) {
                    $youtube_video = Wo_Secure($match[1]);
                    $api_request   = file_get_contents('https://www.googleapis.com/youtube/v3/videos?id=' . $youtube_video . '&key=AIzaSyDoOC41IwRzX5XvP7bNiCJXJfcK14HalM0&part=snippet,contentDetails,statistics,status');
                    $thumbnail     = '';
                    if (!empty($api_request)) {
                        $json_decode = json_decode($api_request);
                        if (!empty($json_decode->items[0]->snippet)) {
                            if (!empty($json_decode->items[0]->snippet->thumbnails->maxres->url)) {
                                $thumbnail = $json_decode->items[0]->snippet->thumbnails->maxres->url;
                            }
                            if (!empty($json_decode->items[0]->snippet->thumbnails->medium->url)) {
                                $thumbnail = $json_decode->items[0]->snippet->thumbnails->medium->url;
                            }
                            $info        = $json_decode->items[0]->snippet;
                            $title       = $info->title;
                            $description = $info->description;
                            if (!empty($json_decode->items[0]->snippet->tags)) {
                                if (is_array($json_decode->items[0]->snippet->tags)) {
                                    foreach ($json_decode->items[0]->snippet->tags as $key => $tag) {
                                        $tags_array[] = $tag;
                                    }
                                    $tags = implode(',', $tags_array);
                                }
                            }
                        }
                        // $output = array(
                        //     'title' => $title,
                        //     'images' => array(
                        //         $thumbnail
                        //     ),
                        //     'content' => $description,
                        //     'url' => $_POST["postText"]
                        // );

                        $_POST['url_title'] = $title;
                        $_POST['url_content'] = $description;
                        $_POST['url_image'] = $thumbnail;
                        $_POST['url_link'] = $_POST["postText"];
                    }
                } else if (isset($_POST["postText"])) {
                    $page_title = '';
                    $image_urls = array();
                    $page_body  = '';
                    $get_url    = $_POST["postText"];
                    include_once("assets/libraries/simple_html_dom.inc.php");
                    $get_image = getimagesize($get_url);
                    if (!empty($get_image)) {
                        $image_urls[] = $get_url;
                        $page_title   = 'Image';
                    } else {
                        $get_content = file_get_html($get_url);
                        foreach ($get_content->find('title') as $element) {
                            @$page_title = $element->plaintext;
                        }
                        if (empty($page_title)) {
                            $page_title = '';
                        }
                        @$page_body = $get_content->find("meta[name='description']", 0)->content;
                        $page_body = mb_substr($page_body, 0, 250, "utf-8");
                        if ($page_body === false) {
                            $page_body = '';
                        }
                        if (empty($page_body)) {
                            @$page_body = $get_content->find("meta[property='og:description']", 0)->content;
                            $page_body = mb_substr($page_body, 0, 250, "utf-8");
                            if ($page_body === false) {
                                $page_body = '';
                            }
                        }
                        $image_urls = array();
                        @$page_image = $get_content->find("meta[property='og:image']", 0)->content;
                        if (!empty($page_image)) {
                            if (preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $page_image)) {
                                $image_urls[] = $page_image;
                            }
                        } else {
                            foreach ($get_content->find('img') as $element) {
                                if (!preg_match('/blank.(.*)/i', $element->src)) {
                                    if (preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $element->src)) {
                                        $image_urls[] = $element->src;
                                    }
                                }
                            }
                        }
                    }
                    // $output = array(
                    //     'title' => $page_title,
                    //     'images' => $image_urls,
                    //     'content' => $page_body,
                    //     'url' => $_POST["postText"]
                    // );

                    $_POST['url_title'] = $page_title;
                    $_POST['url_content'] = $page_body;
                    $_POST['url_image'] = $image_urls[0];
                    $_POST['url_link'] = $_POST["postText"];
                }

            }




            $media         = '';
            $mediaFilename = '';
            $mediaName     = '';
            $html          = '';
            $recipient_id  = 0;
            $page_id       = 0;
            $group_id      = 0;
            $image_array   = array();
            if (isset($_POST['recipient_id']) && !empty($_POST['recipient_id'])) {
                $recipient_id = Wo_Secure($_POST['recipient_id']);
            } else if (isset($_POST['page_id']) && !empty($_POST['page_id'])) {
                $page_id = Wo_Secure($_POST['page_id']);
            } else if (isset($_POST['group_id']) && !empty($_POST['group_id'])) {
                $group_id = Wo_Secure($_POST['group_id']);
                $group    = Wo_GroupData($group_id);
                if (!empty($group['id'])) {
                    if ($group['privacy'] == 1) {
                        $_POST['postPrivacy'] = 0;
                    } else if ($group['privacy'] == 2) {
                        $_POST['postPrivacy'] = 2;
                    }
                }
            }
            if (isset($_FILES['postFile']['name'])) {
                $fileInfo = array(
                    'file' => $_FILES["postFile"]["tmp_name"],
                    'name' => $_FILES['postFile']['name'],
                    'size' => $_FILES["postFile"]["size"],
                    'type' => $_FILES["postFile"]["type"]
                );
                $media    = Wo_ShareFile($fileInfo);
                if (!empty($media)) {
                    $mediaFilename = $media['filename'];
                    $mediaName     = $media['name'];
                }
            }
            if (isset($_FILES['postVideo']['name']) && empty($mediaFilename)) {
                $fileInfo = array(
                    'file' => $_FILES["postVideo"]["tmp_name"],
                    'name' => $_FILES['postVideo']['name'],
                    'size' => $_FILES["postVideo"]["size"],
                    'type' => $_FILES["postVideo"]["type"],
                    'types' => 'mp4,m4v,webm,flv,mov,mpeg'
                );
                $media    = Wo_ShareFile($fileInfo);
                if (!empty($media)) {
                    $mediaFilename = $media['filename'];
                    $mediaName     = $media['name'];
                }
            }
            if (isset($_FILES['postMusic']['name']) && empty($mediaFilename)) {
                $fileInfo = array(
                    'file' => $_FILES["postMusic"]["tmp_name"],
                    'name' => $_FILES['postMusic']['name'],
                    'size' => $_FILES["postMusic"]["size"],
                    'type' => $_FILES["postMusic"]["type"],
                    'types' => 'mp3,wav'
                );
                $media    = Wo_ShareFile($fileInfo);
                if (!empty($media)) {
                    $mediaFilename = $media['filename'];
                    $mediaName     = $media['name'];
                }
            }
            $multi = 0;
            if (isset($_FILES['postPhotos']['name']) && empty($mediaFilename) && empty($_POST['album_name'])) {
                
                if (count($_FILES['postPhotos']['name']) == 1) {
                    if ($_FILES['postPhotos']['size'] > $wo['config']['maxUpload']) {
                        $invalid_file = 1;
                    } else if (Wo_IsFileAllowed($_FILES['postPhotos']['name']) == false) {
                        $invalid_file = 2;
                    } else {
                        $fileInfo = array(
                            'file' => $_FILES["postPhotos"]["tmp_name"],
                            'name' => $_FILES['postPhotos']['name'],
                            'size' => $_FILES["postPhotos"]["size"],
                            'type' => $_FILES["postPhotos"]["type"]
                        );
                        $media    = Wo_ShareFile($fileInfo);
                        if (!empty($media)) {
                            $mediaFilename = $media['filename'];
                            $mediaName     = $media['name'];
                        }
                    }
                } else {
                    $multi = 1;
                }
            }
            if (empty($_POST['postPrivacy'])) {
                $_POST['postPrivacy'] = 0;
            }
            $post_privacy  = 0;
            $privacy_array = array(
                '0',
                '1',
                '2',
                '3'
            );
            if (isset($_POST['postPrivacy'])) {
                if (in_array($_POST['postPrivacy'], $privacy_array)) {
                    $post_privacy = $_POST['postPrivacy'];
                }
            }
            $import_url_image = '';
            $url_link         = '';
            $url_content      = '';
            $url_title        = '';
            if (!empty($_POST['url_link']) && !empty($_POST['url_title'])) {
                $url_link  = $_POST['url_link'];
                $url_title = $_POST['url_title'];
                if (!empty($_POST['url_content'])) {
                    $url_content = $_POST['url_content'];
                }
                if (!empty($_POST['url_image'])) {
                    $import_url_image = @Wo_ImportImageFromUrl($_POST['url_image']);
                }
            }
            $post_text = '';
            $post_map  = '';
            if (!empty($_POST['postText']) && !ctype_space($_POST['postText'])) {
                $post_text = $_POST['postText'];
            }
            if (!empty($_POST['postMap'])) {
                $post_map = $_POST['postMap'];
            }
            $album_name = '';
            if (!empty($_POST['album_name'])) {
                $album_name = $_POST['album_name'];
            }
            if (!isset($_FILES['postPhotos']['name'])) {
                $album_name = '';
            }
            $traveling = '';
            $watching  = '';
            $playing   = '';
            $listening = '';
            $feeling   = '';
            if (!empty($_POST['feeling_type'])) {
                $array_types = array(
                    'feelings',
                    'traveling',
                    'watching',
                    'playing',
                    'listening'
                );
                if (in_array($_POST['feeling_type'], $array_types)) {
                    if ($_POST['feeling_type'] == 'feelings') {
                        if (!empty($_POST['feeling'])) {
                            if (array_key_exists($_POST['feeling'], $wo['feelingIcons'])) {
                                $feeling = $_POST['feeling'];
                            }
                        }
                    } else if ($_POST['feeling_type'] == 'traveling') {
                        if (!empty($_POST['feeling'])) {
                            $traveling = $_POST['feeling'];
                        }
                    } else if ($_POST['feeling_type'] == 'watching') {
                        if (!empty($_POST['feeling'])) {
                            $watching = $_POST['feeling'];
                        }
                    } else if ($_POST['feeling_type'] == 'playing') {
                        if (!empty($_POST['feeling'])) {
                            $playing = $_POST['feeling'];
                        }
                    } else if ($_POST['feeling_type'] == 'listening') {
                        if (!empty($_POST['feeling'])) {
                            $listening = $_POST['feeling'];
                        }
                    }
                }
            }
            if (isset($_FILES['postPhotos']['name'])) {
                $allowed = array(
                    'gif',
                    'png',
                    'jpg',
                    'jpeg'
                );
                for ($i = 0; $i < count($_FILES['postPhotos']['name']); $i++) {
                    if (count($_FILES['postPhotos']['name']) > 1) {
                        $new_string = pathinfo($_FILES['postPhotos']['name'][$i]);
                    } else {
                        $new_string = pathinfo($_FILES['postPhotos']['name']);
                    }
                    if (!in_array(strtolower($new_string['extension']), $allowed)) {
                        $errors[] = $error_icon . $wo['lang']['please_check_details'];
                    }
                }
            }
            if (!empty($_POST['answer']) && array_filter($_POST['answer'])) {
                if (!empty($_POST['postText'])) {
                    foreach ($_POST['answer'] as $key => $value) {
                        if (empty($value) || ctype_space($value)) {
                            $errors = 'Answer #' . ($key + 1) . ' is empty.';
                        }
                    }
                } else {
                    $errors = 'Please write the question.';
                }
            }
            if (empty($errors)) {
                $is_option = false;
                if (!empty($_POST['answer']) && array_filter($_POST['answer'])) {
                    $is_option = true;
                }
                $post_data = array(
                    'user_id' => Wo_Secure($user_id),
                    'page_id' => Wo_Secure($page_id),
                    'group_id' => Wo_Secure($group_id),
                    'postText' => Wo_Secure($post_text),
                    'recipient_id' => Wo_Secure($recipient_id),
                    'postFile' => Wo_Secure($mediaFilename, 0),
                    'postFileName' => Wo_Secure($mediaName),
                    'postMap' => Wo_Secure($post_map),
                    'postPrivacy' => Wo_Secure($post_privacy),
                    'postLinkTitle' => Wo_Secure($url_title),
                    'postLinkContent' => Wo_Secure($url_content),
                    'postLink' => Wo_Secure($url_link),
                    'postLinkImage' => Wo_Secure($import_url_image, 0),
                    'album_name' => Wo_Secure($album_name),
                    'multi_image' => Wo_Secure($multi),
                    'postFeeling' => Wo_Secure($feeling),
                    'postListening' => Wo_Secure($listening),
                    'postPlaying' => Wo_Secure($playing),
                    'postWatching' => Wo_Secure($watching),
                    'postTraveling' => Wo_Secure($traveling),
                    'time' => time()
                );
                if (isset($_POST['postSticker']) && Wo_IsUrl($_POST['postSticker']) && empty($_FILES) && empty($_POST['postRecord'])) {
                    $post_data['postSticker'] = $_POST['postSticker'];
                } else if (empty($_FILES['postPhotos']) && preg_match_all('/https?:\/\/(?:[^\s]+)\.(?:png|jpg|gif|jpeg)/', $post_data['postText'], $matches)) {
                    if (!empty($matches[0][0]) && Wo_IsUrl($matches[0][0])) {
                        $post_data['postPhoto'] = @Wo_ImportImageFromUrl($matches[0][0]);
                    }
                }
                if (!empty($is_option)) {
                    $post_data['poll_id'] = 1;
                }
                if (!empty($_POST['post_color']) && !empty($post_text) && empty($_POST['postRecord']) && empty($mediaFilename) && empty($mediaName) && empty($post_map) && empty($url_title) && empty($url_content) && empty($url_link) && empty($import_url_image) && empty($album_name) && empty($multi) && empty($video_thumb) && empty($post_data['postPhoto'])) {
                    $post_data['color_id'] = Wo_Secure($_POST['post_color']);
                }
                $id = Wo_RegisterPost($post_data);
                if ($id) {
                    if ($is_option == true) {
                        foreach ($_POST['answer'] as $key => $value) {
                            $add_opition = Wo_AddOption($id, $value);
                        }
                    }
                    if (isset($_FILES['postPhotos']['name'])) {
                        if (count($_FILES['postPhotos']['name']) > 0) {
                            for ($i = 0; $i < count($_FILES['postPhotos']['name']); $i++) {
                                $fileInfo = array(
                                    'file' => $_FILES["postPhotos"]["tmp_name"][$i],
                                    'name' => $_FILES['postPhotos']['name'][$i],
                                    'size' => $_FILES["postPhotos"]["size"][$i],
                                    'type' => $_FILES["postPhotos"]["type"][$i],
                                    'types' => 'jpg,png,jpeg,gif'
                                );
                                $file     = Wo_ShareFile($fileInfo, 1);
                                if (!empty($file)) {
                                    $media_album = Wo_RegisterAlbumMedia($id, $file['filename']);
                                }
                            }
                        }
                    }
                    $wo['story'] = Wo_PostData($id);
                    $html .= Wo_LoadPage('story/content');
                    $wo['story']['shared_info'] = null;

                    if (!empty($wo['story']['postFile'])) {
                        $wo['story']['postFile'] = Wo_GetMedia($wo['story']['postFile']);
                    }
                    if (!empty($wo['story']['postFileThumb'])) {
                        $wo['story']['postFileThumb'] = Wo_GetMedia($wo['story']['postFileThumb']);
                    }
                    if (!empty($wo['story']['postPlaytube'])) {
                        $wo['story']['postText'] = strip_tags($wo['story']['postText']);
                    }



                    if (!empty($wo['story']['publisher'])) {
                        foreach ($non_allowed as $key4 => $value4) {
                          unset($wo['story']['publisher'][$value4]);
                        }
                    }
                    else{
                        $wo['story']['publisher'] = null;
                    }

                    if (!empty($wo['story']['user_data'])) {
                        foreach ($non_allowed as $key4 => $value4) {
                          unset($wo['story']['user_data'][$value4]);
                        }
                    }
                    else{
                        $wo['story']['user_data'] = null;
                    }

                    if (!empty($wo['story']['parent_id'])) {
                        $shared_info = Wo_PostData($wo['story']['parent_id']);
                        if (!empty($shared_info)) {
                            if (!empty($shared_info['publisher'])) {
                                foreach ($non_allowed as $key4 => $value4) {
                                  unset($shared_info['publisher'][$value4]);
                                }
                            }
                            else{
                                $shared_info['publisher'] = null;
                            }

                            if (!empty($shared_info['user_data'])) {
                                foreach ($non_allowed as $key4 => $value4) {
                                  unset($shared_info['user_data'][$value4]);
                                }
                            }
                            else{
                                $shared_info['user_data'] = null;
                            }

                            if (!empty($shared_info['get_post_comments'])) {
                                foreach ($shared_info['get_post_comments'] as $key3 => $comment) {

                                    foreach ($non_allowed as $key5 => $value5) {
                                      unset($shared_info['get_post_comments'][$key3]['publisher'][$value5]);
                                    }
                                }
                            }
                        }
                        $wo['story']['shared_info'] = $shared_info;
                    }

                    if (!empty($value['get_post_comments'])) {
                        foreach ($value['get_post_comments'] as $key3 => $comment) {

                            foreach ($non_allowed as $key5 => $value5) {
                              unset($wo['story']['get_post_comments'][$key3]['publisher'][$value5]);
                            }
                        }
                    }
                }
            } else {
                header("Content-type: application/json");
                echo json_encode(array(
                    'api_status' => 400,
                    'api_text' => 'failed',
                    'api_version' => $api_version,
                    'errors' => $errors
                ));
                exit();
            }
        }
    } else {
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
$json_success_data22 = array(
    'api_status' => '200',
    'api_text' => 'success',
    'api_version' => $api_version,
    'post_html' => $html,
    'post_data' => $wo['story']
);
header("Content-type: application/json");
echo json_encode($json_success_data22);
exit();
?>