<?php

namespace system\models;

use system\classes\model;
use system\fields\text;

class tariff extends model {
  public string $store = '/tariffs.json';
  protected array $fields = [
    [
      'name' => 'title',
      'title' => 'Название',
      'class' => text::class,
      'require' => true,
    ],
    [
      'name' => 'categoriesIds',
      'title' => 'Категории',
      'class' => text::class,
      'require' => true,
    ],
  ];

  /**
   * Получить актуальные тарифы по поселению
   * @param string $subdomain Поддомен
   * @param int $settlement_map_id ID поселения (mapId)
   * @return array
   */
  public function parseSettlement (string $subdomain, int $settlement_map_id): array {
    $contents = file_get_contents('https://' . $subdomain
      . '.mts.ru/json/homeTariffs/universal-cards/5396058/'
      . $settlement_map_id
    );
    return json_decode($contents, true);
  }
}
