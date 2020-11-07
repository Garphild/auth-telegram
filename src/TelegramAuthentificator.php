<?php

namespace Garphild\AuthTelegram;

use Garphild\AuthTelegram\Models\TelegramUserModel;

class TelegramAuthentificator {
  protected $botName = '';
  protected $botKey = '';
  protected $cookieName = '';
  public $user = null;
  protected $size = 'large';
  protected $userPhoto = true;
  protected $cornerRadius = 14;
  protected $resultActionType = 'callback';
  protected $resultAction = '';
  protected $requestWrite = true;
  protected $async = true;

  function __construct($botName, $botKey, $cookieName, $config = []) {
    $this->botName = $botName;
    $this->botKey = $botKey;
    $this->cookieName = $cookieName;
    $user = $this->getTelegramUserData();
    if ($user) {
      $this->user = new TelegramUserModel($user);
    } else {
      $this->user = null;
    }
    $this->applyConfig($config);
  }

  function isAuthentificated() {
    return $this->user !== null;
  }

  protected function applyConfig($config) {
    if (isset($config['size'])) $this->size = $config['size'];
    if (isset($config['userPhoto'])) $this->userPhoto = $config['userPhoto'];
    if (isset($config['cornerRadius'])) $this->cornerRadius = $config['cornerRadius'];
    if (isset($config['resultAction'])) $this->resultAction = $config['resultAction'];
    if (isset($config['resultActionType'])) $this->resultActionType = $config['resultActionType'];
    if (isset($config['requestWrite'])) $this->requestWrite = $config['requestWrite'];
    if (isset($config['async'])) $this->async = $config['async'];
  }

  function getWidget() {
    if ($this->user !== null) {
      $html = "<h1>Hello, <a href=\"https://t.me/{$this->user->username}\">{$this->user->first_name} {$this->user->last_name}</a>!</h1>";
      if (isset($this->user->photo_url)) {
        $photo_url = htmlspecialchars($this->user->photo_url);
        $html .= "<img src=\"{$this->user->photo_url}\">";
      }
    } else {
      $params = [];
      if ($this->async) $params[] = 'async';
      $params[] = "src=\"https://telegram.org/js/telegram-widget.js?{$this->cornerRadius}\"";
      $params[] = "data-telegram-login=\"{$this->botName}\"";
      $params[] = "data-size=\"{$this->size}\"";
      switch($this->resultActionType) {
        case 'callback':
          $params[] = "data-onauth=\"{$this->resultAction}(user)\"";
          break;
        case 'url':
          $params[] = "data-auth-url=\"{$this->resultAction}\"";
          break;
        default:
      }
      if (!$this->userPhoto) $params[] = "data-userpic=\"false\"";
      if (!$this->requestWrite) $params[] = "data-request-access=\"write\"";
      $params = implode(" ", $params);
      $html = "<script {$params}></script>";
    }
    return $html;
  }

  protected function getTelegramUserData() {
    if (isset($_COOKIE[$this->cookieName])) {
      $auth_data_json = urldecode($_COOKIE[$this->cookieName]);
      $auth_data = json_decode($auth_data_json, true);
      $this->checkTelegramAuthorization($auth_data);
      return $auth_data;
    }
    return null;
  }

  function logOut() {
    setcookie($this->cookieName, '');
    $this->clearUser();
  }

  function setUser(TelegramUserModel $user) {
    $this->user = $user;
  }

  function clearUser() {
    $this->user = null;
  }

  function checkTelegramAuthorization($auth_data) {
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);
    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
      $data_check_arr[] = $key . '=' . $value;
    }
    sort($data_check_arr);
    $data_check_string = implode("\n", $data_check_arr);
    $secret_key = hash('sha256', $this->botKey, true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);
    if (strcmp($hash, $check_hash) !== 0) {
      throw new Exception('Data is NOT from Telegram');
    }
    if ((time() - $auth_data['auth_date']) > 86400) {
      throw new Exception('Data is outdated');
    }
    return $auth_data;
  }
}
