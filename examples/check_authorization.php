<?php

use Garphild\AuthTelegram\TelegramAuthentificator;

require_once "../vendor/autoload.php";

if (file_exists("./env.php")) {
  require_once "./env.php";
} else {
  define('BOT_USERNAME', 'XXXXXXX'); // place username of your bot here
  define('BOT_KEY', 'XXXXXX:XXXXXXX'); // place username of your bot here
  define('BOT_COOKIE_NAME', 'XXXXXX'); // place username of your bot here
}
$config = [
  'resultActionType' => 'url',
  'resultAction' => 'check_authorization.php',
];
$tgAuth = new TelegramAuthentificator(BOT_USERNAME, BOT_KEY, BOT_COOKIE_NAME, $config);

try {
  $tgAuth->logIn($_GET);
} catch (Exception $e) {
  die ($e->getMessage());
}

header('Location: login_example.php');

?>
