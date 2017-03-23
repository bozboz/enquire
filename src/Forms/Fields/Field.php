<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Base\Model;
use Bozboz\Admin\Base\Sorting\Sortable;
use Bozboz\Admin\Base\Sorting\SortableTrait;
use Bozboz\Enquire\Forms\Fields\Validation\Rule;
use Bozboz\Enquire\Forms\Form;
use Illuminate\Support\Facades\Config;

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
		'options'
	];

	protected $nullable = [
		'help_text',
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

	public function getTypeAttribute()
	{
		return array_search($this->input_type, Config::get('enquire.fields'));
	}

	public function getTypeLabelAttribute()
	{
		return studly_case($this->type);
	}

	public function hasOptions()
	{
		return array_search($this->type, Config::get('enquire.fields_with_options'));
	}

	protected function sortPrependOnCreate()
	{
		return false;
	}

	public function validationRules()
	{
		return $this->belongsToMany(Rule::class, 'enquiry_form_field_validation')->withTimestamps();
	}

	public function getValidator()
	{
		return new FieldValidator;
	}
}