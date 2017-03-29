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
    public function getAdminField(Entity $instance, EntityDecorator $decorator, Value $value)
    {
        if (property_exists($this->options_array, 'entity')) {

            if (property_exists($this->options_array, 'make_parent')) {
                if (!$instance->parent_id) {
                    $instance->parent_id = $this->options_array->entity;
                }
                return new HiddenField($this->getInputName());
            }

            return new HiddenField($this->getInputName(), $this->options_array->entity);
        }

        return new BelongsToField($decorator, $this->relation($value), [
            'name' => $this->getInputName(),
            'label' => $this->getInputLabel(),
            'help_text_title' => $this->help_text_title,
            'help_text' => $this->help_text,
        ]);
    }

    public function relation(Value $value)
    {
        return $value->belongsTo(Relation::class, 'foreign_key')->with(['fields' => function($query) {
            $query->orderBy('sorting');
        }]);
    }

    public function getOptionFields()
    {
        return [];
    }
}