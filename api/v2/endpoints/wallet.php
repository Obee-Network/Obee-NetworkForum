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
                        'top_up'
                    );



if (!empty($_POST['type']) && in_array($_POST['type'], $required_fields)) {

    if ($_POST['type'] == 'send') {

        $user_id  = (!empty($_POST['user_id']) && is_numeric($_POST['user_id'])) ? $_POST['user_id'] : 0;
        $amount   = (!empty($_POST['amount']) && is_numeric($_POST['amount'])) ? $_POST['amount'] : 0;
        $userdata = $db->where('user_id', $user_id)->where('active', '1')->getOne(T_USERS);
        $wallet   = $wo['user']['wallet'];
        if (empty($user_id) || empty($amount) || empty($userdata) || empty(floatval($wallet)) || $amount < 0) {
            $error_code    = 5;
            $error_message = 'Please check your details.';
        } else if ($wallet < $amount) {
            $error_code    = 6;
            $error_message = 'The amount exceded your current wallet!';
        } else {
            $amount          = ($amount <= $wallet) ? $amount : $wallet;
            $up_data1        = array(
                'wallet' => sprintf('%.2f', $userdata->wallet + $amount)
            );
            $up_data2        = array(
                'wallet' => sprintf('%.2f', $wallet - $amount)
            );
            $currency        = Wo_GetCurrency($wo['config']['ads_currency']);
            $notif_msg       = $wo['lang']['sent_you'];
            $db->where('user_id', $user_id)->update(T_USERS, $up_data1);
            $db->where('user_id', $wo['user']['id'])->update(T_USERS, $up_data2);
            $notification_data_array = array(
                'recipient_id' => $user_id,
                'type' => 'sent_u_money',
                'user_id' => $wo['user']['id'],
                'text' => "$notif_msg $amount$currency!",
                'url' => 'index.php?link1=wallet'
            );
            Wo_RegisterNotification($notification_data_array);
            $response_data = array(
                                    'api_status' => 200,
                                    'message' => "Money successfully sent."
                                );
        }


    }

    if ($_POST['type'] == 'top_up') {
        if (!empty($_POST['user_id']) && is_numeric($_POST['user_id']) && $_POST['user_id'] > 0 && !empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
            $user   = Wo_UserData(Wo_Secure($_POST['user_id']));
            $amount = Wo_Secure($_POST['amount']);
            if (!empty($user)) {
                //encrease wallet value with posted amount
                $result = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `wallet` = `wallet` + " . $amount . " WHERE `user_id` = '" . $user['id'] . "'");
                if ($result) {
                    $create_payment_log = mysqli_query($sqlConnect, "INSERT INTO " . T_PAYMENT_TRANSACTIONS . " (`userid`, `kind`, `amount`, `notes`) VALUES ('" . $user['id'] . "', 'WALLET', '" . $amount . "', 'paypal')");
                }
                $response_data = array(
                                    'api_status' => 200,
                                    'message' => "The money successfully added to your wallet."
                                );
            }
            else{
                $error_code    = 7;
                $error_message = 'user not found';
            }
        }
        else{
            $error_code    = 5;
            $error_message = 'Please check your details.';
        }

    }

}
else{
    $error_code    = 4;
    $error_message = 'type can not be empty';
}