<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Fields\TextareaField;

class Checkboxes extends Field
{
    protected $view = 'enquire::partials.checkboxes';

    public function getOptions()
    {
        return $this->decodeOptions()->pluck('label', 'value');
    }

    protected function decodeOptions()
    {
        return collect(explode(PHP_EOL, $this->options))->map(function($option) {
            @list($value, $label) = explode('=>', $option);
            return [
                'label' => trim($label ?: $value),
                'value' => trim($value),
            ];
        });
    }

    public function getOptionFields()
    {
        return [
            new TextareaField('options', [
                'help_text' => 'Enter options with a new line between each one'
            ])
        ];
    }
}
