<?php

/**
 * require_onceなどで呼び出す部分をルートからのパスに
 */
define("ROOT", realpath(__DIR__ . "/../"));
ini_set('include_path', get_include_path() . PATH_SEPARATOR . ROOT);

require_once(ROOT . "/vendor/autoload.php");

/**
 * 設定ファイルの読み込み
 */
define("CONFIG", require_once("config/config.php"));

/**
 * URL
 */
$protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
$host = $_SERVER['HTTP_HOST'];
$path = $_SERVER['REQUEST_URI'];
define("PUBLIC_URL", $protocol . $host . $path);


/**
 * ルーティング
 */
$router = new AltoRouter();

$router->setBasePath('/Baby-Base/public/');

$router->map( 'GET', '', 'HTTP\Controller\WelcomController::get', 'welcom' );
$router->map( 'GET', 'signup', function() {
    require ROOT . "/view/signup.php";
});
$router->map( 'GET', 'users/[i:id]', 'HTTP\Controller\UserController::get', 'users-detail' );

$match = $router->match();

if ($match !== false) {
  if (is_callable($match['target'])) {
      $match['target']();
  } else {
      $params = explode("::", $match['target']);
      $action = new $params[0]();
      call_user_func_array(array($action, $params[1]), $match['params']);
  }
} else {
  header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}