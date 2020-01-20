<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Enquire\Forms\Fields\Contracts\ReplyTo;

class Email extends Field implements ReplyTo
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
        if ( ! key_exists('options', $this->attributes)) {
            return null;
        }
        return json_decode($this->attributes['options']);
    }

    public function getOptionsArrayAttribute()
    {
        if ( ! key_exists('options', $this->attributes)) {
            return null;
        }
        return json_decode($this->attributes['options']);
    }

    public function getReplyToAddress($input)
    {
        if ( empty($this->options->reply_to) || empty($input[$this->name])) {
            return null;
        }

        return [$input[$this->name]];
    }
}
