<?php

namespace system\fields;

use system\classes\field;

/**
 * Дата и время
 */
class datetime extends field {
  public function prepare (mixed $value) {
    $this->value = date('Y-m-d H:i:s', strtotime($value));
    return $this->value;
  }
}
