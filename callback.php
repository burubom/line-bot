<?php
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\GuzzleHTTPClient;
use LINE\LINEBot\Message\MultipleMessages;
use LINE\LINEBot\Message\RichMessage\Markup;

require_once __DIR__.'/vendor/autoload.php';

error_reporting(-1);

$setting = require('settings.php');
$channelId = $setting['channelId'];
$channelSecret = $setting['channelSecret'];
$channelMid = $setting['channelMid'];
$targetMid = $setting['targetMid'];

$config = [
    'channelId' => $channelId,
    'channelSecret' => $channelSecret,
    'channelMid' => $channelMid,
];
$sdk = new LINEBot($config, new GuzzleHTTPClient($config));

// verify
$headerSig = $_SERVER['HTTP_X_LINE_SIGNATURE'];
$jsonStr = file_get_contents('php://input');
$sig = base64_encode(hash_hmac('sha256', $jsonStr , $channelSecret));
error_log('headerSig='.$headerSig);
error_log('sig='.$sig);
$jsonArr = json_decode($jsonStr, true);

switch ($jsonArr['events']['type']) {
    case 'follow':
    	$targetMid = $jsonArr['events']['source']['userId'];
        error_log('followed by MID='.$targetMid);
        $replyToken = $jsonArr['events']['replyToken'];
        
        $sdk->sendText($targetMid, 'hello!');
        break;
    case 'unfollow':
    	$targetMid = $jsonArr['events']['source']['userId'];
        error_log('unfollowed by MID='.$targetMid);
        exit;
        break;
}
