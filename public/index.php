<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new Weather\Client();

var_dump($client::getCurrentData());

try {
    $client->saveToFile();
    $client->saveToStorage();
} catch (Exception $e) {

}
