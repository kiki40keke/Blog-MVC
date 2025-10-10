<?php

use App\Helpers\Upload;
?>
<form action="" method="POST" enctype="multipart/form-data">
    <?= $form->input('name', 'Titre', 'Text'); ?>
    <?= $form->input('slug', 'Slug', 'Text'); ?>
    <?= $form->textArea('content', 'Contenu'); ?>
    <?= $form->input('created_at', 'Date', 'text'); ?>
    <?= $form->selectMultiple('categories_id', 'Categories', $categories); ?>
    <div class="row mb-3 border-bottom pb-3">
        <div class="col-md-8">
            <?= $form->input('image', 'Image à la une', 'file'); ?>

        </div>
        <div class="col-md-4">
            <?= Upload::viewImage($post->getImage()) ?>

        </div>
    </div>
    <?= $form->button($label = ($post->getId()) ? "Modifier" : "Enregistrer") ?>
</form>