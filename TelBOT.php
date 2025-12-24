<?php

// ===== 基础配置 =====
$botToken = 'xxxx';
$apiUrl = "https://api.telegram.org/bot{$botToken}/";

// 读取 Telegram 发送的数据
$update = json_decode(file_get_contents('php://input'), true);

// 发送消息函数
function sendMessage($chatId, $text, $replyMarkup = null)
{
    global $apiUrl;

    $data = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];

    if ($replyMarkup) {
        $data['reply_markup'] = json_encode($replyMarkup);
    }

    file_get_contents($apiUrl . 'sendMessage?' . http_build_query($data));
}

// ===== 处理消息 =====
if (isset($update['message'])) {

    $chatId = $update['message']['chat']['id'];
    $text   = $update['message']['text'] ?? '';

    // 按钮定义
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '📞 联系我们', 'callback_data' => 'contact_us'],
            ],
            [
                ['text' => '🌐 前往官网', 'url' => 'https://www.gasstation.ai']
            ]
        ]
    ];

    // /start 命令
    if ($text === '/start') {
        sendMessage(
            $chatId,
            "欢迎使用我们的官方机器人，请选择操作：",
            $keyboard
        );
    }
}

// ===== 处理按钮回调 =====
if (isset($update['callback_query'])) {

    $chatId = $update['callback_query']['message']['chat']['id'];
    $data   = $update['callback_query']['data'];

    if ($data === 'contact_us') {
        sendMessage(
            $chatId,
            "📩 联系我们方式：\n\nTelegram：@Gasstationai"
        );
    }
}
