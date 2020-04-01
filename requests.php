<?php
require_once('assets/init.php');
$f = '';
$s = '';
if (isset($_GET['f'])) {
    $f = Wo_Secure($_GET['f'], 0);
}
if (isset($_GET['s'])) {
    $s = Wo_Secure($_GET['s'], 0);
}
$hash_id = '';
if (!empty($_POST['hash_id'])) {
    $hash_id = $_POST['hash_id'];
} else if (!empty($_GET['hash_id'])) {
    $hash_id = $_GET['hash_id'];
} else if (!empty($_GET['hash'])) {
    $hash_id = $_GET['hash'];
} else if (!empty($_POST['hash'])) {
    $hash_id = $_POST['hash'];
}
$data            = array();
$allow_array     = array(
    'upgrade',
    'payment',
    'pay_with_bitcoin',
    'coinpayments_callback',
    'paypro_with_bitcoin',
    'upload-blog-image',
    'wallet',
    'download_user_info',
    'movies',
    'funding'
);
$non_login_array = array(
    'session_status',
    'open_lightbox',
    'get_welcome_users',
    'load_posts',
    'save_user_location',
    'load-more-groups',
    'load-more-pages',
    'load-more-users',
    'load_profile_posts',
    'confirm_user_unusal_login',
    'confirm_user',
    'confirm_sms_user',
    'resned_code',
    'resned_code_ac',
    'resned_ac_email',
    'contact_us',
    'login',
    'register',
    'recover',
    'recoversms',
    'reset_password',
    'search',
    'get_search_filter',
    'update_announcement_views',
    'get_more_hashtag_posts',
    'open_album_lightbox',
    'get_next_album_image',
    'get_previous_album_image',
    'get_next_product_image',
    'get_previous_product_image',
    'open_multilightbox',
    'get_next_image',
    'get_previous_image',
    'load-blogs',
    'load-recent-blogs'
);
if (!in_array($f, $allow_array)) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            exit("Restrcited Area");
        }
    } else {
        exit("Restrcited Area");
    }
}
if (!in_array($f, $non_login_array)) {
    if ($wo['loggedin'] == false && ($s != 'load_more_posts')) {
        if ($s != 'load-comments') {
            exit("Please login or signup to continue.");
        }
    }
}
$files = scandir('xhr');
unset($files[0]);
unset($files[1]);
if (file_exists('xhr/' . $f . '.php') && in_array($f . '.php', $files)) {
    include 'xhr/' . $f . '.php';
}
mysqli_close($sqlConnect);
unset($wo);
exit();