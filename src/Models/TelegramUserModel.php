<?php

namespace Garphild\AuthTelegram\Models;

class TelegramUserModel {
  public $first_name = null;
  public $last_name = null;
  public $username = null;
  public $photo_url = null;
  public $auth_date = null;
  public $id = null;
  public $hash = null;

  function __construct($user) {
    $this->first_name = isset($user['first_name']) ? htmlspecialchars($user['first_name']) : null;
    $this->last_name = isset($user['last_name']) ? htmlspecialchars($user['last_name']) : null;
    $this->username = isset($user['username']) ? htmlspecialchars($user['username']) : null;
    $this->photo_url = isset($user['photo_url']) ? htmlspecialchars($user['photo_url']) : null;
    $this->auth_date = isset($user['auth_date']) ? htmlspecialchars($user['auth_date']) : null;
    $this->id = isset($user['id']) ? htmlspecialchars($user['id']) : null;
    $this->hash = isset($user['hash']) ? htmlspecialchars($user['hash']) : null;
  }
}
