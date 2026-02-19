<?php namespace App\Models;
use CodeIgniter\Model;
class DirectorioModel extends Model {
  protected $table = 'directorio';
  protected $primaryKey = 'id';
  protected $allowedFields = ['numero','titulo','contenido','fechareg','tipo','idCate','idSCate','fechareg','fechapub','fechafin'];
  public $timestamps = false;
}
