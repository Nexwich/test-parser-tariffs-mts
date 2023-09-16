<?php

namespace system\views;

use system\classes\view;

/**
 * Вывод шаблонами
 */
class template extends view {
  /**
   * Выводит шаблон
   * @param string $template_view
   * @param array $data
   */
  function generate (string $template_view, array $data = []) {
    if (is_array($data)) {
      extract($data);
    }

    require realpath(__DIR__ . '/../../') . '/templates/' . $template_view . '.php';
  }
}
