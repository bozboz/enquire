<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Services\Validators\Validator;

class FieldValidator extends Validator
{
	protected $rules = [
		'form_id' => 'required|exists:enquiry_forms,id',
        'label' => 'required|regex:/^[a-z0-9_]+$/|unique:enquiry_form_fields,label,{id},id,form_id,{form_id}',
		'input_type' => 'required',
	];
}
