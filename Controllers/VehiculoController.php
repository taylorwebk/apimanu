<?php
namespace Controllers;
use Slim\Http\UploadedFile;
use Models\Vehiculo as Vehiculo;
use Models\Descuento as Descuento;
use Models\Reserva as Reserva;
use Models\Apartado as Apartado;
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
        // if (Vehiculo::has('reserva')) {
        //   return R::error('El vehiculo ya fue reservado');
        // }
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
    $vs = Vehiculo::with('reserva')->has('reserva')->get();
    return R::success($vs);
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
            'urlImgComprobate' => $filename,
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
