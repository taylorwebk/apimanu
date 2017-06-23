<?php
namespace Models;
use Illuminate\Database\Eloquent\Model as Model;

/**
 *
 */
class Vehiculo extends Model
{
  protected $table = 'vehiculo';
  public $timestamps = false;
  public function descuento()
  {
    return $this->hasOne('Models\Descuento');
  }
  public static function add($mod, $ag, $ext, $int, $eq, $fen, $p, $e)
  {
    $v = new Vehiculo();
    $v->modelo = $mod;
    $v->agencia = $ag;
    $v->exterior = $ext;
    $v->interior = $int;
    $v->equipo = $eq;
    $v->fentrega = $fen;
    $v->precio = $p;
    $v->estado = $e;
    $v->save();
    return $v;
  }
}
