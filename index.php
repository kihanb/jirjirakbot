<?php

/*
    JirJirakBot Project | Self-Destruct Messages (SDM)
    
    Coded By kihanb [kihanb.ir | @kihanb_ir | kihanbabaee@gmail.com]
*/
define('API_KEY', 'YOUR-BOT-TOKEN');
function Bot($method, $datas = []){
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
}
function EditMessage_inline($chat_id, $message_id, $text, $keyboard){
    Bot('editMessagetext', ['inline_message_id' => $message_id, 'text' => $text, 'reply_markup' => $keyboard]);
}
$update = json_decode(file_get_contents('php://input'));
if (isset($update->message)){
    $message = $update->message;
    $chat_id = $message->chat->id;
    $textmessage = $message->text;
    $lang = $message->from->language_code;
}
if(isset($update->callback_query)){
    $data = $update->callback_query->data;
    $chatid = $update->callback_query->message->chat->id;
    $fromid = $update->callback_query->from->id;
    $messageid = $update->callback_query->inline_message_id;
    $lang = $update->callback_query->from->language_code;
}
if ($textmessage == '/start'){
    if ($lang == "fa"){
        $txt = "🌹به ربات جیرجیرک خوش اومدی، با من میتونی متن های خود تخریبی بسازی!\nکافیه یه متن رو برام بفرستی که برات رمزنگاریش کنم، بعدش میتونی از طریق من به اشتراک بزاریش طوری که فقط یکبار بشه خوندش اونم بصورتی که کپی نشه";
    }else{
        $txt = "🌹Welcome to Jirjirakbot. You can build self-destruct messages with me!\nJust send me a massege, I encrypted then you can share it, the message will destruct after read and nobody can copy it!";
    }
    $lic = false;
    $read = file("data/users.txt");
    foreach ($read as $name){
        $name2 = STR_REPLACE("\n", "", $name);
        if ($name2 == $chat_id){
            $found = true;
        }
    }
    if ($found == false){
        $myfile2 = fopen("data/users.txt", "a") or die("Unable to open file!");
        fwrite($myfile2, "$chat_id\n");
        fclose($myfile2);
    }
    bot('sendMessage', ['chat_id' => $chat_id, 'text' => "$txt", ]);

}elseif (isset($textmessage)){
    if ($lang == "fa"){
        $txt = "پیام خودتخریبیت برای ارسال آماده اس!😉";
    }else{
        $txt = "Your self-destruct message is ready to share!😉";
    }
    bot('sendMessage', ['chat_id' => $chat_id, 'text' => "$txt", 'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Share | اشتراک گذاری', 'switch_inline_query' => "$textmessage"]]], 'resize_keyboard' => true]) ]);
}elseif (isset($update->inline_query)){
    $nnn = $update->inline_query->query;
    $nnp = str_replace(" ", "", $nnn);
    if (!empty($nnp) && !empty($nnn)){
        $array[] = array(
            'type' => 'article',
            'id' => 1,
            'title' => "Self-destruct message | پیام خود تخریبی",
            'description' => "$nnn",
            'input_message_content' => ['message_text' => "You Have a message!\n\nشما یک پیام دارید!"],
            'reply_markup' => ['inline_keyboard' => [[['text' => 'Show | نمایش',
            'callback_data' => "s_$nnn"]]]]);
        $results = json_encode($array);
        bot('answerInlineQuery', ['inline_query_id' => $update->inline_query->id, 'results' => $results]);
    }
}
elseif (strpos($data, 's_') !== false){
    if ($lang == "fa"){
        $txt = "پیام خوانده شد!";
    }else{
        $txt = "Message Read!";
    }
    $data = str_replace("s_", '', $data);
    EditMessage_inline($fromid, $messageid, "$txt", json_encode(['inline_keyboard' => [[['text' => "@JirJirakBot", "url" => "https://t.me/JirjirakBot?start"]], ], 'resize_keyboard' => true]));
    bot('answercallbackquery', ['callback_query_id' => $update->callback_query->id, 'text' => "$data", 'show_alert' => true]);
}

if ($chat_id = "615724046"){
    if ($textmessage == "آمار ربات"){
        $txtt = file_get_contents('data/users.txt');
        $member_id = explode("\n", $txtt);
        $amar = count($member_id) - 1;
        bot('sendMessage', ['chat_id' => $chat_id, 'text' => "Users: <code>$amar</code> Coded By @kihanb", 'parse_mode' => "HTML", ]);
    }
}

?>
