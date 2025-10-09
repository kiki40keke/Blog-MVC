<?php

namespace App\Validators;

use App\Models\Repository\PostRepository;

class PostValidator extends ValidatorBase
{

    public function __construct(array $data, PostRepository $Repository, array $categoriesIds, ?int $excludeId = null)
    {
        //dd($data, array_keys($categoriesIds));
        parent::__construct($data, $Repository);

        $v = $this->validator;

        $v->labels([
            'name'       => 'Titre',
            'slug'       => 'Slug',
            'content'    => 'Contenu',
            'created_at' => 'Date de création',
            'categories_id' => 'Categorie',
            'image'      => '',
        ]);

        $v->rule('required', ['name', 'slug', 'content', 'created_at', 'categories_id']);
        $v->rule('lengthBetween', ['name', 'slug'], 3, 500);
        $v->rule('lengthBetween', 'content', 30, 1000);

        // Custom rules
        $v->rule('slugFormat', 'slug');
        $v->rule('unique', 'slug', 'slug', $excludeId);
        $v->rule('unique', 'name', 'name', $excludeId);
        $v->rule('dateAnyOf', 'created_at');

        // Validation de l’image 
        $v->rule('imageValid', 'image');

        // le champ doit exister et ne pas être vide
        $v->rule('array', 'categories_id');          // doit être un Repositoryau (select multiple)
        $min = 1;
        $v->rule('minCount', 'categories_id', $min)
            ->message(sprintf('sélectionnez au moins %d élément(s).', $min));    // au moins 1 sélection
        $v->rule('eachInt', 'categories_id');        // chaque valeur est un entier > 0

        // Et restreindre aux IDs autorisés
        $v->rule('subset', 'categories_id', array_map('intval', array_keys($categoriesIds)));
    }
}
