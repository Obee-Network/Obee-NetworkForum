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
if ($wo['config']['pro'] == 1) {
	$posts = Wo_GetPromotedPost();
	$posts['shared_info'] = null;

	if (!empty($posts['postFile'])) {
		$posts['postFile'] = Wo_GetMedia($posts['postFile']);
	}
	if (!empty($posts['postFileThumb'])) {
		$posts['postFileThumb'] = Wo_GetMedia($posts['postFileThumb']);
	}

	if (!empty($posts['postPlaytube'])) {
		$posts['postText'] = strip_tags($posts['postText']);
	}



	if (!empty($posts['publisher'])) {
		foreach ($non_allowed as $key4 => $value4) {
          unset($posts['publisher'][$value4]);
        }
    }
    else{
    	$posts['publisher'] = null;
    }

    if (!empty($posts['user_data'])) {
    	foreach ($non_allowed as $key4 => $value4) {
          unset($posts['user_data'][$value4]);
        }
    }
    else{
    	$posts['user_data'] = null;
    }

    if (!empty($posts['parent_id'])) {
    	$shared_info = Wo_PostData($posts['parent_id']);
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
    	$posts['shared_info'] = $shared_info;
    }

    if (!empty($value['get_post_comments'])) {
        foreach ($value['get_post_comments'] as $key3 => $comment) {

	        foreach ($non_allowed as $key5 => $value5) {
	          unset($posts['get_post_comments'][$key3]['publisher'][$value5]);
	        }
	    }
	}
	$response_data = array(
	                        'api_status' => 200,
	                        'data' => $posts
	                    );
}
else{
	$error_code    = 4;
    $error_message = 'Promoted Posts not available';
}