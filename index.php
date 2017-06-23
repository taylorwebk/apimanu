<?php
require_once 'vendor/autoload.php';
define("PROJECTPATH", __DIR__);
define("IP", $_SERVER['SERVER_NAME']);

function autoloader($className)
{
  $filename = PROJECTPATH . '/' . str_replace('\\', '/', $className) . '.php';
  if (is_file($filename)) {
    include_once($filename);
  }
}
spl_autoload_register('autoloader');
$config = parse_ini_file(PROJECTPATH . '/Database/config.db');

$conf = [
  'settings' => [
    'displayErrorDetails' => true,
    'db' => [
      'driver'  => 'mysql',
      'host'    => $config['host'],
      'database'=> $config['database'],
      'username'=> $config['user'],
      'password'=> $config['password'],
      'charset' => 'utf8',
      'collation'=>'utf8_unicode_ci',
      'prefix'  => '',
      ]
  ],
];
$c = new Slim\Container($conf);
/*$c['errorHandler'] = function ($c) {
  return function ($request, $response, $exception) use ($c) {
    $data;
    $data['status'] = "error";
    $data['content'] = "Fatal Error.... Unknow Error";
    return $c['response']->withJson($data);
  };
};*/
$app = new Slim\App($c);

$container = $app->getContainer();
$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container->get('settings')['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
include_once (PROJECTPATH.'/Routes/Routes.php');
$app->run();
?>
