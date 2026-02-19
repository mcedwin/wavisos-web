<div class="container">
  <div class="card mb-3">
    <div class="card-body categorias">
      <div class="row">
        <div class="col-md-6">
          <ul class="list-unstyled ps-0 mt-2">
            <?php foreach ($cates as $index => $cate) : ?>
              <li class="mb-1">
                <a class="cate-title" href="<?php echo base_url("{$cate->cate_ini}") ?>">
                  <i class="<?php echo $cate->cate_icono ?>"></i> <?php echo $cate->cate_nombre; ?>
                </a>
                <div class="">
                  <ul class="list-inline list-subc small ms-3">
                    <?php foreach ($cate->subcates as $subc) : ?>
                      <li class="list-inline-item">
                        <a href="<?php echo base_url("/{$cate->cate_ini}/{$subc->subc_ini}") ?>" class=""><?php echo $subc->subc_nombre; ?></a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </li>
              <?php if ($index == 3) echo '</ul></div><div class="col-md-6"><ul class="list-unstyled ps-0 mt-2">';  ?>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">

  <div class="col-md-3">

    <div class="card">
      <div class="card-header">
        Featured
      </div>

      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
          <a href="#" class="nav-link active" aria-current="page">
            <svg class="bi me-2" width="16" height="16">
              <use xlink:href="#home"></use>
            </svg>
            Home
          </a>
        </li>
        <li>
          <a href="#" class="nav-link link-dark">
            <svg class="bi me-2" width="16" height="16">
              <use xlink:href="#speedometer2"></use>
            </svg>
            Dashboard
          </a>
        </li>
        <li>
          <a href="#" class="nav-link link-dark">
            <svg class="bi me-2" width="16" height="16">
              <use xlink:href="#table"></use>
            </svg>
            Orders
          </a>
        </li>
        <li>
          <a href="#" class="nav-link link-dark">
            <svg class="bi me-2" width="16" height="16">
              <use xlink:href="#grid"></use>
            </svg>
            Products
          </a>
        </li>
        <li>
          <a href="#" class="nav-link link-dark">
            <svg class="bi me-2" width="16" height="16">
              <use xlink:href="#people-circle"></use>
            </svg>
            Customers
          </a>
        </li>
      </ul>
    </div>
  </div>
  <div class="col-md-9">
    <div class="super">
      <div class="conbox">
        <?php
        $colores = ['yellow', 'red', 'green', 'purple', 'orange', '', '', ''];

        foreach ($anuncios as $reg):
          $hash = md5($reg->contenido);
          $subHash = substr($hash, 0, 8);
          $decimal = hexdec($subHash);
          $valor = $decimal % 8;
          $tam = strlen($reg->contenido);
          $class = '';
          $clas = '';
          if (!preg_match("#\n#", $reg->contenido)) {
            $clas = $tam < 40 ? 'mod4' : ($tam < 90 ? 'mod3' : ($tam < 120 ? 'mod2' : ''));
          }
        ?>
          <div class="box <?php echo $colores[$valor] ?>">

            <p class="<?php echo $clas; ?>"><strong title="<?php echo $reg->id ?>"><?php echo $reg->title ?></strong> <?php echo $reg->contenido ?></p>
            <div class="info">
              <div class="d-flex justify-content-between">
                <span><?php echo tiempoTranscurrido($reg->fechapub) ?></span>
                <span><i class="fa-solid fa-phone"></i> <?php echo $reg->numero ?></a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>

      </div>
    </div>
  </div>
</div>