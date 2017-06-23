<?php
namespace Models;
use Illuminate\Database\Eloquent\Model as Model;
/**
 *
 */
class Descuento extends Model
{
  protected $guarded = array();
  protected $table = 'descuento';
  public $timestamps = false;
  function vehiculo()
  {
    return $this->belongsTo('Models\Vehiculo');
  }
}
