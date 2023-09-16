<?php

namespace system\models;

use system\classes\model;
use system\fields\text;

class offer extends model {
  public string $store = '/offers.json';
  protected array $fields = [
    [
      'name' => 'title',
      'title' => 'Название',
      'class' => text::class,
      'require' => true,
    ],
    [
      'name' => 'price',
      'title' => 'Цена',
      'class' => text::class,
      'require' => true,
    ],
    [
      'name' => 'tariffId',
      'title' => 'Тариф',
      'class' => text::class,
      'require' => true,
    ],
  ];
}
