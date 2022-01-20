# claymore-phoenixminer-web-stats

Claymore-PhoenixMiner-Web-Stat is a simple PHP web stats page that utilizes the remote monitoring ports (JSON-RPC API)
available on Claymore Miner and PhoenixMiner. This page allows you to view the following stats pulled from your miners:

* Global hashrate for all miners
* Miner uptime
* Miner version
* Connected pool and port
* Submitted, stale and invalid shares
* Per GPU hashrates
* Per GPU temperatures (with configurable thresholds)
* Per GPU fan percentages (with configurable thresholds)
* Auto Refreshing (configurable)
* Display profitability from WhatToMine API

## Before Using

Make sure you are using either PhoenixMiner or Claymore and that you have the RPC API enabled.

For PhoenixMiner:

* Add the following to the end of your bat file - `-cdm 1 -cdmport <YOUR_PORT> -cdmpass <YOUR_PASSWORD>`

For Claymore:

* Add the following to the end of your bat file - `-mport <YOUR_PORT> -mpsw <YOUR_PASSWORD>`

For TeamRedMiner:

* Add the following to the end of your bat file - `--cm_api_listen=IP:PORT --cm_api_password=<YOUR_PASSWORD>`

For your server/machine hosting the app, make sure you have the `php-curl` module installed if you wish to have
profitability calculations

## How to use

Usage of the script is simple, and all you need to run it is a server with PHP and IIS/Apache

Installation Instructions

* Copy all files to a directory of your choice
* Edit `config.php` to update the server list, you can have as many or as few as you want
* Browse to `your/path/index.php` and view stats
* If you wish to edit the yellow and red warning thresholds for fan speed and GPU temp, you can change the values
  in `config.php`
* In order to enable profitability checks, you must edit `config.php` and add the following for each server (default is
  null)
    * Power usage - Usage in W/h (ex. 1200)
    * Power cost - Cost of power in USD per KW/h (ex. 0.105)
    * Pool Fee - Current pool fee in percent (ex. 0.9)

## How Can I Help?

If you find this little page useful, please consider buying me a drink, or at least donating a little bit of crypto so I
can buy my own drink :)

* ETH: `0x43883860168A1F3B53920EA5497Be99FEcdFD99E`
* ETC: `0x373a58Ac1ebfbB9C2Df26D42014c65A40F9A135B`
* DASH: `XsFW3Tqmfu1V74obQXib1efXCa9MR8Cy69`
* LTC: `LWQre6uUo9awERFokn2mVLbvto5Lw1SdWp`

## Screenshot

This is what the application looks like:

![Screenshot of claymore-phoenixminer-web-stats](https://raw.githubusercontent.com/JaymZZZZ/claymore-phoenixminer-web-stats/master/screenshot.png)

