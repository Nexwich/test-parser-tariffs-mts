<?php

namespace system\fields;

use system\classes\field;

/**
 * Файл
 */
class file extends field {
  public function prepare (mixed $value) {
    $this->value = $this->file_upload($value);
    return $this->value;
  }

  /**
   * Сохранить файл
   * @param $value
   * @return string Путь к файлу
   */
  protected function file_upload ($value): string {
    if (!empty($_FILES[$this->name]['name'])) {
      $path_parts = pathinfo($_FILES[$this->name]['name']);
      $file_name = md5(microtime() . $_FILES[$this->name]['name']) . '.' . $path_parts['extension'];
      $file_save_root = __DIR__ . '/../../files/' . $file_name;

      if (!move_uploaded_file($_FILES[$this->name]['tmp_name'], $file_save_root)) {
        die('Ошибка загрузки файла');
      }

      return __DIR__ . '/../../files/' . $file_name;
    }elseif (!empty($value)) {
      $file = file_get_contents($value);
      $path_parts = pathinfo($value);
      $file_name = md5(microtime() . $value) . '.' . $path_parts['extension'];
      $file_save_root = __DIR__ . '/../../files/' . $file_name;

      file_put_contents($file_save_root, $file);

      return '/files/' . $file_name;
    }

    return '';
  }

  public function clear ($data) {
    $file_root = __DIR__ . '/../dist/' . $data;
    if (file_exists($file_root)) unlink($file_root);
  }
}
