<?php

namespace system\classes;

use JetBrains\PhpStorm\Pure;

/**
 * Модель
 */
abstract class model implements \ArrayAccess {
  protected string $id = '_id'; // Название поля для идентификатора в хранилище
  protected array $fields = []; // Поля
  protected string $store;
  protected string $store_path;
  protected array $items = [];
  protected array $data = [];

  public function __construct () {
    $this->store_path = realpath(__DIR__ . '/../../') . '/data' . $this->store;
  }

  /**
   * Получить список полей
   * @return array
   */
  public function get_fields (): array {
    return $this->fields;
  }

  /**
   * Изменить значения полей
   * @param array $values Массив значений для установки
   * @return $this
   */
  public function set_values (array $values): model {
    foreach ($values as $name => $value) {
      $this->set_value($name, $value);
    }

    return $this;
  }

  /**
   * Изменить значение поля
   * @param string $name Название поля
   * @param mixed $value Значение поля
   * @return $this
   */
  public function set_value (string $name, mixed $value): model {
    $this->data[$name] = $value;
    return $this;
  }

  /**
   * Выбрать объект
   * @param string|int $id Id объекта
   * @return $this
   */
  public function load_by_id (string|int $id): model {
    $items = $this->getItems();

    foreach ($items as $item) {
      if ($item[$this->id] == $id) {
        $this->set_data($item);
      }
    }

    if (!$this->get_id()) {
      var_dump('Нет объекта');
      exit();
    }

    return $this;
  }

  /**
   * Получить список объектов
   * @return array
   */
  public function getItems (): array {
    if (!$this->items and $this->store) {
      $items = file_get_contents($this->store_path);
      $this->items = json_decode($items, true);
    }

    return ($this->items ?: []);
  }

  /**
   * Получить id выбранного объекта
   * @return string|int|bool
   */
  public function get_id (): bool|int|string {
    if (!empty($this->data[$this->id])) return $this->data[$this->id];
    return false;
  }

  /**
   * Сохранить объект
   * @return $this
   */
  public function save (): model {
    if (!empty($this->get_id())) {
      $this->update();
    }else {
      $this->insert();
    }

    return $this;
  }

  /**
   * Обновить объект
   * @return $this
   */
  public function update (): model {
    $items = $this->getItems();
    $save_data = [];

    foreach ($items as $item) {
      if ($item[$this->id] == $this->get_id()) {
        $save_data[] = $this->data;
      }else {
        $save_data[] = $item;
      }
    }

    $this->record($save_data);
    return $this;
  }

  /**
   * Записать объект в хранилище
   * @param array $data Входное значение
   * @return $this
   */
  public function record (array $data): model {
    if (file_exists($this->store_path)) {
      file_put_contents($this->store_path, json_encode($data));
    }

    return $this;
  }

  /**
   * Создать объект
   * @return $this
   */
  public function insert (): model {
    $items = $this->getItems();
    $save_data = $items;

    $this->set_value($this->id, md5(microtime()) . md5(rand()));
    array_push($save_data, $this->data);
    $this->record($save_data);

    return $this;
  }

  /**
   * Удалить выбранные объект
   * @return $this
   */
  public function remove (): model {
    $items = $this->getItems();
    $save_data = [];
    $id = $this->get_id();

    foreach ($items as $item) {
      if ($item[$this->id] == $id) {
        $this->set_data($item);
        $this->clear();
      }else {
        $save_data[] = $item;
      }
    }

    $this->record($save_data);

    return $this;
  }

  public function removeAll() {
    $this->record([]);
  }

  protected function clear () {
    $data = $this->get_data();

    foreach ($this->fields as $model_field) {
      $field = new $model_field['class']($model_field);
      $field->clear($data[$model_field['name']]);
    }
  }

  /**
   * Получить данные объекта
   * @return array
   */
  public function get_data (): array {
    return $this->data;
  }

  /**
   * Заменить значения
   * @param array $data Входное данные
   * @return $this
   */
  protected function set_data (array $data): model {
    $this->data = $data;
    return $this;
  }

  /**
   * Обработать объект по полям
   * @param array $data Входное данные
   * @return $this
   */
  public function prepare (array $data): model {
    foreach ($this->fields as $model_field) {
      $value = $data[$model_field['name']] ?? null;
      $current_value = $this->get($model_field['name']);

      if (!$value and $model_field['require'] and !$current_value) {
        die('Поле «' . $model_field['title'] . '» обязательно к заполнению');
      }

      if ($value === null and $current_value) {
        $this->set_value($model_field['name'], $current_value);
        continue;
      };

      $field = new $model_field['class']($model_field);
      $is = $field->prepare($value);

      if (!$is and $model_field['require']) {
        die('Поле «' . $model_field['title'] . '» неверно заполнено');
      }

      $this->set_value($field->get_name(), $field->get_value());
    }

    return $this;
  }

  /**
   * Задать значение
   * @param string $name ключ в массиве $this->data
   * @param mixed $value новое значение
   * @return self
   */
  public function set (string $name, mixed $value): static {
    $this->data[$name] = $value;

    return $this;
  }

  /**
   * Получить значение
   * @param string $name ключ в массиве $this->data
   * @return mixed значение
   */
  public function get (string $name): mixed {
    return $this->data[$name] ?? null;
  }

  /**
   * Проверка наличия свойства
   * @param string $name
   * @return boolean
   */
  public function has_property (string $name): bool {
    return array_key_exists($name, $this->data);
  }

  // ArrayAccess
  #[\ReturnTypeWillChange] public function offsetGet (mixed $offset) {
    return $this->get($offset);
  }

  #[\ReturnTypeWillChange] public function offsetSet (mixed $offset, $value) {
    $this->set($offset, $value);
  }

  #[Pure] public function offsetExists (mixed $offset): bool {
    return $this->has_property($offset);
  }

  #[\ReturnTypeWillChange] public function offsetUnset (mixed $offset) {
    unset($this->data[$offset]);
  }

  // Iterator
  public function rewind () {
    reset($this->data);
  }

  public function current () {
    return $this->offsetGet(key($this->data));
  }

  public function key (): int|string|null {
    return key($this->data);
  }

  public function next () {
    return next($this->data);
  }

  public function valid (): bool {
    return (key($this->data) !== null);
  }
}
