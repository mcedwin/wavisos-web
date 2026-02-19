<?php

namespace App\Models;

use CodeIgniter\Model;

class RegionModel extends Model
{
    protected $table      = 'regiones';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'pais_id',
        'nombre',
        'slug',
        'codigo',
        'lat',
        'lng'
    ];

    /* ===============================
       🔎 Buscar región por slug
    =============================== */
    public function getBySlug($paisId, $slug)
    {
        return $this->where('pais_id', $paisId)
                    ->where('slug', $slug)
                    ->first();
    }

    /* ===============================
       📋 Obtener regiones por país
    =============================== */
    public function getByPais($paisId)
    {
        return $this->where('pais_id', $paisId)
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }
}