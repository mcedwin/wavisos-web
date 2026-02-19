<div class="col-md-4 sidebar">
  <div class="versiculo rounded p-4 text-center">
    <img src="" alt="" class="w-100">
    <p id="lectura"><?php echo $config->{'frase' . $config->activa} ?></p>
    <a href="#" class="btn btn-success leer"><i class="fa-solid fa-volume-high"></i></a>
  </div>

  <?php if ($config->esenvivo): ?>
    <a data-fancybox href="https://www.youtube.com/embed/<?php echo @id_youtube($config->urlvivo); ?>" class="d-block text-center mt-3">
      <img src="<?php echo base_url('sys/assets/img/envivo.png'); ?>" alt="" class="img-fluid">
    </a>
  <?php endif; ?>

  <div class="card mt-4">
    <iframe class="w-100" height="250" src="https://www.youtube.com/embed/<?php echo $videos[0]->idVideo ?>"></iframe>
    <div class="card-body">
      <p class="card-text"><?php echo $videos[0]->titulo ?></p>
      <a href="<?php echo base_url('videos') ?>" class="btn btn-secondary"><i class="fa-solid fa-video"></i> Más</a>
    </div>
  </div>




  <div class="my-3">
    <h3><span>Últimas noticias</span></h3>
    <ul class="lasts">
      <?php
      foreach ($noticias as $reg):
        list($anio, $mes, $dia) = explode("-", $reg->fecha)
      ?>
        <li class="d-flex ">
          <div class="date text-center p-1 px-3 me-2 rounded flex-shrink-1">
            <span>
              <span class="day"><?php echo $dia ?></span>
              <span class="month"><?php echo $mes ?></span>
            </span>
          </div>

          <div class="event-content w-100">
            <h6><a href="<?php echo base_url('noticias/' . $reg->slugifyTitulo) ?>"><?php echo $reg->titulo ?></a></h6>
          </div>
        </li>
      <?php endforeach; ?>


    </ul>
    <div class="d-flex justify-content-end">
      <a href="<?php echo base_url('noticias') ?>" class="btn btn-sm btn-secondary"><i class="fa-solid fa-angles-right"></i> Mas noticias</a>
    </div>

  </div>

  <div class="card my-3">
    <div class="card-body">
      <a href="http://www.cadenaradiovision.pe/" class="d-block text-center mt-3">
        <img src="<?php echo base_url('sys/assets/img/inforadio.jpeg'); ?>" alt="" class="img-fluid w-100">
      </a>
    </div>
  </div>

</div>