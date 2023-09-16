<?php

namespace system\classes;

/**
 * Контроллер
 */
abstract class controller {
  protected string $model_class;
  protected string $view_class;
  protected model $model;
  protected view $view;

  public function __construct () {
    $this->model = new $this->model_class();
    $this->view = new $this->view_class();
  }

  /**
   * Вызвать действие
   * @param string $action_name Название действия
   */
  public function call (string $action_name) {
    $action_name = 'action' . ucfirst($action_name);
    $this->$action_name();
  }

  /**
   * Получить объекты
   * @return array
   */
  protected function getItems (): array {
    $src_data = $this->model->getItems();
    $data = $src_data;

    if (!empty($_GET['page'])) {
      $data = [];

      $per_page = !empty($_GET['perPage']) ? $_GET['perPage'] : 2;
      $finish = $_GET['page'] * $per_page;
      $count = count($src_data);
      $start = $finish - $per_page;
      $finish = $finish <= $count ? $finish : $count;

      for ($i = $start; $i < $finish; $i += 1) {
        $data[] = $src_data[$i];
      }
    }

    return $data;
  }
}
