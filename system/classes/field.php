<?php

namespace system\classes;

abstract class field {
  protected string $name;
  protected mixed $value;
  protected array $settings;

  /**
   * @param array $field Данные по полю
   */
  public function __construct (array $field) {
    $this->settings = $field;
    $this->name = $field['name'];
  }

  public function get_name (): string {
    return $this->name;
  }

  public function get_value () {
    return $this->value;
  }

  /**
   * Обработать данные
   * @param mixed $value Входное значение
   * @return string
   */
  public function prepare (mixed $value): string {
    $this->value = $value;
    return $this->value;
  }

  public function clear ($data) {
  }
}
