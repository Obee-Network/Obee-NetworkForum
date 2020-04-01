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
else if (empty($_POST['s'])) {
    $json_error_data = array(
        'api_status' => '400',
        'api_text' => 'failed',
        'api_version' => $api_version,
        'errors' => array(
            'error_id' => '5',
            'error_text' => 'No session sent.'
        )
    );
    header("Content-type: application/json");
    echo json_encode($json_error_data, JSON_PRETTY_PRINT);
    exit();
}
else if (empty($_POST['user_id'])) {
    $json_error_data = array(
        'api_status' => '400',
        'api_text' => 'failed',
        'api_version' => $api_version,
        'errors' => array(
            'error_id' => '3',
            'error_text' => 'No user id sent.'
        )
    );
    header("Content-type: application/json");
    echo json_encode($json_error_data, JSON_PRETTY_PRINT);
    exit();
}
else if ($wo['loggedin'] == false) {
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
}
$non_allowed_config = array(
    'reCaptchaKey',
    'googleAnalytics',
    'AllLogin',
    'googleLogin',
    'facebookLogin',
    'twitterLogin',
    'linkedinLogin',
    'VkontakteLogin',
    'facebookAppId',
    'facebookAppKey',
    'googleAppId',
    'googleAppKey',
    'twitterAppId',
    'twitterAppKey',
    'linkedinAppId',
    'linkedinAppKey',
    'VkontakteAppId',
    'VkontakteAppKey',
    'smtp_username',
    'smtp_host',
    'smtp_password',
    'smtp_port',
    'smtp_encryption',
    'sms_or_email',
    'sms_username',
    'sms_password',
    'sms_phone_number',
    'eapi',
    'paypal_id',
    'paypal_secret',
    'paypal_mode',
    'weekly_price',
    'monthly_price',
    'yearly_price',
    'lifetime_price',
    'post_limit',
    'user_limit',
    'css_upload',
    'smooth_loading',
    'video_chat',
    'video_accountSid',
    'video_apiKeySid',
    'video_apiKeySecret',
    'video_configurationProfileSid',
    'monthly_boosts',
    'yearly_boosts',
    'lifetime_boosts',
    'instagramAppId',
    'instagramAppkey',
    'instagramLogin',
    'smtp_or_mail',
    'maintenance_mode',
    'developers_page',
    'order_posts_by',
    'groups',
    'pages',
    'games',
    'last_backup',
    'currency',
    'pro',
    'is_ok',
    'profile_privacy',
    'emailValidation',
    'emailNotification',
    'seoLink',
    'reCaptcha',
    'profileVisit',
    'censored_words',
    'online_sidebar',
    'second_post_button',
    'useSeoFrindly',
    'cacheSystem',
    'sms_t_phone_number',
    'amazone_s3_s_key',
    'amazone_s3_key',
    'sms_twilio_username',
    'sms_twilio_password',
    'amazone_s3',
    'stripe_secret',
    'stripe_id',
    'siteEmail',
    'mime_types',
    'logo_url',
    'classified_currency_s',
    'classified_currency',
    'amount_ref',
    'amount_percent_ref',
    'google_map_api'
);
$type               = Wo_Secure($_GET['type'], 0);
if ($type == 'get_settings') {
    $get_config = Wo_GetConfig();
    if (empty($get_config)) {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '6',
                'error_text' => 'No settings availabe.'
            )
        );
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
    /*if (empty($_GET['key'])) {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '1',
                'error_text' => 'App is not connected.'
            )
        );
        //header("Content-type: application/json");
        //echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        //exit();
    }*/
    /*if ($_GET['key'] != $get_config['widnows_app_api_id']) {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '1',
                'error_text' => 'App key is not valid.'
            )
        );
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }*/
    foreach ($non_allowed_config as $key => $value) {
        unset($get_config[$value]);
    }
    $get_config['windows_app_version'] = '0';
    $get_config['update_available'] = false;
    if (!empty($_POST['windows_app_version'])) {
        if ($get_config['windows_app_version'] > $_POST['windows_app_version']) {
           $get_config['update_available'] = true;
        } 
    }
    $get_config['windows_app_version'] = '1.0';
    $get_config['logo_url'] = $config['theme_url'] . '/img/logo.' . $get_config['logo_extension'];
    $get_config['page_categories'] = $wo['page_categories'];
    $get_config['user_messages'] = base64_encode('Error while connecting to our servers.');
    $json_success_data      = array(
        'api_status' => '200',
        'api_text' => 'success',
        'api_version' => $api_version,
        'config' => $get_config
    );
    header("Content-type: application/json");
    echo json_encode($json_success_data, JSON_PRETTY_PRINT);
    exit();
}
header("Content-type: application/json");
echo json_encode($json_success_data);
exit();
?>