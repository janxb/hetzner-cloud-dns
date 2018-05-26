<?php
require "vendor/autoload.php";

$options = getopt(null, ["bind:", "apikey:"]);

if (sizeof($options) != 2) {
    die("Hetzner Cloud DNS: wrong arguments. Use --apikey= and --bind=" . PHP_EOL);
}

$hetznerCloudResolver = new HetznerCloudResolver($options['apikey']);

$dns = new yswery\DNS\Server($hetznerCloudResolver, $options['bind']);
$dns->start();