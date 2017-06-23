<?php
namespace Controllers;
use Models\Vehiculo as Vehiculo;
use Models\Descuento as Descuento;
use Models\R as R;
/**
 *
 */
class VehiculoController
{
  public static function add($data)
  {
    $fields = ['modelo', 'agencia', 'exterior', 'interior', 'equipo', 'fentrega', 'precio', 'tipo', 'monto'];
    if (self::validateData($data, $fields)) {
      if ($data['tipo'] != 'porcentaje' && $data['tipo'] != 'efectivo') {
        return R::error('Campo tipo solo puede tener el valor de: porcentaje o efectivo');
      }
      $v = Vehiculo::add(
        $data['modelo'],
        $data['agencia'],
        $data['exterior'],
        $data['interior'],
        $data['equipo'],
        $data['fentrega'],
        $data['precio'],
        0
      );
      $d = Descuento::create([
        'vehiculo_id' => $v->id,
        'cantidad' => $data['monto'],
        'tipo' => $data['tipo']
        ]);
      $v->descuento;
      return R::success('Se adiciono correctamente el vehiculo');
      // $v->descuento()->save($d);
      // return $v;
    }
    else {
      return R::error('No se reconocieron todos los campos');
    }
  }
  private static function validateData($data, $fields)
  {
    foreach ($fields as $value) {
      if (! isset($data[$value])) {
        return false;
      }
    }
    return true;
  }
}
