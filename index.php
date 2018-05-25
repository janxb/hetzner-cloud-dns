<?php
require "vendor/autoload.php";

$options = getopt(null, ["bind:", "apikey:"]);
var_dump($options);

$hetznerCloudResolver = new HetznerCloudResolver();


echo 'dns server starting on interface ' . $bindAddress . PHP_EOL;
$dns = new yswery\DNS\Server($lxdSocketResolver, $bindAddress);
$dns->start();
