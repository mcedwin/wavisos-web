<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title><?php echo $meta->title ?></title>
  <meta name="description" content="<?php echo $meta->description ?>" />
  <meta name="keywords" content="<?php echo $meta->keywords ?>" />
  <meta name="robots" content="all" data-rh="" />
  <link rel="icon" type="image/png" href="<?php echo base_url('/sys/assets/img/favicon.png') ?>">
  <link rel="stylesheet" href="<?php echo base_url('/sys/assets/lib/bootstrap533/css/bootstrap.min.css') ?>" />
  <link rel="stylesheet" href="<?php echo base_url('/sys/assets/lib/fontawesome6/css/all.min.css') ?>" />
  <?php echo $css ?? '' ?>
  <link href="<?php echo base_url('sys/assets/css/style.css') ?>" rel="stylesheet" media="all">

  <!--  Essential META Tags -->
  <meta property="og:title" content="<?php echo $meta->title ?>">
  <meta property="og:type" content="website" />
  <meta property="og:image" content="<?php echo $meta->image ?>">
  <meta property="og:url" content="<?php echo $meta->url ?>">
  <meta property="og:image:secure_url" content="<?php echo $meta->image ?>" />

  <!--  Non-Essential, But Recommended -->
  <meta property="og:description" content="<?php echo $meta->description ?>">
  <meta property="og:site_name" content="<?php echo $meta->site_name ?>">
</head>

<body>
  <div class="container">
    <div class="d-flex justify-content-between bg-white my-3 p-2 rounded shadow-sm">
      <div class="d-flex align-items-center">
        <a class="nbrand fs-4 ms-2 mb-1" href="./">w<span>Avisos</span></a>
        <p class="mb-0 ms-3">El clasificado que vive en WhatsApp.</p>
      </div>

      <div class="d-flex">
        <a class="btn btn-outline-secondary me-1 px-3" href="#" data-bs-toggle="modal" data-bs-target="#aboutModal"><i class="fa-solid fa-question"></i></a>
        <a href="https://wa.me/51965889389?text=Hola" target="_blank" class="ms-2 btn btn-success">
          Publicar
          <i class="fas fa-robot"></i>
          <i class="fab fa-whatsapp"></i>
        </a>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-2" id="aboutModalLabel">Cómo funciona wAvisos</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">

            <div>
              <p class="lead">
                Wavisos es un sistema de anuncios clasificados que permite publicar y consultar avisos de manera rápida utilizando WhatsApp y la web.
              </p>

              <hr class="my-4">

              <h3>Publicar un anuncio</h3>
              <p>
                Para publicar, solo necesitas escribir al número oficial de Wavisos en WhatsApp.
                El sistema te guía paso a paso:
              </p>

              <ul>
                <li>Seleccionas tu país y ciudad.</li>
                <li>Eliges la categoría.</li>
                <li>Escribes tu anuncio.</li>
                <li>Opcionalmente agregas una imagen.</li>
              </ul>

              <p>
                En pocos pasos tu aviso queda publicado y visible en tu ciudad.
              </p>

              <h3>Consultar anuncios</h3>
              <p>
                Puedes buscar anuncios directamente desde la web seleccionando tu ubicación o también consultarlos por WhatsApp.
                Los avisos se organizan por país, región y ciudad para mostrar resultados locales y relevantes.
              </p>

              <h3>Ubicación local</h3>
              <p>
                Cada anuncio pertenece a una ciudad específica, lo que permite encontrar oportunidades cercanas y facilitar el contacto directo entre personas de la misma zona.
              </p>

              <h3>Simplicidad</h3>
              <p>
                El sistema está diseñado para ser simple, rápido y accesible.
                No requiere procesos complicados ni formularios extensos.
                Publicar y consultar anuncios toma solo unos minutos.
              </p>

              <hr class="my-4">

              <p class="text-muted">
                Wavisos — Anuncios locales, simples y rápidos.
              </p>

            </div>


          </div>
        </div>
      </div>
    </div>


  </div>

  <div class="container">