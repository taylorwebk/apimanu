<?php
namespace Models;
use Illuminate\Database\Eloquent\Model as Model;
/**
 *
 */
class Facturacion extends Model
{
  protected $guarded = array();
  protected $table = 'facturacion';
  public $timestamps = false;
  function reserva()
  {
    return $this->belongsTo('Models\Reserva');
  }
  function comision()
  {
    return $this->hasOne('Models\Comision');
  }
}
