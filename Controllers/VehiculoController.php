<?php
namespace Controllers;
use Slim\Http\UploadedFile;
use Models\Vehiculo as Vehiculo;
use Models\Descuento as Descuento;
use Models\Reserva as Reserva;
use Models\Apartado as Apartado;
use Models\Facturacion as Facturacion;
use Models\Comision as Comision;
use Models\R as R;
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
      return R::success('Se adiciono correctamente el vehiculo');
    }
    else {
      return R::error('No se reconoce el/los campo(s): '.implode(', ', $fields));
    }
  }
  public static function getAll()
  {
    $res = Vehiculo::where('estado', '=', 0)->get();
    foreach ($res as &$v) {
      $des = $v->descuento()->select(['tipo', 'cantidad'])->first();
      if ($des['tipo'] == 'porcentaje') {
        $v->total = $v->precio*(100-$des['cantidad'])/100;
      } else {
        $v->total = $v->precio - $des['cantidad'];
      }
      $v->descuento = $v->descuento()->select(['tipo', 'cantidad'])->first();
    }
    return R::success($res);
  }
  public static function approve($data)
  {
      if (self::validateData($data, ['id'])) {
        $v = Vehiculo::find($data['id']);
        if ($v) {
          $v->estado = 1;
          $v->save();
          return R::success('Se aprobó el vehículo.');
        } else {
          return R::error('Vehiculo con id: '. $data['id']. ' no encontrado');
        }
      } else {
        return R::error('No se reconoce el/los campo(s): '.implode(', ', ['id']));
      }
  }
  public static function delete($data)
  {
      if (self::validateData($data, ['id'])) {
        $v = Vehiculo::find($data['id']);
        if ($v) {
          $v->delete();
          return R::success('Se eliminó el vehículo.');
        } else {
          return R::error('Vehiculo con id: '. $data['id']. ' no encontrado');
        }
      } else {
        return R::error('No se reconoce el/los campo(s): '.implode(', ', ['id']));
      }
  }
  public static function getApproved()
  {
    $res = Vehiculo::where('estado', '=', 1)->whereNotIn('id', Reserva::select('vehiculo_id')->get())->get();
    foreach ($res as &$v) {
      $des = $v->descuento()->select(['tipo', 'cantidad'])->first();
      if ($des['tipo'] == 'porcentaje') {
        $v->total = $v->precio*(100-$des['cantidad'])/100;
      } else {
        $v->total = $v->precio - $des['cantidad'];
      }
      $v->descuento = $v->descuento()->select(['tipo', 'cantidad'])->first();
    }
    return R::success($res);
  }
  public static function reserve($data)
  {
    $fields = ['id', 'comprador', 'vendedor'];
    if (self::validateData($data, $fields)) {
      $v = Vehiculo::where([
        ['id', '=', $data['id']],
        ['estado', '=', 1]
        ])->whereNotIn('id', Reserva::select('vehiculo_id')->get())->first();
      if ($v) {
        Reserva::create([
          'id' => null,
          'vehiculo_id' => $v->id,
          'comprador' => $data['comprador'],
          'vendedor' => $data['vendedor']
        ]);
        return R::success('Reserva exitosa, ahora puede proceder con los apartados');
      } else {
        return R::error('Vehiculo con id: '.$data['id'].' no aprobado, no encontrado o ya reservado');
      }
    } else {
      return R::error('No se reconoce el/los campo(s): '.implode(', ', $fields));
    }
  }
  public static function getReserves()
  {
    $vs = Vehiculo::with('reserva.apartados')->has('reserva')->get();
    $vs2 = $vs->filter(function ($v, $i) {
      $reservas = (Facturacion::select('reserva_id')->get())->toArray();
      // $rid = ($v->reserva()->select('id')->first())['id'];
      $rid = $v->reserva->id;
      // return false;
      foreach ($reservas as $reserva) {
        // var_dump($reserva);
        if ($reserva['reserva_id'] == $rid) {
          return false;
        }
      }
      return true;
    });
    return R::success($vs2->values());
  }
  public static function nuevoApartado($data, $files)
  {
    if (self::validateData($data, ['reserva_id', 'cantidad'])) {
      if (isset($files['comprobante'])) {
        if (Reserva::find($data['reserva_id'])) {
          $file = $files['comprobante'];
          $folder = PROJECTPATH.'/assets/apartados/';
          $filename = self::moveUploadedFile($folder, $file);
          Apartado::create([
            'id' => null,
            'reserva_id' => $data['reserva_id'],
            'cantidad' => $data['cantidad'],
            'urlImgComprobate' => IP.'/apimanu/assets/apartados/'.$filename,
            'fecha' => date('Y-m-d')
          ]);
          return R::success('Se creo el apartado');
        } else {
          return R::error('no existe el vehículo con id: '.$data['reserva_id'].' o no esta reservado');
        }
      } else {
        return R::error('No se reconoce el archivo: comprobante');
      }
    } else {
      return R::error('No se reconocen los campos: '.implode(', ', ['reserva_id', 'cantidad']));
    }
  }
  public static function invoicing($data)
  {
    $fields = ['vin', 'reserva_id'];
    if (self::validateData($data, $fields)) {
      $f = Facturacion::create([
        'reserva_id' => $data['reserva_id'],
        'vin' => $data['vin'],
        'ffact' => date('Y-m-d')
      ]);
      return R::success('Se facturó el vehículo');
    } else {
      return R::error('No se reconocen los campos: '.implode(', ', $fields));
    }
  }
  public static function invoices()
  {
    $res = Facturacion::with(['reserva.vehiculo.descuento','reserva.apartados','pagos'])->get();
    $res->transform(function ($r, $key)
    {
      $item = [];
      $item['id'] = $r->id;
      $item['vin'] = $r->vin;
      $item['ffact'] = $r->ffact;
      $item['modelo'] = $r->reserva->vehiculo->modelo;
      $item['vendedor'] = $r->reserva->vendedor;
      if ($r->reserva->vehiculo->descuento->tipo == 'porcentaje') {
        $precio = $r->reserva->vehiculo->precio*(100-$r->reserva->vehiculo->descuento->cantidad)/100;
      } else {
        $precio = $r->reserva->vehiculo->precio - $r->reserva->vehiculo->descuento->cantidad;
      }
      $totalApartados = 0;
      $totalPagos = 0;
      $totalApartados = $r->reserva->apartados->reduce(function ($val, $a) {
        return $val + $a->cantidad;
      }, 0);
      $totalPagos = $r->pagos->reduce(function ($val, $p)
      {
        return $val + $p->monto;
      }, 0);
      $item['precio'] = $precio;
      $item['totalApartados'] = $totalApartados;
      $item['totalPagos'] = $totalPagos;
      $item['precioRestante'] = $precio - $totalPagos - $totalApartados;
      return $item;
    });
    return $res;
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
  private static function moveUploadedFile($directory, UploadedFile $uploadedFile)
  {
      $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
      $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
      $filename = sprintf('%s.%0.8s', $basename, $extension);

      $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

      return $filename;
  }
}
