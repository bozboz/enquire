<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Decorators\ModelAdminDecorator;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\HiddenField;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\TextareaField;
use Bozboz\Admin\Reports\Filters\ArrayListingFilter;
use Bozboz\Enquire\Forms\Form;
use Config;
use Illuminate\Database\Eloquent\Builder;

class FieldDecorator extends ModelAdminDecorator
{
	public function __construct(Field $model)
	{
		parent::__construct($model);
	}

	public function getLabel($instance)
	{
		return $instance->label;
	}

	public function getColumns($instance)
	{
		$type = studly_case(array_search($instance->input_type, Config::get('enquire::fields')));
		return [
			'Name' => $instance->label,
			'Type' => $type,
			'Required' => $instance->required ? '<i class="fa fa-check"></i>' : '',
		];
	}

	public function getListingFilters()
	{
		$formOptions = Form::orderBy('name')->lists('name', 'id');
		$formIds = array_keys($formOptions);
		$defaultForm = reset($formIds);
		return [
			new ArrayListingFilter('form_id', $formOptions, function($builder, $value) {
				$builder->where('form_id', $value);
			}, $defaultForm)
		];
	}

	public function getFields($instance)
	{
		return [
			new TextField(['name' => 'label', 'label' => 'Name']),
			new TextField(['name' => 'placeholder']),
			new CheckboxField(['name' => 'required']),
			new TextField(['name' => 'validation']),
			new HiddenField(['name' => 'form_id']),
			new HiddenField(['name' => 'input_type']),
		];
	}
}
