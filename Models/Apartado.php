<?php
namespace Models;
use Illuminate\Database\Eloquent\Model as Model;
/**
 *
 */
class Apartado extends Model
{
  protected $guarded = array();
  // protected $table = 'reserva';
  public $timestamps = false;
  function reserva()
  {
    return $this->belongsTo('Models\Reserva');
  }
}
