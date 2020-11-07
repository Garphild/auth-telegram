<?php

namespace Garphild\AuthTelegram;

use Garphild\AuthTelegram\Exceptions\OutdatedException;
use Garphild\AuthTelegram\Exceptions\UnknownSourceException;
use Garphild\AuthTelegram\Models\TelegramUserModel;

class TelegramAuthentificator {
  protected $botName = '';
  protected $botKey = '';
  public $user = null;
  protected $size = 'large';
  protected $userPhoto = true;
  protected $cornerRadius = 14;
  protected $resultActionType = 'callback';
  protected $resultAction = '';
  protected $requestWrite = true;
  protected $async = true;
  protected $maxAge = 86400;

  function __construct($botName, $botKey, $config = []) {
    if ($botName === 'XXXXXXX') new \Exception("Bot name must be correct");
    if (empty($botName)) new \Exception("Bot name must not be empty");
    if ($botKey === 'XXXXXXX:XXXXXXX') new \Exception("Bot token must be correct");
    if (empty($botKey)) new \Exception("Bot token must not be empty");
    $this->botName = $botName;
    $this->botKey = $botKey;
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
    if (isset($config['maxAge'])) $this->maxAge = $config['maxAge'];
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

  function logIn($data) {
    $auth_data = $this->checkTelegramAuthorization($_GET);
    $this->setUser(new TelegramUserModel($auth_data));
  }

  function logOut() {
    $this->clearUser();
  }

  function setUser(TelegramUserModel $user) {
    $this->user = $user;
  }

  function clearUser() {
    $this->user = null;
  }

  function checkTelegramAuthorization($auth) {
    $check_hash = $auth['hash'];
    $auth_data = $auth;
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
      throw new UnknownSourceException();
    }
    if ((time() - $auth_data['auth_date']) > $this->maxAge) {
      throw new OutdatedException();
    }
    return $auth_data;
  }
}
