<?php

namespace Bozboz\Enquire\Forms;

use Bozboz\Admin\Services\Validators\Validator;

class FormValidator extends Validator
{
	protected $rules = [
		'confirmation_message' => 'required',
		'my_name' => 'honeypot',
		'my_time' => 'required|honeytime:5'
	];
}
