<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="./jquery-ui-1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="./jquery-ui-1.12.1/jquery-ui.min.css">
<link rel="stylesheet" href="./jquery-ui-1.12.1/jquery-ui.structure.min.css">
<link rel="stylesheet" href="./jquery-ui-1.12.1/jquery-ui.theme.min.css">
<style>
input[type="text"]{width:400px;}
th{text-align:left;}
</style>
</head>
<body>
<div id="container" class="ui-widget">
<div style="padding-left:10px;margin-bottom: 10px;display:none;" class="err_msg_div ui-state-error ui-corner-all">
  <p>
    <span style="float: left; margin: 0.3em 0.3em 0 0" class="ui-icon ui-icon-alert"></span>
    <span class="err_msg"></span> 
  </p>
</div>
<form method="POST">
<input type="hidden" name="state" value="post" />
Recipient User Mid  <input type="text" value="" name="mid" />
<br><br>
Type<br>
<input type="radio" name="type" value="button">Button
<input type="radio" name="type" value="text" checked>Text
<table id="text_opt">
<tr valign="top"><th rowspan="5">
Message Body<br>(up to 5) 
</th><td>
<input type="text" value="" name="msgBody1" />
</td></tr>
<tr><td>
<input type="text" value="" name="msgBody2" />
</td></tr>
<tr><td>
<input type="text" value="" name="msgBody3" />
</td></tr>
<tr><td>
<input type="text" value="" name="msgBody4" />
</td></tr>
<tr><td>
<input type="text" value="" name="msgBody5" />
</td></tr>
<tr><th>
Content Image URL
</th><td>
<input type="text" value="" name="originalContentUrl" />
</td></tr>
<tr><th>
Preview Image URL
</th><td>
<input type="text" value="" name="previewImageUrl" />
</td></tr>
<tr><td colspan="2">
*https only
</td></tr>
</table>
<br><br>
<input type="submit" value="SEND" class="ui-button ui-corner-all ui-widget">
</form>
</div>
<script>
$(function(){
    $('input[name="type"]:radio').change(function () {
        if($(this).val() == 'button') {
            $('#text_opt input').prop("disabled", true);
        } else {
            $('#text_opt input').prop("disabled", false);
        }
    });
});
</script>
</body>
</html>
<?php

use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Message\MultipleMessages;
use LINE\LINEBot\Message\RichMessage\Markup;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

require_once __DIR__.'/vendor/autoload.php';

error_reporting(-1);

if (!array_key_exists("state", $_POST)) {
    exit;
}
if (!array_key_exists("mid", $_POST) || empty($_POST["mid"])) {
    echo '<script>$(function(){$(\'.err_msg\').html(\'Please enter Mid.\');$(\'.err_msg_div\').show();});</script>';
    exit;
}

$setting = require('settings.php');
$channelId = $setting['channelId'];
$channelSecret = $setting['channelSecret'];
$channelMid = $setting['channelMid'];
$token = $setting['token'];
$targetMid = $_POST['mid'];

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($token);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);

if ($_POST['type'] == 'button') {
$imageUrl = "https://".$_SERVER["HTTP_HOST"] ."/linebot/betrend_logo.png";

$buttonTemplateBuilder = new ButtonTemplateBuilder(
    'button sample',
    'ボタンタイプメッセージのサンプルです。60 characters未満',
    $imageUrl,
    [
        new UriTemplateActionBuilder('Go to betrend.com', 'http://betrend.com'),
        new MessageTemplateActionBuilder('Say message', 'hello hello'),
    ]
);
$ret = $bot->pushMessage($targetMid, new TemplateMessageBuilder('おしらせ', $buttonTemplateBuilder));
echo "API Response:<br>";
var_dump($ret);
exit;
}

$textMessageBuilder = new TextMessageBuilder('default message');
if (!empty($_POST['msgBody5'])) {
    $textMessageBuilder = new TextMessageBuilder($_POST['msgBody1'], $_POST['msgBody2'], $_POST['msgBody3'], $_POST['msgBody4'], $_POST['msgBody5']);
} else if (!empty($_POST['msgBody4'])) {
    $textMessageBuilder = new TextMessageBuilder($_POST['msgBody1'], $_POST['msgBody2'], $_POST['msgBody3'], $_POST['msgBody4']);
} else if (!empty($_POST['msgBody3'])) {
    $textMessageBuilder = new TextMessageBuilder($_POST['msgBody1'], $_POST['msgBody2'], $_POST['msgBody3']);
} else if (!empty($_POST['msgBody2'])) {
    $textMessageBuilder = new TextMessageBuilder($_POST['msgBody1'], $_POST['msgBody2']);
} else if (!empty($_POST['msgBody1'])) {
    $textMessageBuilder = new TextMessageBuilder($_POST['msgBody1']);
}

$ret = $bot->pushMessage($targetMid, $textMessageBuilder);
echo "API Response:<br>";
var_dump($ret);

if (!empty($_POST["originalContentUrl"]) && !empty($_POST["previewImageUrl"])) {
    $ret = $bot->pushMessage($targetMid, new ImageMessageBuilder($_POST["originalContentUrl"], $_POST["previewImageUrl"]));
    
    echo "<br><br>API Response:<br>";
    var_dump($ret);
}

?>