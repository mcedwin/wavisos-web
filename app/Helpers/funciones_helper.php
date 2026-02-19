<?php

function iniFields($fields, &$tfields)
{
    foreach ($fields as $reg) {
        if (!isset($tfields[$reg->name])) continue;
        $tfields[$reg->name]['type'] = isset($tfields[$reg->name]['type']) ? $tfields[$reg->name]['type'] : $reg->type;
        $tfields[$reg->name]['name'] = isset($tfields[$reg->name]['name']) ? $tfields[$reg->name]['name'] : $reg->name;
        $tfields[$reg->name]['max_length'] = $reg->max_length;
        $tfields[$reg->name]['value'] =  isset($tfields[$reg->name]['value']) ? $tfields[$reg->name]['value'] : '';
        $tfields[$reg->name]['required'] =  isset($tfields[$reg->name]['required']) ? $tfields[$reg->name]['required'] : true;
        $tfields[$reg->name]['valid'] =  isset($tfields[$reg->name]['valid']) ? $tfields[$reg->name]['valid'] : '';
        $tfields[$reg->name] = (object) $tfields[$reg->name];
    }
}

function resumen($contenido){
    return substr($contenido,0,255)."...";
}

function get_image($folder,$fname,$size){
    return base_url('uploads/'.$folder.'/' . str_replace('normal', $size, $fname));
}

function THS($arr)
{
    $str = "";
    foreach ($arr as $cod => $val) {
        if (!preg_match('/DT_/', $val['dt']))
            $str .= '<th class="ths">' . $val['dt'] . '</th>';
    }
    return $str;
}

function genDataTable($id, $columns, $withcheck = false, $responsive = false)
{
    if ($responsive) $class = "table table-striped table-bordered dt-responsive";
    else $class = "table table-striped table-bordered";
    return '<table id="' . $id . '" wch="' . $withcheck . '" cellpadding="0" cellspacing="0" border="0" width="100%" class="' . $class . '">
          <thead>
              <tr>
                  '  . THS($columns) . ($withcheck ? '<th></th>' : '') . '
              </tr>
          </thead>
      </table>';
}

function id_youtube($url) {
  $url = $url;
  parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
  return $my_array_of_vars['v']; 
}

function slugify($cadena, $separador="-"){
  $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $cadena);
  $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
  $slug = strtolower(trim($slug, $separador));
  $slug = preg_replace("/[\/_|+ -]+/", $separador, $slug);
  return $slug;
}

function slugify1(STRING $string, STRING $separator = '-'){
  $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
  $special_cases = [ '&' => 'and', "'" => ''];
  $string = mb_strtolower( trim( $string ), 'UTF-8' );
  $string = str_replace( array_keys($special_cases), array_values( $special_cases), $string );
  $string = preg_replace( $accents_regex, '$1', htmlentities( $string, ENT_QUOTES, 'UTF-8' ) );
  $string = preg_replace('/[^a-z0-9]/u', $separator, $string);
  return preg_replace('/['.$separator.']+/u', $separator, $string);
}


function tiempoTranscurrido($fecha) {
  // Crear objeto DateTime de la fecha dada y la fecha actual
  $fechaPublicacion = new DateTime($fecha);
  $fechaActual = new DateTime('now');
  
  // Calcular la diferencia
  $diferencia = $fechaActual->diff($fechaPublicacion);
  
  // Extraer las diferencias relevantes
  $segundos = (new DateTime('now'))->getTimestamp() - $fechaPublicacion->getTimestamp();
  $dias = $diferencia->days;
  $horas = floor($segundos / 3600);
  $minutos = floor($segundos / 60);
  
  // Determinar el formato de salida
  if ($segundos < 60) {
      return "Ahora";
  } elseif ($minutos < 60) {
      return "Hace $minutos minutos";
  } elseif ($horas < 24) {
      return "Hace $horas horas";
  } elseif ($dias < 7) {
      return "Hace $dias días";
  } else {
      $semanas = floor($dias / 7);
      return "Hace $semanas semanas";
  }
}