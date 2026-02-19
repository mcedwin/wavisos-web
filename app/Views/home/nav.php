<style>
  .location-bar {
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    padding: 12px 0;
    font-size: 0.95rem;
  }

  .location-text strong {
    font-weight: 600;
  }

  .change-btn {
    font-size: 0.85rem;
  }
</style>

<!-- BARRA UBICACIÓN -->
<div class="location-bar mb-2">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="location-text">
      📍 Mostrando en:
      <strong id="currentLocation">
        <?php echo $ciudad ?>, <?php echo $region ?>, <?php echo $pais ?>
      </strong>
    </div>
    <button class="btn btn-sm btn-outline-secondary change-btn"
      data-bs-toggle="modal"
      data-bs-target="#locationModal">
      Cambiar
    </button>
  </div>
</div>

<!-- MODAL CAMBIAR UBICACIÓN -->
<div class="modal fade" id="locationModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar ubicación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        <div class="mb-3">
          <label class="form-label">País</label>
          <select id="paisSelect" class="form-select mb-3">
            <?php foreach ($paises as $pais): ?>
              <option value="<?= $pais['id'] ?>"
                <?= ($pais['id'] == $pais_id) ? 'selected' : '' ?>>
                <?= $pais['nombre'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Región</label>
          <select id="regionSelect" class="form-select mb-3">
            <?php foreach ($regiones as $region): ?>
              <option value="<?= $region['id'] ?>"
                <?= ($region['id'] == $region_id) ? 'selected' : '' ?>>
                <?= $region['nombre'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Ciudad</label>
          <select id="ciudadSelect" class="form-select">
            <?php foreach ($ciudades as $ciudad): ?>
              <option value="<?= $ciudad['id'] ?>"
                <?= ($ciudad['id'] == $ciudad_id) ? 'selected' : '' ?>>
                <?= $ciudad['nombre'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-success" onclick="guardarUbicacion()">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('paisSelect').addEventListener('change', function() {

    let paisId = this.value;

    fetch('<?php echo base_url() ?>api/regiones/' + paisId)
    .then(res => res.json())
    .then(data => {

        let regionSelect = document.getElementById('regionSelect');
        regionSelect.innerHTML = '';

        data.forEach(region => {
            regionSelect.innerHTML += 
            `<option value="${region.id}">${region.nombre}</option>`;
        });

        // cargar ciudades de primera región
        loadCiudades(data[0].id);
    });
});

document.getElementById('regionSelect').addEventListener('change', function() {
    loadCiudades(this.value);
});

function loadCiudades(regionId)
{
    fetch('<?php echo base_url() ?>api/ciudades/' + regionId)
    .then(res => res.json())
    .then(data => {

        let ciudadSelect = document.getElementById('ciudadSelect');
        ciudadSelect.innerHTML = '';

        data.forEach(ciudad => {
            ciudadSelect.innerHTML += 
            `<option value="${ciudad.id}">${ciudad.nombre}</option>`;
        });
    });
}

function guardarUbicacion() {

    let pais = document.getElementById('paisSelect').value;
    let region = document.getElementById('regionSelect').value;
    let ciudad = document.getElementById('ciudadSelect').value;

    window.location.href = '<?php echo base_url() ?>cambiar/' + pais + '/' + region + '/' + ciudad;
}
</script>


<div class="bg-white my-3 p-3 rounded shadow-sm">
  <form id="searchForm" action="<?php echo ($url) ?>" class="d-block" method="GET">
    <div class="input-group input-group">
      <input class="form-control " type="search" id="s" name="s" placeholder="Buscar" value="<?php echo $s; ?>">
      <button class="btn btn-primary" type="submit">
        <i class="fa fa-search"></i>
      </button>
    </div>
  </form>


  <style>
    .card-ad {
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
      border: 1px solid #e0f3ff;
    }

    .ad-image,
    .ad-placeholder {
      height: 180px;
    }

    .ad-image {
      object-fit: cover;
      width: 100%;
    }

    .ad-placeholder {
      background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      color: #9ca3af;
    }

    .ad-title {
      font-size: 0.95rem;
      font-weight: 600;
      line-height: 1.3;
      min-height: 48px;
      /* altura fija para 2 líneas */
    }

    .ad-price {
      font-weight: 700;
      color: #16a34a;
      font-size: 1.1rem;
    }

    .ad-meta {
      font-size: 0.8rem;
      color: #6b7280;
    }
  </style>

  <div class="content mt-3">
    <div class="row g-4">
      <?php foreach ($registros as $row) : ?>
        <?php
        $row = (object)$row;
        $url = base_url("{$controller}/item-" . $row->id);
        ?>


        <div class="col-md-4 d-flex">
          <div class="card card-ad shadow-sm h-100 d-flex flex-column w-100">

            <div class="ad-placeholder">
              📱
            </div>
            <!-- <img src="https://picsum.photos/401/300" class="ad-image"> -->

            <div class="card-body d-flex flex-column">
              <div class="ad-title">
                <?php echo $row->titulo; ?>
              </div>
              <p class="card-text small"><?php echo $row->descripcion; ?></p>
              <div class="ad-price mt-2" data-telf="<?php echo $row->precio ?>">
                Consultar
              </div>

              <div class="ad-meta my-2">
                📍 Bogotá, Colombia • <?php echo dateToUser(explode(" ", $row->fecha_publicacion)[0]) ?>
              </div>

              <a href="<?php echo base_url("item-{$row->id}") ?>" class="btn btn-success btn-sm mt-auto">
                Ver anuncio
              </a>
            </div>

          </div>
        </div>

      <?php endforeach; ?>
    </div>

    <!-- pagination blcok -->
    <div class="mt-3">
      <?php if ($pager) : ?>
        <?= $pager->links('grupo', 'bootstrap5_full') ?>
      <?php endif ?>
    </div>
  </div>
</div>