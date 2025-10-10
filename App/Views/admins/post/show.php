<?php

use App\Helpers\Text;
use App\Helpers\Upload;
?>

<h1><?= Text::e($post->getName()) ?></h1>
<div class="text-center mb-4">
    <?= Upload::viewImage($post->getImage(), 'img-fluid big-image') ?>
</div>
<p><?= $post->getFormatDate() ?></p>
<p><?= $post->getFormatedContent() ?></p>

<div class="d-flex justify-content-between">
    <a type="button" href="<?= $linkedit ?>" class="btn btn-warning">Modifier</a>
    <form action="<?= $linkdelete ?>" method="post" onsubmit="return confirm('Voulez le vous vraiment effectuez cette action')">
        <button type="submit" class="btn btn-danger">Supprimer</button>
    </form>

</div>