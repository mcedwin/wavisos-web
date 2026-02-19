<?php

namespace App\Models;

use CodeIgniter\Model;

class PaisModel extends Model
{
    protected $table      = 'paises';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'nombre',
        'codigo_iso',
        'codigo_iso3',
        'slug'
    ];

    /* ===============================
       🔎 Buscar por código ISO
    =============================== */
    public function getByISO($iso)
    {
        return $this->where('codigo_iso', strtoupper($iso))->first();
    }

    /* ===============================
       🔎 Buscar por slug
    =============================== */
    public function getBySlug($slug)
    {
        return $this->where('slug', $slug)->first();
    }

    /* ===============================
       📋 Obtener todos activos
    =============================== */
    public function getAll()
    {
        return $this->orderBy('nombre', 'ASC')->findAll();
    }
}