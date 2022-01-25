<?php
/**
 * Claymore-PhoenixMiner-Web-Stats
 *
 * Simple PHP page to connect to any number of Claymore and PhoenixMiner miners and view hashrates, GPU temps, and fan speeds.
 *
 * @package     claymore-phoenixminer-web-stats
 * @version     1.0.2
 * @author      James D (jimok82@gmail.com)
 * @copyright   Copyright (c) 2018-2021 James D.
 * @license     This file is part of claymore-phoenixminer-web-stats - free software licensed under the GNU General Public License version 3
 * @link        https://github.com/JaymZZZZ/claymore-phoenixminer-web-stats
 *
 *
 * @var boolean $debug_mode
 * @var object $miner_list
 * @var integer $refresh_interval
 * @var integer $require_admin_password
 * @var string $admin_password
 */
// ------------------------------------------------------------------------
session_start();
require_once 'conf.php';

if (strpos($_SERVER['REQUEST_URI'], "login.php") !== FALSE) {
    $url = "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url = str_replace("login.php", '', $url);
    header("Location: " . $url);
    die();
}

if (isset($_SESSION['user_logged_in']) && isset($_SESSION['admin_password_hash']) && $_SESSION['admin_password_hash'] == md5($admin_password)) {
    die();
}


if ($debug_mode) {
    error_reporting(E_ERROR | E_WARNING);
    ini_set('display_errors', 1);
    ini_set('error_prepend_string', '<p style="color: white;">');
    ini_set('error_append_string', '</p>');

} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}


?>
<!DOCTYPE html>
<html lang='en' class=''>
<head>
    <title>Login</title>
    <meta charset='UTF-8'>
    <meta name="robots" content="noindex">
    <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css'>
    <style class="cp-pen-styles">@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700);

        /* --------------------CSS----------------- */

        body {
            background: #222;
            font: 16px 'Open Sans', sans-serif;
            padding: 20px;
        }

        .box span {
            font-family: 'Lato', sans-serif;
            font-weight: 300;
            font-size: 20px;
            position: absolute;
        }

        .box span:nth-child(2) {
            top: 2px;
            left: 125px;
        }

        .box span:nth-child(7) {
            top: 85px;
            left: 125px;
        }

        .box span:nth-child(12) {
            top: 165px;
            left: 125px;
        }

        .box {
            background: linear-gradient(#194e6e, #4992d9);
            position: relative;
            display: inline-block;
            border-radius: 5px;
            width: 550px;
            height: 200px;
            vertical-align: top;
            text-align: center;
            margin-bottom: 10px;
        }


        .box__header {
            padding: 10px 25px;
            position: relative;
        }

        .box__body {
            padding: 0 25px;
        }

        /* STATS */

        .stats {
            color: #fff;
            position: relative;
            padding-bottom: 18px;
        }

        .stats__amount {
            font-size: 15px;
            font-weight: bold;
            line-height: 1.1;
        }

        .stats__name {
            font-size: 14px;
            font-weight: bold;
            line-height: 1.1;
        }

        .stats__caption {
            font-size: 16px;

        }

        .stats__change {
            position: absolute;
            top: 6px;
            right: 0;
            text-align: right;
        }

        .stats__value {
            font-size: 18px;
        }

        .stats__period {
            font-size: 18px;
            font-weight: bold;
        }

        .stats__value--positive {
            color: #AEDC6F;
            font-weight: bold;
        }

        .stats__value--negative {
            color: #9d050c;
            font-weight: bold;
        }

        .yellow-alert {
            color: #d3b715;
            font-weight: bold;
        }

        .red-alert {
            color: #ad050c;
            font-weight: bold;
        }

        .login__main {
            font-size: 40px;
        }

        .stats__name {
            font-size: 26px;
        }

        .stats__update_time {
            text-align: right;
            position: absolute;
            bottom: 10px;
            color: white;
            font-size: 10px;
        }
    </style>
</head>
<body>
<div id="results_<?php echo $name; ?>"
     class="box <?php if ($parser->miner_status->{$name} != 1) { ?> box-down <?php } else { ?> box-up <?php } ?>">
    <div class="box__header">

    </div>
    <div class="box__body">
        <div class="stats stats--main">
            <div class="stats__name">Enter Password
            </div>
            <form method="post">
                <input type="text" name="password">
                <input type="submit" name="submit" value="login">
            </form>
        </div>
    </div>
</div>

</body>
</html>