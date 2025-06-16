<?php

require __DIR__ . '/../api/vendor/autoload.php';
// if (!file_exists('novagram.phar')) copy('https://gaetano.eu.org/novagram/phar.phar', 'novagram.phar');
// require_once 'novagram.phar';

require __DIR__ . '/customer.php';

use skrtdev\NovaGram\Bot;
use skrtdev\Telegram\Message;
use skrtdev\Telegram\Invoice;
use skrtdev\Telegram\CallbackQuery;

define("BOT_TOKEN", '6327764045:AAFjJ5zDjVT_r7OfZa6Is9-Gel0zpZ-2a_E');
define("DEVELOPER_ID", 5456281641);
define("CHANNEL_ID", -1002310111830);
define("LOGGING_ID", -1002176139699);

$Bot = new Bot(BOT_TOKEN, [
    'parse_mode' => 'MarkdownV2',
    'debug' => LOGGING_ID
]);

$Bot->addErrorHandler(function (Throwable $e) use ($Bot) {
    $Bot->debug( (string) $e );
});

$Bot->onCommand('start', function (Message $message) use ($Bot) {
    if ($message->chat->type != 'private') return;

    $customer = new Customer($message->from->id);

    $chat = $Bot->getChat(CHANNEL_ID);
    $response = $Bot->getChatMember(CHANNEL_ID, $message->from->id);
    
    if (!in_array($response->status, ['member', 'administrator', 'creator'])) {
        $message->reply("📢 <b>Please join our channel first to continue</b>\nThen send /start again.", [
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'resize_keyboard' => true,
                'inline_keyboard' => [
                    [
                        [
                            'text' => "{$chat->title}",
                            'url' => "{$chat->invite_link}"
                        ]
                    ]
                ]
            ])
        ]);
        return;
    }

    $msg  = "👋 <b>Welcome to 🇮🇩 ɖքʀ•ʟʏռӼ ʏȶ</b>\n\n";
    $msg .= "🧾 <b>User Information:</b>\n";
    $msg .= "• 🆔 Telegram ID: <code>{$customer->getTelegramID()}</code>\n";
    $msg .= "• 🔑 Access Key: <code>{$customer->getAccessKey()}</code>\n";
    $msg .= "• 💰 Balance: <code>" . ($customer->getIsUnlimited() ? 'Unlimited ♾️' : number_format($customer->getCredits())) . "</code>\n\n";
    $msg .= "🛒 Buy Balance: <a href='https://t.me/DPR_LynX'>@DPR_LynX</a>";

    $message->reply($msg, [
        'parse_mode' => 'HTML',
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'inline_keyboard' => [
                [
                    [
                        'text' => '♻️ Revoke Access Key',
                        'callback_data' => 'revoke_access_key'
                    ]
                ]
            ]
        ])
    ]);
});

$Bot->onCallbackData('back_home', function (CallbackQuery $callback_query) {
    $customer = new Customer($callback_query->from->id);

    $msg  = "👋 <b>Welcome back to 🇮🇩 ɖքʀ•ʟʏռӼ ʏȶ</b>\n\n";
    $msg .= "🧾 <b>User Information:</b>\n";
    $msg .= "• 🆔 Telegram ID: <code>{$customer->getTelegramID()}</code>\n";
    $msg .= "• 🔑 Access Key: <code>{$customer->getAccessKey()}</code>\n";
    $msg .= "• 💰 Balance: <code>" . ($customer->getIsUnlimited() ? 'Unlimited ♾️' : number_format($customer->getCredits())) . "</code>\n\n";
    $msg .= "🛒 Buy Balance: <a href='https://t.me/DPR_LynX'>@DPR_LynX</a>";

    $callback_query->message->editText($msg, [
        'parse_mode' => 'HTML',
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'inline_keyboard' => [
                [
                    [
                        'text' => '♻️ Revoke Access Key',
                        'callback_data' => 'revoke_access_key'
                    ]
                ]
            ]
        ])
    ]);
});

$Bot->onCallbackData('revoke_access_key', function (CallbackQuery $callback_query) {
    $customer = new Customer($callback_query->from->id);
    $new_key = $customer->revokeAccessKey();

    $msg = "✅ <b>Access Key has been successfully changed!</b>\n\n";
    $msg .= "🔑 <b>Your New Access Key:</b>\n<code>{$new_key}</code>";

    $callback_query->answer('🔐 Access Key successfully updated!');
    
    $callback_query->message->editText($msg, [
        'parse_mode' => 'HTML',
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'inline_keyboard' => [
                [
                    [
                        'text' => '🔙 Back to Home',
                        'callback_data' => 'back_home'
                    ]
                ]
            ]
        ])
    ]);
});


use skrtdev\NovaGram\Bot;
use skrtdev\Telegram\Message;
use skrtdev\Telegram\Invoice;
use skrtdev\Telegram\CallbackQuery;

define("BOT_TOKEN", '6327764045:AAFjJ5zDjVT_r7OfZa6Is9-Gel0zpZ-2a_E');
define("DEVELOPER_ID", 5456281641);
define("CHANNEL_ID", -1002310111830);
define("LOGGING_ID", -1002176139699);

$Bot = new Bot(BOT_TOKEN, [
    'parse_mode' => 'MarkdownV2',
    'debug' => LOGGING_ID
]);


function escapeMarkdown($text) {
    return str_replace(
        ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'],
        array_map(fn($c) => '\\' . $c, ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!']),
        $text
    );
}

$Bot->onCommand('give', function (Message $message, array $args = []) use ($Bot) {
    if (count($args) < 2) return $message->reply("⚠️ *Usage:* /give [user_id] [amount]", ['parse_mode' => 'Markdown']);
    [$id, $amount] = $args;

    if (!is_numeric($amount) || (int)$amount <= 0) {
        return $message->reply("❌ *Amount must be a positive number.*", ['parse_mode' => 'Markdown']);
    }

    $customer = new Customer($id);
    $old = $customer->getCredits();
    $amount = (int)$amount;
    $new_balance = $old + $amount;

    $customer->setCredits($new_balance); // simpan nilai baru

    $msg  = "🎁 *Balance Updated!*\n";
    $msg .= "━━━━━━━━━━━━━━━━\n";
    $msg .= "👤 *User*: `" . escapeMarkdown($id) . "`\n";
    $msg .= "💳 *Old Balance*: `" . number_format($old) . "`\n";
    $msg .= "📥 *Added*: `+" . number_format($amount) . "`\n";
    $msg .= "💰 *New Balance*: `" . number_format($new_balance) . "`\n";
    $msg .= "━━━━━━━━━━━━━━━━";
    $message->reply($msg, ['parse_mode' => 'Markdown']);
});

$Bot->onCommand('take', function (Message $message, array $args = []) use ($Bot) {
    if (count($args) < 2) {
        return $message->reply("⚠️ *Usage:* /take [user_id] [amount]", ['parse_mode' => 'Markdown']);
    }

    [$id, $amount] = $args;

    if (!is_numeric($amount) || (int)$amount <= 0) {
        return $message->reply("❌ *Amount must be a positive number.*", ['parse_mode' => 'Markdown']);
    }

    $customer = new Customer($id);
    $old = $customer->getCredits();
    $amount = (int)$amount;

    if ($old < $amount) {
        return $message->reply("🚫 *Insufficient balance.*", ['parse_mode' => 'Markdown']);
    }

    $new_balance = $old - $amount;
    $customer->setCredits($new_balance);

    // ⬅️ Ambil ulang agar getCredits() ambil data terbaru
    $customer = new Customer($id);
    $final_balance = $customer->getCredits();

    $msg  = "💸 *Balance Deducted!*\n";
    $msg .= "━━━━━━━━━━━━━━━━\n";
    $msg .= "👤 *User*: `" . escapeMarkdown($id) . "`\n";
    $msg .= "💳 *Old Balance*: `" . number_format($old) . "`\n";
    $msg .= "📤 *Deducted*: `-" . number_format($amount) . "`\n";
    $msg .= "💰 *New Balance*: `" . number_format($final_balance) . "`\n";
    $msg .= "━━━━━━━━━━━━━━━━";
    $message->reply($msg, ['parse_mode' => 'Markdown']);
});

$Bot->onCommand('block', function (Message $message, array $args = []) {
    if (empty($args[0])) return $message->reply("⚠️ *Usage:* /block [user_id]", ['parse_mode' => 'Markdown']);
    $id = $args[0];
    $customer = new Customer($id);

    if ($customer->getIsBlocked()) return $message->reply("❌ *User already blocked!*", ['parse_mode' => 'Markdown']);

    $customer->setIsBlocked(true);
    $message->reply("🚫 *User has been blocked.*", ['parse_mode' => 'Markdown']);
});

$Bot->onCommand('unblock', function (Message $message, array $args = []) {
    if (empty($args[0])) return $message->reply("⚠️ *Usage:* /unblock [user_id]", ['parse_mode' => 'Markdown']);
    $id = $args[0];
    $customer = new Customer($id);

    if (!$customer->getIsBlocked()) return $message->reply("✅ *User is not blocked.*", ['parse_mode' => 'Markdown']);

    $customer->setIsBlocked(false);
    $message->reply("🔓 *User has been unblocked.*", ['parse_mode' => 'Markdown']);
});

$Bot->onCommand('unlimited', function (Message $message, array $args = []) {
    if (empty($args[0])) return $message->reply("⚠️ *Usage:* /unlimited [user_id]", ['parse_mode' => 'Markdown']);
    $id = $args[0];
    $customer = new Customer($id);

    if ($customer->getIsUnlimited()) return $message->reply("⚡ *User already has unlimited access!*", ['parse_mode' => 'Markdown']);

    $customer->setIsUnlimited(true);
    $message->reply("🚀 *User now has unlimited access.*", ['parse_mode' => 'Markdown']);
});

$Bot->onCommand('limited', function (Message $message, array $args = []) {
    if (empty($args[0])) return $message->reply("⚠️ *Usage:* /limited [user_id]", ['parse_mode' => 'Markdown']);
    $id = $args[0];
    $customer = new Customer($id);

    if (!$customer->getIsUnlimited()) return $message->reply("📉 *User already limited.*", ['parse_mode' => 'Markdown']);

    $customer->setIsUnlimited(false);
    $message->reply("📉 *User is now limited.*", ['parse_mode' => 'Markdown']);
});

$Bot->onCommand('menu', function (Message $message) {
    $keyboard = [
        [
            ['text' => "Cek Saldo", 'callback_data' => "cek_saldo"],
            ['text' => "Give Saldo", 'callback_data' => "give_saldo"],
        ]
    ];

    $message->reply("📋 Pilih aksi:", [
        'reply_markup' => ['inline_keyboard' => $keyboard],
        'parse_mode' => 'Markdown'
    ]);
});

$Bot->onCallbackQuery(function (CallbackQuery $callback) {
    $data = $callback->data;
    $callback->answer("Diproses...");

    if ($data === "cek_saldo") {
        return $callback->message->reply("💼 Saldo kamu: 10.000");
    } elseif ($data === "give_saldo") {
        return $callback->message->reply("💸 Ketik: /give [id] [jumlah]");
    }
});

$Bot->run();