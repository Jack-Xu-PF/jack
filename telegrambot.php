<?php

// ===== 加载配置 =====
$config = require __DIR__ . '/config.php';

$botToken = $config['bot_token'];
$apiUrl   = "https://api.telegram.org/bot{$botToken}/";

// ===== 读取 Telegram 数据 =====
$update = json_decode(file_get_contents('php://input'), true);

// ===== 发送文本消息 =====
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

// ===== 发送图片介绍卡片 =====
function sendPhoto($chatId, $photoUrl, $caption, $replyMarkup = null)
{
    global $apiUrl;

    $data = [
        'chat_id' => $chatId,
        'photo' => $photoUrl,
        'caption' => $caption,
        'parse_mode' => 'HTML'
    ];

    if ($replyMarkup) {
        $data['reply_markup'] = json_encode($replyMarkup);
    }

    file_get_contents($apiUrl . 'sendPhoto?' . http_build_query($data));
}

// ===== 主菜单（输入框按钮） =====
$mainKeyboard = [
    'keyboard' => [
        ['📞 联系我们'],
        ['🌐 前往官网']
    ],
    'resize_keyboard' => true,
    'one_time_keyboard' => false
];

// ===== 介绍文字 =====
$introText = <<<HTML
<b>它能做什么？</b>
您好:

<p>波场能量null</p>
<p>欢迎使用波场能量机器人</p>
<p>如果需要直接购买请前往我们的网站进行购买</p>
<p>联系客服获取大客户API对接方式</p>

请选择功能开始使用
HTML;

// ===== 处理普通消息 =====
if (isset($update['message'])) {

    $chatId = $update['message']['chat']['id'];
    $text   = trim($update['message']['text'] ?? '');

    // /start 命令
    if ($text === '/start') {

        // ① 发送介绍卡片（图片 + 文字）
        sendPhoto(
            $chatId,
            'https://www.gasstation.ai/tg/img/adslogo.jpg', // 图片 URL
            $introText
        );

        // ② 再发送主菜单
        sendMessage(
            $chatId,
            "请选择功能开始使用：",
            $mainKeyboard
        );

        exit;
    }

    // 联系我们
    if ($text === '📞 联系我们') {
        sendMessage(
            $chatId,
            "📩 联系我们：\n\nTelegram：{$config['contact_telegram']}",
            $mainKeyboard
        );
        exit;
    }

    // 前往官网
    if ($text === '🌐 前往官网') {
        sendMessage(
            $chatId,
            "🌐 官网地址：\n{$config['website_url']}",
            $mainKeyboard
        );
        exit;
    }
}
