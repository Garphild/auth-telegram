<?php

use Garphild\AuthTelegram\TelegramAuthentificator;

require_once "./vendor/autoload.php";

define('BOT_USERNAME', 'gTests_bot'); // place username of your bot here
define('BOT_KEY', '1461964873:AAEECDqBNhp8HHsaNIcOsQ9BuXm0-0hbAmc'); // place username of your bot here
define('BOT_COOKIE_NAME', 'tg_user'); // place username of your bot here
$config = [
  'resultActionType' => 'url',
  'resultAction' => 'check_authorization.php',
];
$tgAuth = new TelegramAuthentificator(BOT_USERNAME, BOT_KEY, BOT_COOKIE_NAME, $config);

if ($_GET['logout']) {
  $tgAuth->logOut();
  exit;
}

if ($tgAuth->isAuthentificated()) {
  $first_name = htmlspecialchars($tgAuth->user->first_name);
  $last_name = htmlspecialchars($tgAuth->user->last_name);
  if ($tgAuth->user->username) {
    $username = htmlspecialchars($tgAuth->user->username);
    $html = "<h1>Hello, <a href=\"https://t.me/{$username}\">{$first_name} {$last_name}</a>!</h1>";
  } else {
    $html = "<h1>Hello, {$first_name} {$last_name}!</h1>";
  }
  if (isset($tgAuth->user->photo_url)) {
    $photo_url = htmlspecialchars($tgAuth->user->photo_url);
    $html .= "<img src=\"{$photo_url}\">";
  }
  $html .= "<p><a href=\"?logout=1\">Log out</a></p>";
} else {
    $html = $tgAuth->getWidget();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Login Widget Example</title>
    <script type="text/javascript">
        function onTelegramAuth(user) {
            console.log(user);
            alert('Logged in as ' + user.first_name + ' ' + user.last_name + ' (' + user.id + (user.username ? ', @' + user.username : '') + ')');
        }
    </script>
  </head>
  <body>
    <center>
      <h1>Hello, anonymous!</h1>
      <?php echo $html?>
    </center>
  </body>
</html>
