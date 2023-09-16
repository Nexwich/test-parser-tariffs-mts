<?php

namespace system\controllers;

use system\classes\controller;
use system\models\category;
use system\models\offer;
use system\views\template;

/**
 * Тарифы
 */
class tariff extends controller {
  public string $model_class = \system\models\tariff::class;
  public string $view_class = template::class;

  /**
   * Показать список
   */
  protected function actionIndex () {
    $category = new category();
    $offer = new offer();
    $tariffs = $this->model->getItems();
    $data_categories = $category->getItems();
    $data_offers = $offer->getItems();
    $search = $_GET['search'] ?? [];
    $sort = $_GET['sort'] ?? 'title_ask';

    // Варианты сортировки
    $sorts = [[
      'id' => 'title_ask',
      'title' => 'По названию от А до Я',
    ], [
      'id' => 'title_desk',
      'title' => 'По названию от Я до А',
    ]];

    // Собрать предложения по тарифам
    foreach ($tariffs as $key => $tariff) {
      foreach ($data_offers as $data_offer) {
        if ($data_offer['tariffId'] != $tariff['id']) continue;

        $tariffs[$key]['offers'][] = $data_offer;
      }
    }

    // Фильтровать тарифы
    $tariffs = array_filter($tariffs, function ($element) use ($search) {
      return empty($search['categoryId']) or in_array($search['categoryId'],
          $element['categoriesIds']
        );
    });

    // Сортировать тарифы
    usort($tariffs, function ($a, $b) use ($sort) {
      return match ($sort) {
        'title_desk' => $b['title'] <=> $a['title'],
        default => $a['title'] <=> $b['title'],
      };
    });

    $this->view->generate('main', [
      'tariffs' => $tariffs,
      'categories' => $data_categories,
      'sorts' => $sorts,
      'offers' => $data_offers,
    ]);
  }

  /**
   * Парсить
   */
  protected function actionParse () {
    $tariffs = [];
    $offers = [];
    $categories = [];
    $category = new category();
    $offer = new offer();

    // Получить общие тарифы (они не встречаются в АПИ по поселениям)
    $contents = file_get_contents('https://spb.mts.ru/personal/mobilnaya-svyaz/tarifi/vse-tarifi');

    // Выбрать со страницы подходящие данные
    $re = '/window\s*\.\s*globalSettings\s*\.\s*tariffs\s*=\s*\{.+?\}.+?<\/\s*script>/m';
    $str = str_replace(["\r", "\n", "\t", "\f", "\v",], '', $contents);

    preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

    // Оставить только объект
    $re = '/\{.*\}/m';
    $str = $matches[0][0];

    preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

    $data_init_tariffs = json_decode($matches[0][0], true);

    // Собрать категории
    foreach ($data_init_tariffs['catalogMenuItems'] as $data_tariff) {
      $categories[] = [
        'id' => $data_tariff['id'],
        'title' => preg_replace('/\<br\s*\/?\>/m', ' ', $data_tariff['title']),
      ];
    }

    // Записать
    $category->removeAll();
    $category->record($categories);

    // Если потребуется обойти все тарифы компании в этом случае
    // Получить регионы (https://moskva.mts.ru/json/region/list/)
    // Получить список поддоменов сайта МТС (разработать метод)
    // Получить поселения региона (https://${subdomain}.mts.ru/json/region/list/${region.mapId})

    // Получить тарифы поселения (Москва) (Тарифы связанны с поддоменом)
    $data_settlement_tariffs = $this->model->parseSettlement('moskva', 20063);
    $data_tariffs = array_merge($data_init_tariffs['actualTariffs'], $data_settlement_tariffs);

    foreach ($data_tariffs as $data_tariff) {
      $tariffs[$data_tariff['id']] = [
        'id' => $data_tariff['id'],
        'title' => $data_tariff['title'],
        'type' => $data_tariff['tariffType'],
        'categoriesIds' => $data_tariff['categoriesIds'],
      ];

      // Если есть пакеты
      if (!empty($data_tariff['configurableTariffSettings']['packages'])) {
        foreach ($data_tariff['configurableTariffSettings']['packages'] as $package) {
          $subscription = $package['subscriptionFee'];

          $offers[] = [
            'id' => $package['id'],
            'title' => $subscription['title'],
            'price' => $subscription['numValue'],
            'tariffId' => $data_tariff['id'],
          ];
        }
      }

      // Тариф по подписке
      if (!empty($data_tariff['subscriptionFee']['numValue'])) {
        $subscription = $data_tariff['subscriptionFee'];

        $offers[] = [
          'id' => $data_tariff['id'] . '_0',
          'title' => $subscription['title'] ?? 'Подписка',
          'price' => $subscription['numValue'],
          'tariffId' => $data_tariff['id'],
        ];
      }

      // Предложения
      $data_offers = !empty($data_tariff['homeTariffSettings'])
        ? $data_tariff['homeTariffSettings']['familyOffers'] : null;
      if (!$data_offers) $data_offers = !empty($data_tariff['convergentTariffSettings'])
        ? $data_tariff['convergentTariffSettings']['family']['offers'] : null;

      if (!empty($data_offers)) {
        foreach ($data_offers as $data_offer) {
          $offers[] = [
            'id' => $data_offer['id'],
            'title' => $data_offer['title'],
            'price' => $data_offer['totalPrice']['value'],
            'tariffId' => $data_tariff['id'],
          ];
        }
      }
    }

    $tariffs = array_values($tariffs);

    // Записать тарифы
    $this->model->removeAll();
    $this->model->record($tariffs);

    // Записать предложения
    $offer->removeAll();
    $offer->record($offers);

    // Вывод
    $this->view->generate('json', ['data' => $offers]);
  }

  /**
   * Очистить
   */
  protected function actionClear() {
    $category = new category();
    $offer = new offer();

    $category->record([]);
    $offer->record([]);
    $this->model->record([]);

    // Вывод
    $this->view->generate('json', ['data' => []]);
  }
}
