<form action="" method="POST">
    <?= \App\Helpers\Csrf::field() ?>

    <?= $form->input('name', 'Titre', 'Text'); ?>
    <?= $form->input('slug', 'Slug', 'Text'); ?>
    <?= $form->button($label = (isset($id)) ? "Modifier" : "Enregistrer") ?>
</form>