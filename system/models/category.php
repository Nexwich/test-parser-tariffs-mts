<?php

namespace system\models;

use system\classes\model;
use system\fields\text;

class category extends model {
  public string $store = '/categories.json';
  protected array $fields = [
    [
      'name' => 'title',
      'title' => 'Название',
      'class' => text::class,
      'require' => true,
    ],
  ];
}
