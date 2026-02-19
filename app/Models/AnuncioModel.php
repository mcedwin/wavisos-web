<?php

namespace App\Models;

use CodeIgniter\Model;

class AnuncioModel extends Model
{
    protected $table            = 'anuncios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'usuario_id',
        'categoria_id',
        'pais_id',
        'ciudad_id',
        'titulo',
        'descripcion',
        'precio',
        'tipo_precio',
        'slug',
        'destacado',
        'estado',
        'vistas',
        'fecha_expiracion'
    ];

    protected $useTimestamps = false;

    /* ============================================
       🔎 Obtener anuncios por ciudad
    ============================================ */

    public function getByCiudad($paisId, $ciudadId, $limit = 12, $offset = 0)
    {
        return $this->where('pais_id', $paisId)
                    ->where('ciudad_id', $ciudadId)
                    ->where('estado', 'activo')
                    ->orderBy('destacado', 'DESC')
                    ->orderBy('fecha_publicacion', 'DESC')
                    ->findAll($limit, $offset);
    }

    /* ============================================
       🔎 Obtener por slug
    ============================================ */

    public function getBySlug($slug)
    {
        return $this->where('slug', $slug)
                    ->where('estado', 'activo')
                    ->first();
    }

    /* ============================================
       🔎 Búsqueda por texto
    ============================================ */

    public function search($keyword, $paisId = null, $ciudadId = null)
    {
        $builder = $this->builder();

        $builder->like('titulo', $keyword)
                ->orLike('descripcion', $keyword)
                ->where('estado', 'activo');

        if ($paisId) {
            $builder->where('pais_id', $paisId);
        }

        if ($ciudadId) {
            $builder->where('ciudad_id', $ciudadId);
        }

        return $builder->orderBy('fecha_publicacion', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    /* ============================================
       👁 Incrementar vistas
    ============================================ */

    public function incrementarVistas($id)
    {
        return $this->set('vistas', 'vistas+1', false)
                    ->where('id', $id)
                    ->update();
    }

    /* ============================================
       ⭐ Marcar como destacado
    ============================================ */

    public function destacar($id)
    {
        return $this->update($id, [
            'destacado' => 1
        ]);
    }

    /* ============================================
       🧾 Crear slug automáticamente
    ============================================ */

    public function generarSlug($titulo)
    {
        $slug = url_title($titulo, '-', true);
        $slug .= '-' . uniqid();

        return $slug;
    }
}