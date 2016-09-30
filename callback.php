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
$token = $setting['token'];

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

$event = $jsonArr['events'][0];
switch ($event['type']) {
    case 'follow':
    	$targetMid = $event['source']['userId'];
        error_log('followed by MID='.$targetMid);
        $replyToken = $event['replyToken'];
        
//         $ret = $sdk->sendText($targetMid, 'hello! mid=' . $targetMid);
//         error_log(print_r($ret,true));
        pushMsg($token, $targetMid, 'hello! mid=' . $targetMid);
        break;
    case 'unfollow':
    	$targetMid = $event['source']['userId'];
        error_log('unfollowed by MID='.$targetMid);
        exit;
        break;
}

function pushMsg($token, $targetMid, $text) {
$data = array(
    'to' => $targetMid,
    'messages' => array(
        array ( 'type'  => 'text',
        'text'  => $text),
    ),
);                                                                  
$data_string = json_encode($data);                                                                                                                                                                                                      
$ch = curl_init('https://api.line.me/v2/bot/message/push');                                                                      
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',      
    'Authorization: Bearer '.$token,)                                                                       
);
	$result = curl_exec($ch); 
    error_log('Res='.$result);
}
