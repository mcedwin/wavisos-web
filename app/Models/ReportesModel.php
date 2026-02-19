<?php namespace App\Models;
use CodeIgniter\Model;
class ReportesModel extends Model {
  protected $table = 'reportes';
  protected $primaryKey = 'id';
  protected $allowedFields = ['numero','descripcion','foto','fecha'];
  public $timestamps = false;
}
