<?php

/****************************************
 * LINE 機器人範例
 * 作者:林壽山
 * 聯絡資訊: superlevin@gmail.com
 ***************************************/

require_once('./LINEBotTiny.php');

// 重要資訊1
$channelSecret = "dd1f3347cff517604e7c851cef79a152";
// 重要資訊2
$channelAccessToken = "3YMOV+nLy+NiimJIc2/iYfnrX9yDJ2HHtGiHQt4a8z1ewDQgXkoiF2M0p9lSP2BTm4X1H/v7I5qvywzUPU9xpoPdNress4+yF6x2/13UA2dp9S6R9Ql/i3L7WXUa8zi3vy279ZqcQQoly2VgurwsagdB04t89/1O/w1cDnyilFU=";
// Google表單資料
$googledataspi = "https://spreadsheets.google.com/feeds/list/1G_i7_q3uYUCD00S1B7cCN1JnJTsNma3IuRETkjyhlq0/od6/public/values?alt=json";

// 建立Client from LINEBotTiny
$client = new LINEBotTiny($channelAccessToken, $channelSecret);

// 取得事件(只接受文字訊息)
foreach ($client->parseEvents() as $event) {
switch ($event['type']) {       
    case 'message':
        // 讀入訊息
        $message = $event['message'];

        // 將Google表單轉成JSON資料
        $json = file_get_contents($googledataspi);
        $data = json_decode($json, true);           
        $store_text=''; 
        // 資料起始從feed.entry          
        foreach ($data['feed']['entry'] as $item) {
            // 將keywords欄位依,切成陣列
            $keywords = explode(',', $item['gsx$keywords']['$t']);

            // 以關鍵字比對文字內容，符合的話將店名/地址寫入
            foreach ($keywords as $keyword) {
                if (mb_strpos($message['text'], $keyword) !== false) {                      
                    $store_text .= $item['gsx$bossname']['$t'].$item['gsx$mapid']['$t']."   ".$item['gsx$reborntime']['$t']."\r\n";                 
              }
            }
        }       



        switch ($message['type']) {
            case 'text':
                // 回覆訊息
                // 第一段 你要想找_(原字串)_ 讓我想想喔…
                // 第二段 介紹你_______不錯喔
                $client->replyMessage(array(
                    'replyToken' => $event['replyToken'],
                    'messages' => array(
                        array(
                            'type' => 'text',
                            'text' => $store_text,
                        )

                    ),
                ));               
                break;
            default:
                error_log("Unsupporeted message type: " . $message['type']);
                break;
        }
        break;
    default:
        error_log("Unsupporeted event type: " . $event['type']);
        break;
}
};