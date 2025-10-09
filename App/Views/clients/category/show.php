<?php

use App\Helpers\Text;
?>


<h1><?= Text::e($title) ?></h1>

<?php require dirname(__DIR__) . '/layouts/card.php'; ?>
<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        <?= $paginatedquery->getPreviousLink($link) ?>
        <?= $paginatedquery->getNextLink($link) ?>

    </ul>
</nav>