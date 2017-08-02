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
$app->get('/vehiculos', function (Request $req, Response $res)
{
  $result = VC::getAll();
  return $res->withJson($result);
});
$app->post('/aprobar', function (Request $req, Response $res)
{
  $result = VC::approve($req->getParsedBody());
  return $res->withJson($result);
});
$app->delete('/vehiculo', function (Request $req, Response $res)
{
  $result = VC::delete($req->getParsedBody());
  return $res->withJson($result);
});
$app->get('/aprobados', function (Request $req, Response $res)
{
  $result = VC::getApproved();
  return $res->withJson($result);
});
$app->post('/reservar', function (Request $req, Response $res)
{
  $result = VC::reserve($req->getParsedBody());
  return $res->withJson($result);
});
$app->get('/reservas', function (Request $req, Response $res)
{
  $result = VC::getReserves();
  return $res->withJson($result);
});
$app->post('/apartado', function (Request $req, Response $res)
{
  $result = VC::nuevoApartado($req->getParsedBody(), $req->getUploadedFiles());
  return $res->withJson($result);
});
