<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Services\Validators\Validator;

class FieldValidator extends Validator
{
	protected $rules = [
		'form_id' => 'required|exists:enquiry_forms,id',
		'label' => 'required',
		'input_type' => 'required',
	];
}
