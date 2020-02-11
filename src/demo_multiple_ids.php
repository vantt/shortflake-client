<?php
require "./TcpClient.php";

use ShortFlake\TcpClient;

$client = new TcpClient();
var_dump($client->getIds(5));