<?php

use App\Helpers\Text;
use App\Helpers\Upload;
?>
<div class="row">
    <?php foreach ($posts as $post): ?>
        <?php
        $categories = [];
        foreach ($post->getCategories() as $category) {
            $url = '#';
            // $url = $router->url('category', ['id' => $category->getId(), 'slug' => $category->getSlug()]);
            $categories[] = <<<HTML
     <a href="{$url}">{$category->getName()}</a>
HTML;
        }

        ?>

        <div class="col-md-3">
            <div class="card mb-3">
                <?= Upload::viewImage($post->getImage(), 'card-img-top') ?>

                <div class="card-body">
                    <h5 class="card-title"><?= Text::e($post->getName()) ?></h5>
                    <?php if (!empty($post->getCategories())): ?>
                        ::
                        <?= implode(', ', $categories) ?>
                    <?php endif ?>
                    </p>
                    <p class="card-text"><?= $post->getExcept() ?>
                        <a href="<?= '#'; // $router->url('post', ['id' => $post->getId(), 'slug' => $post->getSlug()]) 
                                    ?>" class="btn btn-primary">Voir plus</a>
                    </p>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Last updated <?= $post->getFormatDate() ?></small>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>