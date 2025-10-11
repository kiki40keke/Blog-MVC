<?php if (!empty($errors['login'])): ?>
    <div class="alert alert-danger">
        <?= implode('<br>', $errors['login']) ?>
    </div>
<?php endif ?>

<form action="" method="post">
<?= \App\Helpers\Csrf::field() ?>
    <?= $form->input('username', 'Nom', 'text'); ?>
    <?= $form->input('password', 'Mot de passe', 'password'); ?>
    <input type="hidden" name="login" value="1">

    <?= $form->button('Se connecter') ?>
</form>