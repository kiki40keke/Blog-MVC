<?php

use App\HTML\Html;
use App\Helpers\Text;
?>
<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Text::e($title ?? 'Mon site') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/custom.css">
</head>

<body class="d-flex flex-column h-100">
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $router->url('home') ?>">Blogast</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <?= Html::generateMenu($menuAvecUrls, $active ?? 'default') ?>

                </div>
            </div>
        </div>
    </nav>
    <?php if ($msg = \App\Helpers\Session::getFlash('danger')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
    <?php endif ?>

    <?php if ($msg = \App\Helpers\Session::getFlash('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif ?>

    <div class="container mt-4">
        <?= $content ?>
    </div>

    <footer class="bg-light py-4 footer mt-auto">
        <div class="container text-center">
            <?php if (defined('DEBUG_TIME')): ?>
                <span>Page genere en <?= round(1000 * (microtime(true) - DEBUG_TIME)) ?> ms</span>
                <br>
            <?php endif; ?>
            <span class="text-muted">© 2024 Mon site. Tous droits réservés.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>