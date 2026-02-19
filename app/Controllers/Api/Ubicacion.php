<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\RegionModel;
use App\Models\CiudadModel;

class Ubicacion extends BaseController
{
    public function regiones($paisId)
    {
        $model = new RegionModel();
        return $this->response->setJSON(
            $model->getByPais($paisId)
        );
    }

    public function ciudades($regionId)
    {
        $model = new CiudadModel();
        return $this->response->setJSON(
            $model->getByRegion($regionId)
        );
    }
}