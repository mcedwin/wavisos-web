<?php

namespace App\Models;

use CodeIgniter\Model;

class CiudadModel extends Model
{
    protected $table      = 'ciudades';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'pais_id',
        'region_id',
        'nombre',
        'slug',
        'lat',
        'lng',
        'poblacion'
    ];

    /* ===============================
       🔎 Buscar ciudad por slug
    =============================== */
    public function getBySlug($regionId, $slug)
    {
        return $this->where('region_id', $regionId)
                    ->where('slug', $slug)
                    ->first();
    }

    /* ===============================
       📋 Obtener ciudades por región
    =============================== */
    public function getByRegion($regionId)
    {
        return $this->where('region_id', $regionId)
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }

    /* ===============================
       🔎 Buscar ciudad por nombre exacto
    =============================== */
    public function getByNombre($nombre)
    {
        return $this->where('nombre', $nombre)->first();
    }
}