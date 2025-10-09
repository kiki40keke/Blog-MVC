<?php

namespace App\Validators;

use App\Models\Repository\CategoryRepository;

class CategoryValidator extends ValidatorBase
{

    public function __construct(array $data, CategoryRepository $Repository, ?int $excludeId = null)
    {
        parent::__construct($data, $Repository);

        $v = $this->validator;

        $v->labels([
            'name'       => 'Titre',
            'slug'       => 'Slug',
        ]);

        $v->rule('required', ['name', 'slug']);
        $v->rule('lengthBetween', ['name', 'slug'], 3, 500);

        // Custom rules
        $v->rule('slugFormat', 'slug');
        $v->rule('unique', 'slug', 'slug', $excludeId);
        $v->rule('unique', 'name', 'name', $excludeId);
    }
}
