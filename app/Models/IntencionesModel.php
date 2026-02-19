<?php namespace App\Models;
use CodeIgniter\Model;

class IntencionesModel extends Model {
  protected $table = 'intenciones';
  protected $primaryKey = 'id';
  protected $allowedFields = ['numero','intencion','datos','estado'];
  public $timestamps = false;

  public function obtenerPendiente($num){
    $r = $this->where('numero',$num)
              ->where('estado','pendiente')
              ->orderBy('id','desc')->first();
    if($r){ 
		//file_put_contents("log.txt", $r['datos']."-\r\n", FILE_APPEND | LOCK_EX);
		$r['datos']=json_decode($r['datos'],true); 
		}
    return $r;
  }
  public function crear($num,$int,$datos){
    return $this->insert(['numero'=>$num,'intencion'=>$int,'datos'=>json_encode($datos),'estado'=>'pendiente']);
  }
  public function agregarDatos($pend,$nuevos){
    $datos = array_merge($pend['datos']??[], $nuevos);
    $this->update($pend['id'],['datos'=>json_encode($datos)]);
    $pend['datos']=$datos;
    return $pend;
  }
  public function marcarCompleto($id){ return $this->update($id,['estado'=>'completado']); }
  public function cancelarPendiente($num){ return $this->where('numero',$num)->where('estado','pendiente')->set(['estado'=>'cancelado'])->update(); }
}
