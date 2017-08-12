<?php
namespace Models;
use Illuminate\Database\Eloquent\Model as Model;
/**
 *
 */
class Comision extends Model
{
  protected $guarded = array();
  protected $table = 'comision';
  public $timestamps = false;
  function facturacion()
  {
    return $this->belongsTo('Models\Facturacion');
  }
}
