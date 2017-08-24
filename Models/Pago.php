<?php
namespace Models;
use Illuminate\Database\Eloquent\Model as Model;
/**
 *
 */
class Pago extends Model
{
  protected $guarded = array();
  protected $table = 'pago';
  public $timestamps = false;
  function reserva()
  {
    return $this->belongsTo('Models\Facturacion');
  }
}
