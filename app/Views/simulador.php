<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Simulador de Webhooks</title>
</head>
<body>
  <h1>Simular mensajes de WhatsApp</h1>
  <ul>
    <?php foreach ($tipos as $tipo): ?>
      <li>
        <a href="<?php echo base_url('simular/enviar/' . $tipo) ?>">
          <?php echo ucfirst($tipo) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>