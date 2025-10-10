<?php

use App\Helpers\Text;
?>
<h1>Liste des articles</h1>


<a class="btn btn-primary" href="<?= $router->url('admin_post_new') ?>">Nouvel Article</a>

<table class="table">
    <thead>
        <tr>
            <th scope="col">#id</th>
            <th scope="col">Name</th>
            <th scope="col">Date</th>
            <th scope="col">Edition</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($posts as $post):
            $linkview = $router->url('admin_post', ['id' => $post->getId()]);
            $linkedit = $router->url('admin_post_edit', ['id' => $post->getId()]);
            $linkdelete = $router->url('admin_post_delete', ['id' => $post->getId()]);
        ?>

            <tr>
                <th scope="row"><?= $post->getId() ?></th>
                <td><?= Text::e($post->getName()) ?></td>
                <td><?= $post->getFormatDate() ?></td>
                <td>
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <a type="button" href="<?= $linkview ?>" class="btn btn-primary">Voir</a>
                        <a type="button" href="<?= $linkedit ?>" class="btn btn-warning">Modifier</a>
                        <form action="<?= $linkdelete ?>" method="post" onsubmit="return confirm('Voulez le vous vraiment effectuez cette action')">
                            <input type="hidden" name="image" value="<?= $post->getImage() ?>">
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