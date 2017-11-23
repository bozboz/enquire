<?php

namespace Bozboz\Enquire\Forms\Fields;

class FileUpload extends Field
{
    protected $view = 'enquire::partials.file';

    function formatInputForEmail($input)
    {
        return link_to(parent::formatInputForEmail($input));
    }
}
