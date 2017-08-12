<?php
namespace Models;
use Illuminate\Database\Eloquent\Model as Model;
/**
 *
 */
class Reserva extends Model
{
  protected $guarded = array();
  protected $table = 'reserva';
  public $timestamps = false;
  function vehiculo()
  {
    return $this->belongsTo('Models\Vehiculo');
  }
  function apartados()
  {
    return $this->hasMany('Models\Apartado');
  }
  function facturacion()
  {
    return $this->hasOne('Models\Facturacion');
  }
}
