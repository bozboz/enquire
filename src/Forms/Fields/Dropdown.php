<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Fields\TextareaField;

class Dropdown extends Field
{
    protected $view = 'enquire::partials.dropdown';

    public function getSelectOptions()
    {
        $options = collect(explode(PHP_EOL, $this->options))->map(function($option) {
            @list($value, $label) = explode('=>', $option);
            return [
                'label' => trim($label ?: $value),
                'value' => trim($value),
            ];
        });
        return $options->pluck('label', 'value')->prepend('- Please Select -', '');
    }

    public function getOptionFields()
    {
        return [
            new TextareaField('options', [
                'help_text' => 'Enter options a new line between each one'
            ])
        ];
    }
}
