<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Base\Model;
use Bozboz\Admin\Base\Sorting\Sortable;
use Bozboz\Admin\Base\Sorting\SortableTrait;
use Bozboz\Enquire\Forms\Form;

class Field extends Model implements Sortable
{
	use SortableTrait;

	protected $table = 'enquiry_form_fields';

	protected $fillable = [
		'form_id',
		'label',
		'input_type',
		'placeholder',
		'help_text',
		'required',
		'validation',
		'options'
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
		return trim(preg_replace('/[^\w]+/', '_', (strtolower($this->label))), trim('_'));
	}

	protected function sortPrependOnCreate()
	{
		return false;
	}

	public function getValidator()
	{
		return new FieldValidator;
	}
}