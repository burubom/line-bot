<?php
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Message\MultipleMessages;
use LINE\LINEBot\Message\RichMessage\Markup;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

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
$events = $bot->parseEventRequest($body, $signature);

foreach ($events as $event) {
    if ($event instanceof MessageEvent) {
        if ($event instanceof TextMessage) {
            $replyText =  "you wrote...    ".$event->getText();
        } else {
            $replyText = "not text message.";
        }
        $ret = $bot->replyText($event->getReplyToken(), $replyText);
        error_log("reply text to token=".$event->getReplyToken());
        continue;
    }
    if ($event instanceof FollowEvent) {
        $ret = $bot->pushMessage($event->getUserId(), new TextMessageBuilder('For your information, your mid is ' . $event->getUserId()));
        error_log("followed by mid=".$event-> getUserId());
    }
    if ($event instanceof UnfollowEvent) {
        error_log("unfollowed by mid=".$event-> getUserId());
    }
}