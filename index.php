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
 */
// ------------------------------------------------------------------------

require_once 'conf.php';

if($debug_mode) {
	error_reporting(E_ERROR|E_WARNING);
	ini_set('display_errors', 1);
} else {
	error_reporting(0);
	ini_set('display_errors', 0);
}

require_once 'json_parser.class.php';


$parser = new json_parser();
$parser->server_list = $server_list;
$parser->wait_timeout = $wait_timeout;

$parser->gpu_temp_yellow = $gpu_temp_yellow;
$parser->gpu_temp_red = $gpu_temp_red;

$parser->gpu_fan_yellow = $gpu_fan_yellow;
$parser->gpu_fan_red = $gpu_fan_red;

$parser->parse_all_json_rpc_calls();


?>
<!DOCTYPE html>
<html lang='en' class=''>
<head>
	<title><?php echo $parser->miner_count ?> Miners: <?php echo $parser->global_hashrate ?> MH/s</title>
	<script src='//production-assets.codepen.io/assets/editor/live/console_runner-079c09a0e3b9ff743e39ee2d5637b9216b3545af0de366d4b9aad9dc87e26bfd.js'></script>
	<script src='//production-assets.codepen.io/assets/editor/live/events_runner-73716630c22bbc8cff4bd0f07b135f00a0bdc5d14629260c3ec49e5606f98fdd.js'></script>
	<script src='//production-assets.codepen.io/assets/editor/live/css_live_reload_init-2c0dc5167d60a5af3ee189d570b1835129687ea2a61bee3513dee3a50c115a77.js'></script>
	<meta charset='UTF-8'>
	<meta name="robots" content="noindex">
	<meta http-equiv="refresh" content="<?php echo $refresh_interval ?>">
	<link rel="shortcut icon" type="image/x-icon" href="//production-assets.codepen.io/assets/favicon/favicon-8ea04875e70c4b0bb41da869e81236e54394d63638a1ef12fa558a4a835f1164.ico"/>
	<link rel="mask-icon" type="" href="//production-assets.codepen.io/assets/favicon/logo-pin-f2d2b6d2c61838f7e76325261b7195c27224080bc099486ddd6dccb469b8e8e6.svg" color="#111"/>
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
	<div class="stats__amount">Global Hashrate: <?php echo $parser->global_hashrate ?> MH/s</div>
</div>
<?php foreach ($parser->miner_data_results as $name => $miner) { ?>
	<div class="box <?php if ($parser->miner_status->{$name} != 1) { ?> box-down <?php } ?>">
		<div class="box__header">
			<?php if ($parser->miner_status->{$name} == 1) { ?>
				<div class="server">
					<ul>
						<li></li>
						<li></li>
						<li></li>
					</ul>
				</div>
			<?php } else { ?>
				<div class="server error">
					<ul>
						<li></li>
						<li></li>
						<li></li>
					</ul>
				</div>
			<?php } ?>
		</div>
		<div class="box__body">
			<div class="stats stats--main">
				<div class="stats__name"><?php echo $name; ?> (<?php echo $miner->coin ?>)</div>
				<div class="stats__caption">Miner: <?php echo $miner->version ?></div>
				<div class="stats__change">
					<div class="stats__value stats__value--positive">Uptime</div>
					<div class="stats__period"><?php echo $miner->uptime ?></div>
				</div>
			</div>
			<div class="stats">
				<div class="stats__amount">Pool</div>
				<div class="stats__caption"><?php echo $miner->pool ?></div>
			</div>
			<div class="stats">
				<div class="stats__amount">Shares (Submitted / Stale / Rejected)</div>
				<div class="stats__caption">
					<div class="stats__value--positive" style="display: inline;"><?php echo number_format($miner->stats->shares, 0) ?></div>
					/ <?php echo number_format($miner->stats->stale, 0) ?> /
					<div class="stats__value--negative" style="display: inline;"><?php echo number_format($miner->stats->rejected, 0) ?></div>
				</div>
			</div>
			<div class="stats">
				<div class="stats__amount">Miner Hashrate <?php if (!is_null($miner->profitability->profit)) { ?>(Daily Profit)<?php } ?></div>
				<div class="stats__caption">
					<?php echo $miner->stats->hashrate ?> MH/s <?php if (!is_null($miner->profitability->profit)) { ?>(<?php echo $parser->show_profit($miner->profitability->profit) ?>)<?php } ?>
				</div>
			</div>
			<div class="stats">
				<div class="stats__amount">Video Card Stats</div>
				<div class="stats__caption">
					<table width="100%">
						<thead>
						<tr>
							<th class="stats__amount">Card</th>
							<th class="stats__amount">Hashrate</th>
							<th class="stats__amount">GPU Temp</th>
							<th class="stats__amount">Fan %</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($miner->card_stats as $key => $stat) { ?>
							<tr>
								<th>Card <?php echo $key; ?></th>
								<th><?php echo number_format($stat->hashrate, 2) ?> MH/s</th>
								<th><?php echo $parser->show_temp_warning($stat->temp, "&deg; C") ?></th>
								<th><?php echo $parser->show_fan_warning($stat->fan, "%") ?></th>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<?php if($debug_mode) { ?>
	<div class="stats">
		DEBUG MODE
		<?php
		foreach	($parser->server_list as $miner){
			$miner->hostname = "MASKED";
			$miner->port = "MASKED";
			$miner->password = "MASKED";
		}
		echo json_encode($parser);
		?>
	</div>
<?php } ?>
</body>
</html>
