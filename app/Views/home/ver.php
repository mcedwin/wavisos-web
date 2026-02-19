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


<div class="container-fluid pt-4">
    <div class="row">
        <div class="col-md-4 offset-md-4">
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
    </div>
</div>