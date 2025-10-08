<?php
http_response_code(404);
?>

<div class="py-5 text-center">
    <h1 class="display-4 text-danger mb-3">404 - Page non trouvée</h1>
    <p class="lead mb-4">
        Désolé, la page que vous cherchez n'existe pas ou a été déplacée.
    </p>
    <a href="<?= $router->url('home'); ?>" class="btn btn-primary">
        ⬅ Retour à l'accueil
    </a>
</div>