<?php

namespace system\traits;

trait default_actions {
  protected string $template = 'json.php';

  /**
   * Показать список
   */
  protected function action_get () {
    $data = parent::get_items();
    $this->view->generate($this->template, ['data' => $data]);
  }

  /**
   * Добавить запись
   */
  protected function action_insert () {
    if ($_POST) {
      $model = new $this->model_class();

      $model->prepare(!empty($_POST['data']) ? json_decode($_POST['data'], true) : $_POST);
      $model->save();
      $this->view->generate($this->template, ['data' => $model->get_data()]);
    }
  }

  /**
   * Обновить запись
   */
  protected function action_update () {
    if ($_POST) {
      $data = !empty($_POST['data']) ? json_decode($_POST['data'], true) : $_POST;
      $model = new $this->model_class();

      $model->load_by_id($data['_id']);
      $model->prepare($data);
      $model->save();
      $this->view->generate($this->template, ['data' => $model->get_data()]);
    }
  }

  /**
   * Удалить запись
   */
  protected function action_delete () {
    if ($_POST) {
      $data = !empty($_POST['data']) ? json_decode($_POST['data'], true) : $_POST;
      $model = new $this->model_class();

      $model->load_by_id($data['_id']);
      $model->remove();
      $this->view->generate($this->template, ['data' => $model->get_data()]);
    }
  }
}
