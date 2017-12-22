<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Fields\TextareaField;
use Bozboz\Enquire\Forms\Fields\Contracts\Recipients;

class RecipientDropdown extends Dropdown implements Recipients
{
    public function getRecipients($input)
    {
        if ( ! key_exists($this->name, $input)) {
            return null;
        }

        return $this->getRecipientOptions()->get($input[$this->name]);
    }

    public function getRecipientOptions()
    {
        return $this->decodeOptions()->pluck('label', 'value')->map(function($recipients) {
            return array_filter(explode(',', $recipients));
        });
    }

    public function getSelectOptions()
    {
        return $this->decodeOptions()->pluck('value', 'value')->prepend('- Please Select -', '');
    }

    public function getOptionFields()
    {
        return [
            new TextareaField('options', [
                'help_text' => "
                    Enter options with a new line between each one using the following format:<br>
                    Option Label => Recipient Emails<br>
                    e.g.<br>
                    Services => services@bozboz.co.uk,info@bozboz.co.uk
                ",
            ])
        ];
    }
}
