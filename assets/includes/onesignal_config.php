<?php 
require_once('assets/libraries/onesignal/vendor/autoload.php');

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle5\Client as GuzzleAdapter;
use Http\Client\Common\HttpMethodsClient as HttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use OneSignal\Config;
use OneSignal\Devices;
use OneSignal\OneSignal;

$One_config = new Config();
$One_config->setApplicationId($wo['config']['android_m_push_id']);
$One_config->setApplicationAuthKey($wo['config']['android_m_push_key']);

$Two_config = new Config();
$Two_config->setApplicationId($wo['config']['ios_m_push_id']);
$Two_config->setApplicationAuthKey($wo['config']['ios_m_push_key']);

$Three_config = new Config();
$Three_config->setApplicationId($wo['config']['android_n_push_id']);
$Three_config->setApplicationAuthKey($wo['config']['android_n_push_key']);

$four_config = new Config();
$four_config->setApplicationId($wo['config']['ios_n_push_id']);
$four_config->setApplicationAuthKey($wo['config']['ios_n_push_key']);

$five_config = new Config();
$five_config->setApplicationId($wo['config']['web_push_id']);
$five_config->setApplicationAuthKey($wo['config']['web_push_key']);

$guzzle = new GuzzleClient();

function Wo_SendPushNotification($data = array(), $push_type = 'chat') {
    global $sqlConnect, $wo, $One_config, $Two_config, $Three_config, $four_config, $five_config, $guzzle;
    if (empty($data)) {
        return false;
    }
    if (empty($data['notification']['notification_content'])) {
        return false;
    }
    if (empty($data['send_to'])) {
        return false;
    }
    if ($wo['config']['push'] == 0) {
        return false;
    }
    $client = new HttpClient(new GuzzleAdapter($guzzle), new GuzzleMessageFactory());
    if ($push_type == 'android_messenger') {
        $config_data = $One_config;
    }
    else if($push_type == 'ios_messenger'){
        $config_data = $Two_config;
    }
    else if($push_type == 'android_native'){
        $config_data = $Three_config;
    }
    else if($push_type == 'ios_native'){
        $config_data = $four_config;
    }
    else if($push_type == 'web'){
        $config_data = $five_config;
    }
    $api = new OneSignal($config_data, $client);
    $data['notification']['notification_content'] = Wo_EmoPhone($data['notification']['notification_content']);
    $data['notification']['notification_content'] = Wo_EditMarkup($data['notification']['notification_content']);
    $final_request_data = array(
        'include_player_ids' => $data['send_to'],
        'send_after' => new \DateTime('1 second'),
        'isChrome' => false,
        'contents' => array(
            'en' => $data['notification']['notification_content']
        ),
        'headings' => array(
            'en' => $data['notification']['notification_title']
        ),
        'android_led_color' => 'FF0000FF',
        'priority' => 10
    );
    if (!empty($data['notification']['notification_data'])) {
        $final_request_data['data'] = $data['notification']['notification_data'];
    }
    if (!empty($data['notification']['notification_image'])) {
        $final_request_data['large_icon'] = $data['notification']['notification_image'];
    }
    $send_notification = $api->notifications->add($final_request_data);
    if ($send_notification['id']) {
        return $send_notification['id'];
    }
    return false;
}
?>