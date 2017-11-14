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
			'Type' => $instance->getDescriptiveName(),
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
		return array_merge([
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
			new HiddenField(['name' => 'form_id']),
		], $instance->getOptionFields($instance));
	}

	/**
	 * Get a new Entity instance, and if a template_id is present in the
	 * attributes, associate it with the Entity.
	 *
	 * @param  array  $attributes
	 * @return Bozboz\Jam\Entities\Entity
	 */
	public function newModelInstance($attributes = [])
	{
		return $this->model->newInstance($attributes);
	}

	/**
	 * Return a new entity, associated with given $template
	 *
	 * @param  Bozboz\Jam\Templates\Template  $template
	 * @return Bozboz\Jam\Entities\Entity
	 */
	public function newFieldOfType($input_type)
	{
		return $this->model->newInstance(['input_type' => $input_type]);
	}
}
