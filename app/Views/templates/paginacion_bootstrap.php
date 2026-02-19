<?php if ($pager): ?>
<nav aria-label="Paginación" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a href="<?= $pager->getFirst() ?>" class="page-link">Primera</a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getPrevious() ?>" class="page-link">&laquo; Anterior</a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a href="<?= $link['uri'] ?>" class="page-link"><?= $link['title'] ?></a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a href="<?= $pager->getNext() ?>" class="page-link">Siguiente &raquo;</a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getLast() ?>" class="page-link">Última</a>
            </li>
        <?php endif ?>
    </ul>
</nav>
<?php endif ?>
