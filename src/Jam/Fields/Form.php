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

    public function getAdminField(Entity $instance, EntityDecorator $decorator, Value $value)
    {
        if ( ! Relation::count()) {
            $this->help_text = "<br>No forms have been created yet, <a href='/admin/enquiry-forms/create' target='_blank'><strong>head here to create a new form</strong></a>.";
        } else {
            $form = $this->relation($value)->first();
            if ($form) {
                $this->help_text .= "<br>
                    <a target='_blank' class='btn btn-default btn-sm' href='/admin/enquiry-forms/{$form->id}/edit'>Edit Form</a>
                    <a target='_blank' class='btn btn-default btn-sm' href='/admin/enquiry-form-fields?form={$form->id}'>Edit form fields</a>
                ";
            }
        }
        return parent::getAdminField($instance, $decorator, $value);
    }
}
