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
		return $this->value;
	}
}
