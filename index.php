<?php
// Кросспостинг в телеграмм - каналы

//Строка для подтверждения адреса сервера из настроек Callback API
//97a85a46
$confirmation_token_A = 'confirmation_token';

//Ключ доступа сообщества
$token_A = 'GROUP_TOKEN';

//Получаем и декодируем уведомление
$data = json_decode(file_get_contents('php://input'));

//Проверяем, что находится в поле "type"
switch ($data->type) {
  //Если это уведомление для подтверждения адреса...
  case 'confirmation':
    //...отправляем строку для подтверждения
    if ($data->group_id == 'Здесь ид группы') {
    echo $confirmation_token_A;
    return header("HTTP/1.1 200 OK");
    } 
    break;

//Если это уведомление о новом сообщении...
case 'wall_post_new':
//Возвращаем "ok" серверу Callback API
//echo('ok');
//...получаем id его автора
$post_id = json_decode($data->object->id);
$from_id = $data->object->from_id;

$owner_id = $data->object->owner_id;
$owner_id_conclused = ltrim($owner_id, '-');

$date = $data->object->date;
$date_conclused = gmdate("Y-m-d\TH:i:s\Z", $date);

$marked_as_ads = $data->object->marked_as_ads;
$post_type = $data->object->post_type;
$text = $data->object->text;
// $can_edit = $data->object->can_edit;
$created_by = $data->object->created_by;
//$attachments = $data->object->attachments;
if (isset($data->object->attachments) && $data->object->attachments[0]->type == "photo") {
$attachments = $data->object->attachments[0]->photo->photo_604;
} else {
$attachments = '';
}

if ($marked_as_ads == 0 && $post_type == 'post'){
//  $lama . '  ' .

$homepage = file_get_contents("./indexlastposts.txt" );

if ($homepage !== $text)  {
$message = " {$text} \n
Вложения: {$attachments} \n
Пост опубликован с нашей официальной группы: https://vk.com/club{$owner_id_conclused}";
$fp = fopen("./indexlastposts.txt", "w+"); // файл открывается для записи с возможностью чтения. Если такой файл уже существует, то он перезаписывается, если нет - то он создается
$test = fwrite($fp, $text); // Запись в файл
fclose($fp); //Закрытие файла
} else {
    echo 'ok';
    die();
}

// file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);

// сюда нужно вписать токен вашего бота
define('TELEGRAM_TOKEN', 'CHANNEL_ID:BOT_TOKEN');
 
// сюда нужно вписать ваш внутренний айдишник
if ($data->group_id == 'VK_GROUP_ID') {
$chat_id = '@CHANNEL_ID';
}

file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
function message_to_telegram($text, $image, $chat_id) {
    $ch = curl_init();
    curl_setopt_array(
        $ch,
        array(
            CURLOPT_URL => 'https://api.telegram.org/bot' . TELEGRAM_TOKEN . '/sendPhoto',
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => array(
                'chat_id' => $chat_id,
                'caption' => $text,
                'photo' => $image,
            ),
            CURLOPT_PROXY => '167.71.142.245:8080',
            CURLOPT_PROXYUSERPWD => '',
            CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
            CURLOPT_PROXYAUTH => CURLAUTH_BASIC,
        )
    );
    curl_exec($ch);
}

message_to_telegram($message, $attachments, $chat_id);

}
break;
} //END switch ($data->type)
header("HTTP/1.1 200 OK");
echo 'ok';
return false;
?>
