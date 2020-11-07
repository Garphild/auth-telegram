<?php

namespace Garphild\AuthTelegram\Exceptions;

class OutdatedException extends \Exception {
  public function __construct()
  {
    parent::__construct('Data is outdated');
  }
}
