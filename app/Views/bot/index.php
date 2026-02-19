<div class="d-flex gap-1 flex mt-3">
<?php foreach ($links as $i=>$link): ?>
  <a href="<?php echo base_url('bot/index-' . $i) ?>" class="btn btn-primary"><?php echo $link[1] ?></a>
<?php endforeach; ?>
</div>
<hr>

<div class="d-flex d-justify-content-between">
  <?php foreach ($pages as $page): ?>
    <img src="<?php echo $page ?>" alt="">
  <?php endforeach; ?>
</div>

<textarea name="" id="" class="form-control" rows=20 style="white-space: nowrap;"><?php echo $texto; ?></textarea>