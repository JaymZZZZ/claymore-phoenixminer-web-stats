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

if ($require_admin_password) {

    if ($_POST['submit'] && $_POST['submit'] == "login") {
        $password = $_POST['password'];
        if ($password == $admin_password) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['admin_password_hash'] = md5($password);
        }
    }

    if (!isset($_SESSION['user_logged_in']) || !isset($_SESSION['admin_password_hash'])) {
        require_once "login.php";
        die();
    }

    if ($_SESSION['admin_password_hash'] != md5($admin_password)) {
        require_once "login.php";
        die();
    }
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
    <title>0 Miners | 0 MH/s</title>
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
            background: linear-gradient(#23ba58, #1d8241);
            position: relative;
            display: inline-block;
            border-radius: 5px;
            width: 550px;
            height: 640px;
            vertical-align: top;
            margin-bottom: 10px;
        }

        .box-debug {
            background: linear-gradient(#fffc1c, #ccc508);
            position: relative;
            display: inline-block;
            border-radius: 5px;
            width: 95%;
            min-height: 100px;
            vertical-align: top;
            margin-bottom: 10px;
            overflow-wrap: normal;
        }

        .box-down {
            background: linear-gradient(#9e3935, #993259);
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

        .stats--main .stats__amount {
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
<div class="stats stats--main">
    <div class="stats__amount" id="global_hashrate">Global Hashrate: 0 MH/s</div>
</div>
<?php foreach ($miner_list as $name => $miner) { ?>
    <div id="results_<?php echo $name; ?>"></div>
<?php } ?>

</body>
<script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous"></script>
<script language="JavaScript">
    jQuery(document).ready(function ($) {



        <?php foreach ($miner_list as $name => $miner) { ?>
        $.get("parser.php?name=<?php echo $name;?>", function (data) {
            $('#results_<?php echo $name;?>').replaceWith(data);
        });
        setInterval(function () {
            $.get("parser.php?name=<?php echo $name;?>", function (data) {
                $('#results_<?php echo $name;?>').replaceWith(data);
            });
        }, <?php echo $refresh_interval * 1000;?>);

        <?php } ?>

        setInterval(function () {
            var down_nodes = $("div.box.box-down").find().prevObject.length;
            var up_nodes = $("div.box.box-up").find().prevObject.length;
            var hashrate = 0.0
            $("div.stats__caption.result_hashrate").find().prevObject.each(function (miner) {
                var text = $(this).text().split(" MH/s");
                hashrate = hashrate + parseFloat(text[0]);
            })

            $('#global_hashrate').text('Global Hashrate: ' + hashrate + ' MH/s | ' + up_nodes + ' Up | ' + down_nodes + ' Down');
            document.title = '' + up_nodes + ' Miners | ' + hashrate + ' MH/s';
        }, <?php echo $refresh_interval * 1000;?>);


    })
</script>
</html>
