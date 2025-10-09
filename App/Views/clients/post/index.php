<h1>Mon blog</h1>
<?php
//j'imprte la page card 
require dirname(__DIR__) . '/layouts/card.php';
?>


<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        <?= $paginatedquery->getPreviousLink($link) ?>
        <?= $paginatedquery->getNextLink($link) ?>

    </ul>
</nav>