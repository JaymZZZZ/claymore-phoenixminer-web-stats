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


class json_parser
{

    public $miner_list = [];
    public $miner_status = [];
    public $miner_data_results = [];
    public $global_hashrate = 0;
    public $miner_count = 0;
    public $wait_timeout = 3;
    public $gpu_temp_yellow = 70;
    public $gpu_temp_red = 75;
    public $gpu_fan_high_yellow = 50;
    public $gpu_fan_high_red = 75;
    public $gpu_fan_low_yellow = 20;
    public $gpu_fan_low_red = 10;
    private $calc_json = "./calculators.json";
    public $error = null;
    public $debug = FALSE;


    public function parse_all_json_rpc_calls($selected)
    {

        $this->miner_list = $this->convert_to_object($this->miner_list);

        //Make sure the request miner is in list
        if (!$this->miner_list->{$selected} || !is_object($this->miner_list->{$selected})) {
            return;
        }
        $miner = $this->miner_list->{$selected};
        if ($miner->is_trex) {
            $this->miner_status[$selected] = $this->verify_trex_server($miner);
        } else {
            $this->miner_status[$selected] = $this->verify_rpc_server($miner);
        }

        $this->miner_data_results = (object)[];
        $this->miner_status = $this->convert_to_object($this->miner_status);


        $miner_data = (object)[];
        if ($this->miner_status->{$selected} == 1) {
            if ($miner->is_trex) {
                $miner_data = $this->get_trex_server_data($miner);
            } else {
                $miner_data = $this->get_rpc_server_data($miner);
            }

        }

        $this->miner_data_results->{$selected} = $miner_data;


        $this->miner_data_results = $this->convert_to_object($this->miner_data_results);

        $this->get_farm_stats();
    }


    private function verify_rpc_server($miner)
    {
        if (!is_object($miner)) {
            return '-1';
        }
        if ($fp = @fsockopen(gethostbyname($miner->hostname), $miner->port, $err_code, $err_str, $this->wait_timeout)) {
            fclose($fp);
            return '1';
        }

        return '3';
    }

    private function verify_trex_server($miner)
    {
        if (!is_object($miner)) {
            return '-1';
        }
        if ($this->get_trex_api_data($miner)) {
            return '1';
        }

        return '3';
    }

    private function get_rpc_server_data($miner)
    {
        if (!is_object($miner)) {
            return [];
        }
        $miner_data = (object)[];

        $socket = fsockopen(gethostbyname($miner->hostname), $miner->port, $err_code, $err_str);

        if ($miner->password != null) {
            $append = ',"psw":"' . $miner->password . '"';
        } else {
            $append = '';
        }

        $data = '{"id":1,"jsonrpc":"2.0","method":"miner_getstat1"' . $append . '} ' . "\r\n\r\n";

        fputs($socket, $data);
        $buffer = null;
        while (!feof($socket)) {
            $buffer .= fgets($socket, $miner->port);
        }
        if ($socket) {
            fclose($socket);
        }

        $response = json_decode($buffer);
        $result = $response->result;

        $miner_info = explode(' - ', $result[0]);
        $miner_data->version = $miner_info[0];
        $miner_data->coin = $miner_info[1];

        $minutes = $result[1];
        $zero = new DateTime('@0');
        $offset = new DateTime('@' . $minutes * 60);
        $diff = $zero->diff($offset);
        $miner_data->uptime = $diff->format('%ad %hh %im');
        $hashrate_stats = explode(';', $result[2]);
        $card_hashrate_stats = explode(';', $result[3]);
        $fan_and_temps = explode(";", $result[6]);
        $miner_data->pool = $result[7];
        $invalid_share_stats = $result[8];


        $miner_data->stats = (object)[
            'hashrate' => round($hashrate_stats[0] / 1000, 2),
            'shares' => $hashrate_stats[1],
            'stale' => $invalid_share_stats[0],
            'rejected' => $hashrate_stats[2]
        ];

        $miner_data->card_stats = [];
        $card_num = 1;
        foreach ($card_hashrate_stats as $key => $card_hashrate_stat) {
            $val = $key * 2;
            $miner_data->card_stats["Card " . $card_num] = (object)[
                'hashrate' => round($card_hashrate_stat / 1000, 2),
                'temp' => $fan_and_temps[$val],
                'mem_temp' => "N/A",
                'fan' => $fan_and_temps[$val + 1]
            ];
            $card_num++;
        }

        $temp_sum = 0;
        foreach ($miner_data->card_stats as $card_stat) {
            $temp_sum += $card_stat->temp;
        }
        $miner_data->temp_av = round($temp_sum / sizeof($miner_data->card_stats));

        if (is_numeric($miner->power_usage) && is_numeric($miner->power_cost) && is_numeric($miner->pool_fee)) {
            $miner_data->profitability = $this->get_profit_stats_from_api($miner_data->stats->hashrate, $miner_data->coin, $miner->power_usage, $miner->power_cost, $miner->pool_fee);
        }

        return $miner_data;
    }

    private function get_trex_server_data($miner)
    {
        if (!is_object($miner)) {
            return [];
        }
        $response = $this->get_trex_api_data($miner);
        if (!$response) {
            return [];
        }
        $miner_data = (object)[];

        $response = $this->convert_to_object($response);
        $result = $response->result;

        $miner_data->version = $result->description . " " . $result->version;
        $miner_data->coin = $result->algorithm;

        $minutes = $result->uptime / 60;
        $zero = new DateTime('@0');
        $offset = new DateTime('@' . $minutes * 60);
        $diff = $zero->diff($offset);
        $miner_data->uptime = $diff->format('%ad %hh %im');
        $cards = $result->gpus;
        $miner_data->pool = str_replace("stratum+tcp://", '', $result->active_pool->url);


        $miner_data->stats = (object)[
            'hashrate' => round($result->hashrate / 1000000, 2),
            'shares' => $result->accepted_count,
            'stale' => 0,
            'rejected' => $result->rejected_count
        ];

        $miner_data->card_stats = [];
        foreach ($cards as $card) {
            $miner_data->card_stats[$card->vendor . " " . $card->name] = (object)[
                'hashrate' => round($card->hashrate / 1000000, 2),
                'temp' => $card->temperature,
                'mem_temp' => $card->memory_temperature,
                'fan' => $card->fan_speed
            ];
        }

        $temp_sum = 0;
        foreach ($miner_data->card_stats as $card_stat) {
            $temp_sum += $card_stat->temp;
        }
        $miner_data->temp_av = round($temp_sum / sizeof($miner_data->card_stats));

        if (is_numeric($miner->power_usage) && is_numeric($miner->power_cost) && is_numeric($miner->pool_fee)) {
            $miner_data->profitability = $this->get_profit_stats_from_api($miner_data->stats->hashrate, $miner_data->coin, $miner->power_usage, $miner->power_cost, $miner->pool_fee);
        }

        return $miner_data;

    }

    public function show_temp_warning($value, $append)
    {

        if ($value >= $this->gpu_temp_red) {
            return "<div class='red-alert' style='display: inline'>$value$append</div>";
        } else if ($value >= $this->gpu_temp_yellow) {
            return "<div class='yellow-alert' style='display: inline'>$value$append</div>";
        } else {
            return $value . $append;
        }

    }

    public function show_fan_warning($value, $append)
    {

        if ($value >= $this->gpu_fan_high_red) {
            return "<div class='red-alert' style='display: inline'>$value$append</div>";
        } else if ($value >= $this->gpu_fan_high_yellow) {
            return "<div class='yellow-alert' style='display: inline'>$value$append</div>";
        } else if ($value <= $this->gpu_fan_low_red) {
            return "<div class='red-alert' style='display: inline'>$value$append</div>";
        } else if ($value <= $this->gpu_fan_low_yellow) {
            return "<div class='yellow-alert' style='display: inline'>$value$append</div>";
        } else {
            return $value . $append;
        }

    }

    public function show_profit($value)
    {
        $stripped_value = str_replace("$", '', $value);

        if ($stripped_value > 0) {
            $class = "stats__value--positive";
        } else {
            $class = "stats__value--negative";
        }

        return "<div class='" . $class . "' style='display: inline'>$value</div>";

    }

    private function check_server_availability()
    {
        $this->miner_list = $this->convert_to_object($this->miner_list);

        $x = 1;
        foreach ($this->miner_list as $name => $miner) {
            if ($fp = @fsockopen(gethostbyname($miner->hostname), $miner->port, $err_code, $err_str, $this->wait_timeout)) {
                $this->miner_status[$name] = '1';
            } else {
                $this->miner_status[$name] = '3';
            }
            if ($fp) {
                fclose($fp);
            }
            $x++;
        }

        $this->miner_status = $this->convert_to_object($this->miner_status);

    }


    private function get_farm_stats()
    {
        foreach ($this->miner_data_results as $miner_data_result) {
            $this->global_hashrate += $miner_data_result->stats->hashrate;
            $this->miner_count++;
        }
        $this->global_hashrate = number_format($this->global_hashrate, 2);
    }

    private function convert_to_object($array)
    {
        return json_decode(json_encode($array));
    }

    private function get_profit_stats_from_api($hashrate, $coin, $power_usage, $power_cost, $pool_fee)
    {
        $ch = curl_init();

        $coin_code = $this->get_id_from_calculators($coin);

        $url = "https://whattomine.com/coins/" . $coin_code . ".json?hr=" . $hashrate . "&p=" . $power_usage . "&fee=" . $pool_fee . "&cost=" . $power_cost . "&hcost=0.0&commit=Calculate";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);

        if ($this->debug) {
            $json_response['debug']['url'] = $url;
            $json_response['debug']['curl_info'] = curl_getinfo($ch);
        }
        curl_close($ch);

        $json_response['result'] = json_decode($result);

        return (object)$json_response;
    }

    private function get_trex_api_data($miner)
    {

        $sid = '';
        $ch = curl_init();

        $protocol = "http";
        if ($miner->trex_secure) {
            $protocol = "https";
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if (!is_null($miner->password)) {

            $login_url = $protocol . "://" . $miner->hostname . ":" . $miner->port . "/login?password=" . $miner->password;
            curl_setopt($ch, CURLOPT_URL, $login_url);

            $login_result = curl_exec($ch);

            if (!$login_result) {
                return FALSE;
            }

            $login_data = json_decode($login_result);

            $sid = $login_data->sid;
        }


        $url = $protocol . "://" . $miner->hostname . ":" . $miner->port . "/summary?sid=" . $sid;
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $result = json_decode($response);
        if (!$result || $result->success == "0") {
            return FALSE;
        }

        if ($this->debug) {
            $json_response['debug']['url'] = $url;
            $json_response['debug']['curl_info'] = curl_getinfo($ch);
        }
        curl_close($ch);

        $json_response['result'] = $result;

        return $json_response;
    }

    private function get_id_from_calculators($coin)
    {

        if (!file_exists($this->calc_json)) {

            return 151;

        }

        $json_file = file_get_contents($this->calc_json);

        $coin_list = json_decode(json_encode(json_decode($json_file), TRUE));

        foreach ($coin_list->coins as $coin_id => $coin_item) {
            if ($coin == $coin_item->tag) {

                return $coin_item->id;
            }
        }

        return 151;

    }

}


?>
