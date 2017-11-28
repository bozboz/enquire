<?php

namespace Bozboz\Enquire\Forms\Fields;

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
}
