<?php

use App\Helpers\Text;
?>



<h1>Liste des categories articles</h1>
<a class="btn btn-primary" href="<?= $router->url('admin_category_new') ?>">Nouvel Article</a>

<table class="table">
    <thead>
        <tr>
            <th scope="col">#id</th>
            <th scope="col">Name</th>
            <th scope="col">Edition</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category):
            $linkedit = $router->url('admin_category_edit', ['id' => $category->getId()]);
            $linkdelete = $router->url('admin_category_delete', ['id' => $category->getId()]);
        ?>

            <tr>
                <th scope="row"><?= $category->getId() ?></th>
                <td><?= Text::e($category->getName()) ?></td>
                <td>
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <a type="button" href="<?= $linkedit ?>" class="btn btn-warning">Modifier</a>
                        <form action="<?= $linkdelete ?>" method="post" onsubmit="return confirm('Voulez le vous vraiment effectuez cette action')">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>

                    </div>
                </td>
            </tr>
        <?php endforeach; ?>



    </tbody>
</table>
<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
        <?= $paginatedquery->getPreviousLink($link) ?>
        <?= $paginatedquery->getNextLink($link) ?>

    </ul>
</nav>