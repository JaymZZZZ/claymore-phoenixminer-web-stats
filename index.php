<?php
/**
 * Claymore-PhoenixMiner-Web-Stats
 *
 * Simple PHP page to connect to any number of Claymore and PhoenixMiner miners and view hashrates, GPU temps, and fan speeds.
 *
 * @package     claymore-phoenixminer-web-stats
 * @version     1.0.1
 * @author      James D (jimok82@gmail.com)
 * @copyright   Copyright (c) 2018 James D.
 * @license     This file is part of claymore-phoenixminer-web-stats - free software licensed under the GNU General Public License version 3
 * @link        https://github.com/JaymZZZZ/claymore-phoenixminer-web-stats
 *
 *
 * @var boolean $debug_mode
 * @var object $server_list
 * @var integer $refresh_interval
 */
// ------------------------------------------------------------------------

require_once 'conf.php';
require_once 'json_parser.class.php';


if ($debug_mode) {
    error_reporting(E_ERROR | E_WARNING);
    ini_set('display_errors', 1);

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
            width: 480px;
            height: 540px;
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
            font-size: 18px;

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
            color: #ee8b8f;
            font-weight: bold;
        }

        .yellow-alert {
            color: #d3b715;
            font-weight: bold;
        }

        .red-alert {
            color: #ee8b8f;
            font-weight: bold;
        }

        .stats--main .stats__amount {
            font-size: 40px;
        }

        .stats__name {
            font-size: 34px;
        }
    </style>
</head>
<body>
<div class="stats stats--main">
    <div class="stats__amount" id="global_hashrate">Global Hashrate: 0 MH/s</div>
</div>
<?php foreach ($server_list as $name => $miner) { ?>
    <div id="results_<?php echo $name; ?>"></div>
<?php } ?>

</body>
<script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous"></script>
<script language="JavaScript">
    jQuery(document).ready(function ($) {



        <?php foreach ($server_list as $name => $miner) { ?>
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
