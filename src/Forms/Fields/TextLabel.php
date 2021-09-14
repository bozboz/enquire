<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Fields\HiddenField;
use Bozboz\Admin\Fields\HTMLEditorField;
use Bozboz\Admin\Fields\TextField;

class TextLabel extends Field
{
    protected $view = 'enquire::partials.text-label';

    public function getDefaultFields()
    {
        return [
            new HiddenField(['name' => 'input_type']),
            new TextField(['name' => 'type_label', 'disabled' => 'disabled']),
            new TextField(['name' => 'label', 'label' => 'Name']),
            new TextField(['name' => 'placeholder', 'label' => 'Short text']),
            new HiddenField(['name' => 'form_id']),
        ];
    }

    public function getOptionFields()
    {
        return [
            new HTMLEditorField('options', [
                'label' => 'Text'
            ]),
        ];
    }
}
