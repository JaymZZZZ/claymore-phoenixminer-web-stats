<?php
/**
 * Claymore-PhoenixMiner-Web-Stats
 *
 * Simple PHP page to connect to any number of Claymore and PhoenixMiner miners and view hashrates, GPU temps, and fan speeds.
 *
 * @package     claymore-phoenixminer-web-stats
 * @version     1.0
 * @author      James D (jimok82@gmail.com)
 * @copyright   Copyright (c) 2018 James D.
 * @license     This file is part of claymore-phoenixminer-web-stats - free software licensed under the GNU General Public License version 3
 * @link        https://github.com/jimok82/claymore-phoenixminer-web-stats
 */
// ------------------------------------------------------------------------


//Set the list of servers

// 'Machine Name' => [
//      'hostname' => Hostname or IP of node,
//      'port' => Listening port for Claymore or PhoenixMiner
//      'password' => If you have a password for the remote monitor, enter it here. Otherwise leave it as null
//      'power_usage' => The power usage of the rig in watts, for profit calculation
//      'power_cost' => Set the power cost in USD per KW/h, for profit calculation
//      'pool_fee' => Set the pool fee percentage, for profit calculation
//]
$server_list = (object)[
	'Server_1' => [
		'hostname' => "server1.example.com",
		'port' => 3333,
		'password' => 'server_1_password',
		'power_usage' => null,
		'power_cost' => null,
		'pool_fee' => null,
	],
	'Server_2' => [
		'hostname' => "server2.example.com",
		'port' => 3333,
		'password' => 'server_2_password',
		'power_usage' => null,
		'power_cost' => null,
		'pool_fee' => null,
	]
];

//Set the socket wait timeout
$wait_timeout = 3;

//Set the GPU Temp yellow and red alert values in Celsius
$gpu_temp_yellow = 70;
$gpu_temp_red = 75;

//Set the GPU Fan yellow and red alert values in Percent of max speed
$gpu_fan_yellow = 50;
$gpu_fan_red = 75;

//Set the page refresh interval
$refresh_interval = 15;

?>
