<?php

namespace App\Models;

use CodeIgniter\Model;

class GeneralModel extends Model
{
  public $fields;
  protected $returnType     = 'object';

  public function __construct($table)
  {
    $datas['directorio'] = ['table' => 'directorio', 'primary' => 'id', 'fields' => [
      'titulo' => array('label' => 'Título', 'required' => true),
      'contenido' => array('label' => 'Description', 'required' => true),
      'numero' => array('label' => 'Teléfono', 'required' => true),
      'idCate' => array('label' => 'Categoría'),
      'fechareg' => array('label' => 'Fecha Reg', 'required' => false),
      'sf_date' => array('label' => 'Fecha', 'required' => false),
      'sf_hash' => array('label' => 'Hash', 'required' => false),
      'sf_original' => array('label' => 'Original', 'required' => false),
      'fechapub' => array('label' => 'Fecha Pub', 'required' => false),
      'fechafin' => array('label' => 'Fecha Fin', 'required' => false),
    ]];


    extract($datas[$table]);

    $this->table = $table;
    $this->fields = $fields;
    $this->primaryKey = $primary;

    parent::__construct();
  }

  public function getTable()
  {
    return $this->table;
  }
  public function getPrimaryKey()
  {
    return $this->primaryKey;
  }

  protected function initialize()
  {
    helper('funciones');

    $dfields = $this->db->getFieldData($this->table);
    iniFields($dfields, $this->fields);

    foreach ($this->fields as $field) {
      $this->allowedFields[] = $field->name;
    }
  }

  function getFields()
  {
    return $this->fields;
  }

  function geti($id = '')
  {
    $builder = $this->db->table($this->table);

    if (!empty($id)) {
      $row = $builder->select()->where($this->primaryKey, $id)->get()->getRow();
      foreach ($row as $k => $value) {
        if (!isset($this->fields[$k])) continue;
        $this->fields[$k]->value =  $value;
      }
    }

    return (object)$this->fields;
  }

  function enum_valores($campo)
  {
    $consulta = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE '$campo'");
    if ($consulta->getNumRows() > 0) {
      $consulta = $consulta->getRow();
      $array = explode(",", str_replace(array("enum", "'", "(", ")"), "", $consulta->Type));
      foreach ($array as $key) {
        $array2[] = (object)array('id' => $key, 'text' => $key);
      }
      return $array2;
    } else {
      return FALSE;
    }
  }

  public function getPaginadas($limit, $offset,$tipo='')
  {
    if($tipo=='noticias'||$tipo=='campanias')
    {
      return $this->orderBy('orden', 'ASC')
      ->findAll($limit, $offset);
    }
    return $this->orderBy('id', 'DESC')
      ->findAll($limit, $offset);
  }
}
