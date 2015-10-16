<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Models\Base;
use Bozboz\Admin\Models\Sortable;
use Bozboz\Enquire\Forms\Form;

class Field extends Base implements Sortable
{
	protected $table = 'enquiry_form_fields';

	protected $fillable = [
		'form_id',
		'label',
		'input_type',
		'placeholder',
		'help_text',
		'required',
		'validation'
	];

	protected $nullable = [
		'help_text',
		'validation'
	];

	public function form()
	{
		return $this->belongsTo(Form::class);
	}

	public function sortBy()
	{
		return 'sorting';
	}

	public function getNameAttribute()
	{
		return trim('_', preg_replace('/[^\w]+/', '_', (strtolower($this->label))));
	}

	public function getValidator()
	{
		return new FieldValidator;
	}
}