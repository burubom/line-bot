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

// verify
$headerSig = '';
foreach (getallheaders() as $name => $value) {
//     echo "$name: $value\n";
    if ($name == 'X-Line-Signature') $headerSig = $value;
}
$reqbody = file_get_contents('php://input');
$sig = base64_encode(hash_hmac('sha256', $reqbody , $channelSecret));

$config = [
    'channelId' => $channelId,
    'channelSecret' => $channelSecret,
    'channelMid' => $channelMid,
];
$sdk = new LINEBot($config, new GuzzleHTTPClient($config));
// Send a text message
$sdk->sendText("u066c3355d1192628c7e8b05b0521c58c", 'hello!');