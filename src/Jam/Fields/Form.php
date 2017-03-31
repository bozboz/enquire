<?php

namespace Bozboz\Enquire\Jam\Fields;

use Bozboz\Admin\Fields\BelongsToField;
use Bozboz\Admin\Fields\HiddenField;
use Bozboz\Enquire\Forms\Form as Relation;
use Bozboz\Jam\Entities\Entity;
use Bozboz\Jam\Entities\EntityDecorator;
use Bozboz\Jam\Entities\Value;
use Bozboz\Jam\Fields\BelongsTo;

class Form extends BelongsTo
{
    protected function getRelationModel()
    {
        return Relation::class;
    }

    public function relation(Value $value)
    {
        return parent::relation($value)->with(['fields' => function($query) {
            $query->orderBy('sorting');
        }]);
    }
}