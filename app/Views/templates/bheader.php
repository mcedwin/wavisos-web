<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title><?php echo $meta->title ?></title>
  <meta name="description" content="<?php echo $meta->description ?>" />
  <link rel="stylesheet" href="<?php echo base_url('/sys/assets/lib/bootstrap533/css/bootstrap.min.css') ?>" />
  <link rel="stylesheet" href="<?php echo base_url('/sys/assets/lib/fontawesome6/css/all.min.css') ?>" />
  <?php echo $css ?? '' ?>
</head>

<body>


    <div class="container">
      