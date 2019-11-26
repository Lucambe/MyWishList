<div class="card text-center my-2">
  <div class="card-header">
    <?= $item->nom ?>
  </div>
  <div class="card-body">
    <img src="<?= $rootUri ?>/public/images/<?= $item->img ?>" class="img-fluid img-thumbnail">
    <p class="card-text"><?= $item->descr ?></p>
    <a href="#" class="btn btn-primary">Go somewhere</a>
  </div>
  <div class="card-footer text-muted">
    <?= $item->tarif ?> €
  </div>
</div>