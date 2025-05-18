<?php

require 'vendor/autoload.php';

use EVEOnlineWormhole\ConfigProvider;
use EVEOnlineWormhole\EVEScoutClient;
use EVEOnlineWormhole\FCMNotificationClient;
use EVEOnlineWormhole\WormholeIdRepository;

$config = new ConfigProvider(__DIR__.'/config/config.json');;

$repo = new WormholeIdRepository();
$client = new EVEScoutClient();
$signatures = $client->fetchSignatures();
$wormholeIds = array_map(function($item) {
    return [
        'id' => (int) $item['id'],
        'out_signature' => $item['out_signature'],
    ];
}, $signatures);

$newWormholes = [];
foreach ($wormholeIds as $wormhole) {
    if (!$repo->isKnownId($wormhole['id'])) {
        $newWormholes[] = $wormhole['id'];
        $repo->saveWormhole($wormhole['id'], $wormhole['out_signature']);
    }
}

$newWormholes = ['1234'];

if (count($newWormholes) > 0) {
    $repo->flush();
    $notification = new FCMNotificationClient($config);
    $notification->sendNotification($newWormholes);
    echo 'Notification sent'. PHP_EOL;
}
