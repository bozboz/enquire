<?php

namespace Bozboz\Enquire\Forms;

use Bozboz\Admin\Services\Validators\Validator;

class FormValidator extends Validator
{
	protected $rules = [
		'confirmation_message' => 'required',
	];
}
