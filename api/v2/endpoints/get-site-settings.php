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
$get_config = Wo_GetConfig();
foreach ($non_allowed_config as $key => $value) {
    unset($get_config[$value]);
}
$get_config['logo_url'] = $config['theme_url'] . '/img/logo.' . $get_config['logo_extension'];
$get_config['page_categories'] = $wo['page_categories'];
$get_config['group_categories'] = $wo['group_categories'];
$get_config['blog_categories'] = $wo['blog_categories'];
$get_config['products_categories'] = $wo['products_categories'];
$get_config['job_categories'] = $wo['job_categories'];
$get_config['genders'] = $wo['genders'];
$get_config['currency_array'] = unserialize($get_config['currency_array']);
$get_config['currency_symbol_array'] = unserialize($get_config['currency_symbol_array']);
foreach ($wo['family'] as $key => $value) {
	$wo['family'][$key] = $wo['lang'][$value];
}
$get_config['family'] = $wo['family'];
if (!empty($wo['post_colors'])) {
	foreach ($wo['post_colors'] as $key => $color) {
		if (!empty($color->image)) {
			$wo['post_colors'][$key]->image = Wo_GetMedia($color->image);
		}
	}
}
$get_config['post_colors'] = $wo['post_colors'];
$get_config['post_reactions_types'] = array('Like','Love','HaHa','Wow','Sad','Angry');
$response_data      = array(
    'api_status' => 200,
    'config' => $get_config
);