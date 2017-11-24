<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Fields\CheckboxField;

class Email extends Field
{
    protected $view = 'enquire::partials.email';

    public function getOptionFields()
    {
        return [
            new CheckboxField([
                'name' => 'options_array[reply_to]',
                'label' => 'Reply-to',
                'help_text' => "Check this option if you want the field's value to be used as the reply-to address"
            ])
        ];
    }

    public function setOptionsArrayAttribute($value)
    {
        $this->options = json_encode($value);
    }

    public function getOptionsAttribute()
    {
        return json_decode($this->attributes['options']);
    }

    public function getOptionsArrayAttribute()
    {
        return json_decode($this->attributes['options']);
    }

    public function getFillable()
    {
        return array_merge(parent::getFillable(), ['options_array']);
    }
}
