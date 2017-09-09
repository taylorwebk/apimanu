<?php
namespace Models;
use Illuminate\Database\Eloquent\Model as Model;
/**
 *
 */
class Factura extends Model
{
  protected $guarded = array();
  protected $table = 'factura';
  public $timestamps = false;
  function facturacion()
  {
    return $this->belongsTo('Models\Facturacion');
  }
}
