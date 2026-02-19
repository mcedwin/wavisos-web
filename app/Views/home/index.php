  <div class="card mb-3">
    <div class="card-body categorias">
      <h5>Directorio Puno</h5>
      <hr>
      <form id="searchForm" action="<?php echo base_url('/todo') ?>" class="d-block" method="GET">
        <div class="input-group input-group">
          <input class="form-control " type="search" id="s" name="s" placeholder="Buscar" value="">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-search"></i> Buscar
          </button>
        </div>
      </form>
      <hr>
      <div class="row">
        <div class="col-md-4">
          <ul class="list-unstyled ps-0 mt-2">
            <?php foreach ($cates as $index => $cate) : ?>
              <li class="mb-1">
                <a class="cate-title" href="<?php echo base_url("{$cate->cate_ini}") ?>">
                  <i class="<?php echo $cate->cate_icono ?>"></i> <?php echo $cate->cate_nombre; ?>
                </a>
                <div class="">
                  <ul class="list-unstyled list-subc small ms-3">
                    <?php foreach ($cate->subcates as $subc) : ?>
                      <li class="">
                        <a href="<?php echo base_url("/{$cate->cate_ini}/{$subc->subc_ini}") ?>" class=""><?php echo $subc->subc_nombre; ?></a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </li>
              <?php if (in_array($index, [2, 6, 8])) echo '</ul></div><div class="col-md-4"><ul class="list-unstyled ps-0 mt-2">';  ?>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>