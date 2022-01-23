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
 *
 * @var boolean $debug_mode
 * @var object $miner_list
 * @var integer $node_count
 * @var integer $execution_time
 * @var integer $refresh_interval
 * @var integer $wait_timeout
 * @var integer $gpu_temp_yellow
 * @var integer $gpu_temp_red
 * @var integer $gpu_fan_high_yellow
 * @var integer $gpu_fan_high_red
 * @var integer $gpu_fan_low_yellow
 * @var integer $gpu_fan_low_red
 */
// ------------------------------------------------------------------------

require_once 'conf.php';
require_once 'json_parser.class.php';

$parser = new json_parser();

if ($debug_mode) {
    error_reporting(E_ERROR | E_WARNING);
    ini_set('display_errors', 1);
    $parser->debug = TRUE;

} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}


$parser->miner_list = $miner_list;
$parser->wait_timeout = $wait_timeout;

$parser->gpu_temp_yellow = $gpu_temp_yellow;
$parser->gpu_temp_red = $gpu_temp_red;
$parser->gpu_fan_high_yellow = $gpu_fan_high_yellow;
$parser->gpu_fan_high_red = $gpu_fan_high_red;
$parser->gpu_fan_low_yellow = $gpu_fan_low_yellow;
$parser->gpu_fan_low_red = $gpu_fan_low_red;

$parser->parse_all_json_rpc_calls($_GET['name']);

?>
<?php foreach ($parser->miner_data_results as $name => $miner) { ?>
    <div id="results_<?php echo $name; ?>"
         class="box <?php if ($parser->miner_status->{$name} != 1) { ?> box-down <?php } else { ?> box-up <?php } ?>">
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
                <div class="stats__name"><?php echo $name; ?>
                    (<?php echo ($miner->coin == null) ? "N/A" : $miner->coin ?>)
                </div>
                <div class="stats__caption">
                    Miner: <?php echo ($miner->version == null) ? "N/A" : $miner->version ?></div>
                <div class="stats__change">
                    <div class="stats__value stats__value--positive">Uptime</div>
                    <div class="stats__period"><?php echo ($miner->uptime == null) ? "DOWN" : $miner->uptime ?></div>
                    <div class="stats__value">Update Time</div>
                    <div class="stats__period"><?php echo date("H:i:s Y-m-d"); ?></div>
                </div>
            </div>
            <div class="stats">
                <div class="stats__amount">Pool</div>
                <div class="stats__caption"><?php echo ($miner->pool == null) ? "N/A" : $miner->pool ?></div>
            </div>
            <div class="stats">
                <div class="stats__amount">Shares (Submitted / Stale / Rejected)</div>
                <div class="stats__caption">
                    <div class="stats__value--positive"
                         style="display: inline;"><?php echo number_format($miner->stats->shares, 0) ?></div>
                    / <?php echo number_format($miner->stats->stale, 0) ?> /
                    <div class="stats__value--negative"
                         style="display: inline;"><?php echo number_format($miner->stats->rejected, 0) ?></div>
                </div>
            </div>
            <div class="stats">
                <div class="stats__amount">Miner
                    Hashrate <?php if (!is_null($miner->profitability->result->profit)) { ?>(Daily Profit)<?php } ?></div>
                <div class="stats__caption result_hashrate">
                    <?php echo ($miner->stats->hashrate == null) ? "0.0" : $miner->stats->hashrate ?>
                    MH/s <?php if (!is_null($miner->profitability->result->profit)) { ?>(<?php echo $parser->show_profit($miner->profitability->result->profit) ?>)<?php } ?>
                </div>
            </div>
            <div class="stats">
                <div class="stats__amount">Video Card Stats</div>
                <div class="stats__caption">
                    <table class="width: 100%">
                        <thead>
                        <tr>
                            <th class="stats__amount">Card</th>
                            <th class="stats__amount">Hashrate</th>
                            <th class="stats__amount">GPU Temp</th>
                            <th class="stats__amount">Fan %</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count((array)$miner->card_stats) >= 1) {
                            foreach ($miner->card_stats as $key => $stat) { ?>
                                <tr>
                                    <th>Card <?php echo $key; ?></th>
                                    <th><?php echo number_format($stat->hashrate, 2) ?> MH/s</th>
                                    <th><?php echo $parser->show_temp_warning($stat->temp, "&deg; C") ?></th>
                                    <th><?php echo $parser->show_fan_warning($stat->fan, "%") ?></th>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="4"> No Card Data Available</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php }
if ($debug_mode) { ?>
    <div class="box-debug">
        DEBUG MODE
        <?php
        foreach ($parser->miner_list as $miner) {
            $miner->hostname = "MASKED";
            $miner->port = "MASKED";
            $miner->password = "MASKED";
        }
        echo "<pre>";
        print_r($parser);
        echo "Node Count:" . $node_count . "<br>";
        echo "Execution Time:" . $execution_time . "<br>";
        echo "</pre>";
        ?>
    </div>
<?php } ?>
