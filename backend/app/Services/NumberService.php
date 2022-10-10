<?php

namespace App\Services;

class NumberService {

  private $value;

  public function __construct(int $value) {
    $this->value = $value;
  }

  public function GetNumber():int {
    return $this->value;
  }
}