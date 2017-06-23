<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Controllers\VehiculoController as VC;

$app->get('/', function (Request $req, Response $res)
{
  echo 'Hello world';
});
$app->post('/vehiculo', function (Request $req, Response $res)
{
  $result = VC::add($req->getParsedBody());
  return $res->withJson($result);
});
