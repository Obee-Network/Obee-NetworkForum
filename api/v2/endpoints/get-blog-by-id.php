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

if (empty($_POST['blog_id'])) {
    $error_code    = 3;
    $error_message = 'blog_id (POST) is missing';
}
else{
	$article = Wo_GetArticle($_POST['blog_id']);
	if (!empty($article)) {
		$article['content'] = strip_tags(htmlspecialchars_decode($article['content']));
		$article['time_text'] = Wo_Time_Elapsed_String($article['posted']);
		foreach ($non_allowed as $key4 => $value4) {
          unset($article['author'][$value4]);
        }
		
		$response_data = array(
                                'api_status' => 200,
                                'data' => $article
                            );
	}
	else{
		$error_code    = 4;
	    $error_message = 'Article not found';
	}
}