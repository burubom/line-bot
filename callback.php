<?php
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Message\MultipleMessages;
use LINE\LINEBot\Message\RichMessage\Markup;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

require_once __DIR__.'/vendor/autoload.php';

error_reporting(-1);

$setting = require('settings.php');
$channelId = $setting['channelId'];
$channelSecret = $setting['channelSecret'];
$channelMid = $setting['channelMid'];
$targetMid = $setting['targetMid'];
$token = $setting['token'];

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($token);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);
$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];
$body = file_get_contents('php://input');
// $events = $bot->parseEventRequest($body, $signature);

// $ret = $bot->pushMessage('U921d0a6fa97a5dffe892ce106e7ad45d', new TextMessageBuilder('test text1', 'test text2', 'test text3'));
// var_dump($ret);
pushMsg($token, 'U921d0a6fa97a5dffe892ce106e7ad45d', 'test test');
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
 curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
 curl_setopt($ch, CURLOPT_PROXY,  getenv('FIXIE_URL'));  
 curl_setopt($ch, CURLOPT_PROXYPORT, 80);                                    
 curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
     'Content-Type: application/json',      
     'Authorization: Bearer '.$token,)                                                                       
 );
	$result = curl_exec($ch); 
     error_log('Res='.$result);
 }
