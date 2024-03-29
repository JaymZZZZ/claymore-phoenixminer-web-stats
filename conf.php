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
 */
// ------------------------------------------------------------------------

//Require basic password to view stats? Set to "1" if you want password protection then set your desired password below
$require_admin_password = 0;
$admin_password = "changeit";

//Set the list of miners

// 'Machine Name' => [
//      'hostname' => Hostname or IP of node,
//      'port' => Listening port for Claymore or PhoenixMiner
//      'password' => If you have a password for the remote monitor, enter it here. Otherwise leave it as null
//                    If using TRex Miner with an API key enabled - Must be the password you used to generate your API key
//      'power_usage' => The power usage of the rig in watts, for profit calculation
//      'power_cost' => Set the power cost in USD per KW/h, for profit calculation
//      'pool_fee' => Set the pool fee percentage, for profit calculation
//      (OPTIONAL) 'is_trex' => Set to 1 if using Trex miner, since it will need a different parser
//      (OPTIONAL) 'trex_secure' => Set to 1 if using HTTPS and 0 if using HTTP (HTTPS Recommended)
//]
$miner_list = (object)[
    'Miner_Claymore_Example' => [
        'hostname' => "server1.example.com",
        'port' => 3333,
        'password' => 'server_1_password',
        'power_usage' => null,
        'power_cost' => null,
        'pool_fee' => null,
        //'is_trex' => 0,
        //'trex_secure' => 0
        //'trex_api_password' => 'not_needed'
    ],
    'Miner_TRex_Example' => [
        'hostname' => "server2.example.com",
        'port' => 4067,
        'password' => 'trex_api_password',
        'power_usage' => null,
        'power_cost' => null,
        'pool_fee' => null,
        'is_trex' => 1,
        'trex_secure' => 1,
    ]
];

//Set the socket wait timeout
$wait_timeout = 3;

//Set the GPU Temp yellow and red alert values in Celsius
$gpu_temp_yellow = 70;
$gpu_temp_red = 75;

//Set the GPU Memory Temp yellow and red alert values in Celsius
$gpu_mem_temp_yellow = 70;
$gpu_mem_temp_red = 75;

//Set the MAXIMUM GPU Fan yellow and red alert values in Percent of max speed
$gpu_fan_high_yellow = 50;
$gpu_fan_high_red = 75;

//Set the MINIMUM GPU Fan yellow and red alert values in Percent of max speed
$gpu_fan_low_yellow = 20;
$gpu_fan_low_red = 10;

//Set the page refresh interval
$refresh_interval = 15;

//Enable Debug Mode if you are having issues
$debug_mode = FALSE;


