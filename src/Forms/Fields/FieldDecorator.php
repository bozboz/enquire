<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Base\ModelAdminDecorator;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\HiddenField;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\TextareaField;
use Bozboz\Admin\Reports\Filters\ArrayListingFilter;
use Bozboz\Admin\Reports\Filters\HiddenFilter;
use Bozboz\Admin\Reports\Filters\RelationFilter;
use Bozboz\Enquire\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;

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
		$type = studly_case(array_search($instance->input_type, Config::get('enquire.fields')));
		return [
			'Name' => $instance->label,
			'Type' => $type,
			'Required' => $instance->required ? '<i class="fa fa-check"></i>' : '',
		];
	}

	public function getListingFilters()
	{
		return [
			new HiddenFilter(new RelationFilter($this->model->form()))
		];
	}

	public function getFields($instance)
	{
		return [
			new TextField(['name' => 'label', 'label' => 'Name']),
			new TextField(['name' => 'placeholder']),
			new CheckboxField(['name' => 'required']),
			new TextField(['name' => 'validation']),
			($instance->input_type == 'enquire::partials.dropdown' ? new TextareaField('options', [
				'help_text' => 'Enter options for the dropdown with a new line between each one'
			]) : null),
			new HiddenField(['name' => 'form_id']),
			new HiddenField(['name' => 'input_type']),
		];
	}
}