<?php

namespace Garphild\AuthTelegram\Exceptions;

class UnknownSourceException extends \Exception {
  public function __construct()
  {
    parent::__construct('Data is NOT from Telegram');
  }
}
