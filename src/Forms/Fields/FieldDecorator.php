<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Base\ModelAdminDecorator;
use Bozboz\Admin\Fields\BelongsToManyField;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\HiddenField;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\TextareaField;
use Bozboz\Admin\Reports\Filters\ArrayListingFilter;
use Bozboz\Admin\Reports\Filters\HiddenFilter;
use Bozboz\Admin\Reports\Filters\RelationFilter;
use Bozboz\Enquire\Forms\Form;
use Illuminate\Database\Eloquent\Builder;

class FieldDecorator extends ModelAdminDecorator
{
	private $rules;

	public function __construct(Field $model, Validation\RuleDecorator $rules)
	{
		parent::__construct($model);
		$this->rules = $rules;
	}

	public function getLabel($instance)
	{
		return $instance->label;
	}

	public function getColumns($instance)
	{
		return [
			'Name' => $instance->label,
			'Type' => $instance->type_label,
			'Required' => $instance->required ? '<i class="fa fa-check"></i>' : '',
		];
	}

	public function getListRelations()
	{
		return [
			'validationRules' => 'rule'
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
			new HiddenField(['name' => 'input_type']),
			new TextField(['name' => 'type_label', 'disabled' => 'disabled']),
			new TextField(['name' => 'label', 'label' => 'Name']),
			new TextField(['name' => 'placeholder']),
			new CheckboxField(['name' => 'required']),
			new BelongsToManyField($this->rules, $instance->validationRules(), [
				'key' => 'rule',
				'data-tags' => 'true',
				'help_text' => '<a href="https://laravel.com/docs/5.2/validation#available-validation-rules" target="_blank">See list of available validation rules.</a>',
			]),
			($instance->hasOptions() || $instance->input_type == 'enquire::partials.dropdown'
				? new TextareaField('options', [
					'help_text' => 'Enter options a new line between each one'
				]) : null),
			new HiddenField(['name' => 'form_id']),
		];
	}
}
