<?php
namespace Models;
/**
 *
 */
class R
{
  
  public static function error($mensaje)
  {
    $data['status'] = 'error';
    $data['content'] = $mensaje;
    return $data;
  }
  public static function success($content)
  {
    $data['status'] = "success";
    $data['content'] = $content;
    return $data;
  }
  public static function warning($mensaje)
  {
    $data['status'] = "warning";
    $data['content'] = $mensaje;
    return $data;
  }
  public static function simpleToken($status, $content, $token)
  {
    return array('status' => $status, 'content' => $content, 'token' => $token);
  }
}
