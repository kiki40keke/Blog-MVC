<?php

use App\Helpers\Text;
use App\Helpers\Upload;
?>
<h1><?= Text::e($post->getName()) ?></h1>
<p><?= $post->getFormatDate() ?></p>
<?php foreach ($post->getCategories() as $k => $category): ?>
    <?php if ($k > 0) : ?>
        ,
    <?php endif ?>

    <a href="<?= $router->url('category', ['id' => $category->getId(), 'slug' => $category->getSlug()]) ?>">


        <?= $category->getName() ?></a>
<?php endforeach ?>
<div class="text-center mb-4">
    <?= Upload::viewImage($post->getImage(), 'img-fluid big-image') ?>
</div>

<p><?= $post->getFormatedContent() ?></p>