<form action="" method="POST">
    <?= $form->input('name', 'Titre', 'Text'); ?>
    <?= $form->input('slug', 'Slug', 'Text'); ?>
    <?= $form->button($label = (isset($id)) ? "Modifier" : "Enregistrer") ?>
</form>