<?php

namespace Bozboz\Enquire\Submissions;

use Bozboz\Admin\Fields\TextField;
use Illuminate\Support\Facades\Form;

class SubmissionValueField extends TextField
{
	private $value;

	public function __construct($name, $value) {
		parent::__construct($name);
		$this->value = $value;
	}

	public function getInput()
	{
		return nl2br($this->generateLinks(e($this->value)));
	}

    protected function generateLinks($value)
    {
        return preg_replace(
            '/((http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?)/',
            '<a href="$1" target="_blank">$1</a>',
            $value
        );
    }
}
