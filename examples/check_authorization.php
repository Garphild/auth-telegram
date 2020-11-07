<?php

define('BOT_TOKEN', '1461964873:AAEECDqBNhp8HHsaNIcOsQ9BuXm0-0hbAmc'); // place bot token of your bot here

function saveTelegramUserData($auth_data) {
  $auth_data_json = json_encode($auth_data);
  setcookie('tg_user', $auth_data_json);
}


try {
  $auth_data = checkTelegramAuthorization($_GET);
  saveTelegramUserData($auth_data);
} catch (Exception $e) {
  die ($e->getMessage());
}

header('Location: login_example.php');

?>
