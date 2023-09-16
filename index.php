<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$sub_folder = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);

spl_autoload_register(function ($class_name) {
  $src_path = explode('\\', $class_name);
  $final_class_name = array_pop($src_path);
  $path = join('/', $src_path);
  $class_root = __DIR__ . '/' . $path . '/' . $final_class_name . '.php';

  include $class_root;
});


// По умолчанию
$controller_name = '';
$action_name = '';

// Страницы
$json_routes = file_get_contents(__DIR__ . '/data/routes.json');
$routes = json_decode($json_routes, true);
$url_info = parse_url($_SERVER['REQUEST_URI']);

foreach ($routes as $row) {
  if ($url_info['path'] == $sub_folder . $row['route']) {
    $controller_name = $row['controllerClass'];
    $action_name = $row['controllerAction'];
  }
}

if (!preg_match("/^[\w]+$/", $controller_name)) {
  die ('incorrect controller name');
}

$controller_class = '\system\controllers\\' . $controller_name;

// Создать экземпляр и вывести представление

try {
  $controller = new $controller_class();
  echo $controller->call($action_name);
} catch (Exception $exception) {
  var_dump($exception->getMessage());
}
