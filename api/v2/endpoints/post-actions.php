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
if (empty($_POST['post_id'])) {
    $error_code    = 4;
    $error_message = 'post_id (POST) is missing';
} else if (empty($_POST['action'])) {
    $error_code    = 5;
    $error_message = 'action (POST) is missing';
} else if (!in_array($_POST['action'], array('edit', 'delete', 'comment', 'like', 'dislike', 'wonder','report','save','disable_comments','reaction','boost'))) {
	$error_code    = 6;
    $error_message = 'Undefined action value';
}

if (empty($error_code)) {
	$action = '';
    if ($_POST['action'] == 'delete') {
		if (Wo_DeletePost($_POST['post_id']) === true) {
			$action = 'deleted';
		}
	} else if ($_POST['action'] == 'like') {
		if (Wo_AddLikes($_POST['post_id']) == 'unliked') {
			$action = 'unliked';
		} else {
			$action = 'liked';
		}
		$like_data = array(
			'count' => Wo_CountLikes($_POST['post_id'])
		);
	} else if ($_POST['action'] == 'dislike') {
		if (Wo_AddWonders($_POST['post_id']) == 'unwonder') {
			$action = 'like';
		} else {
			$action = 'disliked';
		}
		$dislike_data = array(
			'dislikes' => Wo_CountWonders($_POST['post_id'])
		);
	} else if ($_POST['action'] == 'wonder') {
		if (Wo_AddWonders($_POST['post_id']) == 'unwonder') {
			$action = 'unwondered';
		} else {
			$action = 'wondered';
		}
		$wonder_data = array(
			'wonders' => Wo_CountWonders($_POST['post_id'])
		);
	} else if ($_POST['action'] == 'comment') {
        if (empty($_POST['text'])) {
			$error_code    = 7;
            $error_message = 'text (POST) is empty';
		} else {
			$page_id = '';
	        if (!empty($_POST['page_id'])) {
	            $page_id = $_POST['page_id'];
	        }
	        $text_comment = $_POST['text'];
	        $C_Data = array(
	            'user_id' => Wo_Secure($wo['user']['user_id']),
	            'page_id' => Wo_Secure($page_id),
	            'post_id' => Wo_Secure($_POST['post_id']),
	            'text' => Wo_Secure($_POST['text']),
	            'time' => time()
	        );
	        $R_Comment     = Wo_RegisterPostComment($C_Data);
	        $comment_data = Wo_GetPostComment($R_Comment);
	        if (!empty($comment_data)) {
	        	$action = 'commented';
	            $comment_data = array(
	                'text' => $comment_data['Orginaltext'],
	                'post_comments_count' => Wo_CountPostComment($_POST['post_id'])
	            );
	        }
	    }
	}  else if ($_POST['action'] == 'edit') {
		if (empty($_POST['text'])) {
			$error_code    = 7;
            $error_message = 'text (POST) is empty';
		} else {
			$updatePost = Wo_UpdatePost(array(
                'post_id' => $_POST['post_id'],
                'text' => $_POST['text']
            ));
            if (!empty($updatePost)) {
            	$action = 'edited';
            }
		}
	}  else if ($_POST['action'] == 'report') {
        $post_data = array(
            'post_id' => $_POST['post_id']
        );
        if (Wo_ReportPost($post_data) == 'unreport') {
            $code = 0;
            $action = 'unreport post';
        } else {
            $code = 1;
            $action = 'report post';
        }
	}  else if ($_POST['action'] == 'save') {
        $post_data = array(
            'post_id' => $_POST['post_id']
        );
        if (Wo_SavePosts($post_data) == 'unsaved') {
            $code = 0;
            $action = 'unsaved post';
        } else {
            $code = 1;
            $action = 'saved post';
        }
	}  else if ($_POST['action'] == 'disable_comments') {
		$post_id = Wo_Secure($_POST['post_id']);
		$post = Wo_PostData($post_id);
		if (Wo_IsPostOnwer($post_id, $wo['user']['user_id'])) {
			if ($post['comments_status'] == 1) {
				$db->where('id', $post_id)->update(T_POSTS, array('comments_status' => 0));
	            $code = 0;
	            $action = 'post comments disabled';
			}
			else{
				$db->where('id', $post_id)->update(T_POSTS, array('comments_status' => 1));
	            $code = 1;
	            $action = 'post comments enabled';
			}
		}
		else{
			$error_code    = 7;
		    $error_message = 'You are not the post owner';
		}
	}  else if ($_POST['action'] == 'boost') {
		$post_id = Wo_Secure($_POST['post_id']);
		$post = Wo_PostData($post_id);
		if (Wo_IsPostOnwer($post_id, $wo['user']['user_id'])) {
			if (Wo_BoostPost($post_id) == 'unboosted') {
	            $code = 0;
	            $action = 'post unboosted';
			}
			else{
	            $code = 1;
	            $action = 'post boosted';
			}
		}
		else{
			$error_code    = 7;
		    $error_message = 'You are not the post owner';
		}
	}  else if ($_POST['action'] == 'reaction') {
		$reactions_types = array('Like','Love','HaHa','Wow','Sad','Angry');
		$post_id = Wo_Secure($_POST['post_id']);
		if (Wo_IsReacted($post_id, $wo['user']['user_id']) == true) {
			Wo_DeleteReactions($post_id);
			$code = 0;
            $action = 'reaction deleted';
		}

		if (!empty($_POST['reaction']) && in_array($_POST['reaction'], $reactions_types)) {
			$reaction = Wo_Secure($_POST['reaction']);
			Wo_AddReactions($post_id, $reaction);
			$code = 1;
            $action = 'reaction Added';
		}
		elseif (empty($action)){
			$error_code    = 8;
			$error_message = 'reaction (POST) is missing';
		}
	}
	if (!empty($action)) {
		$response_data = array(
			'api_status' => 200,
			'action' => $action
		);
		if (!empty($comment_data)) {
			$response_data['comment_data'] = $comment_data;
		}
		if (!empty($like_data)) {
			$response_data['likes_data'] = $like_data;
		}
		if (!empty($dislike_data)) {
			$response_data['dislike_data'] = $dislike_data;
		}
		if (!empty($wonder_data)) {
			$response_data['wonder_data'] = $wonder_data;
		}
		if ($_POST['action'] == 'report') {
			$response_data['code'] = $code;
		}
		if ($_POST['action'] == 'save') {
			$response_data['code'] = $code;
		}
		if ($_POST['action'] == 'disable_comments') {
			$response_data['code'] = $code;
		}
		if ($_POST['action'] == 'reaction') {
			$response_data['code'] = $code;
		}
		if ($_POST['action'] == 'boost') {
			$response_data['code'] = $code;
		}
	}
}