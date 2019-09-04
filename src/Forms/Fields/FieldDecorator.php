<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Base\ModelAdminDecorator;
use Bozboz\Admin\Reports\Filters\ArrayListingFilter;
use Bozboz\Admin\Reports\Filters\HiddenFilter;
use Bozboz\Admin\Reports\Filters\RelationFilter;
use Bozboz\Enquire\Forms\Form;
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
		return [
			'Name' => $instance->label,
			'Type' => $instance->getDescriptiveName(),
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
		return array_merge($instance->getDefaultFields(), $instance->getOptionFields());
	}

	public function setType($inputType)
	{
		$this->model = $this->newFieldOfType($inputType);
		return $this;
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

	/**
	 * Get the names of the many-to-many relationships defined on the model
	 * that need to be processed.
	 *
	 * @return array
	 */
	public function getSyncRelations()
	{
		return $this->model->getSyncRelations();
	}

	/**
	 * Get the names of the sortable many-to-many relationships on the model
	 * return array
	 */
	public function getSortableSyncRelations()
	{
		return $this->model->getSortableSyncRelations();
	}

	/**
	 * Get the names (and associated attribute to use) of list-style
	 * many-to-many relationship on the model that should be saved.
	 *
	 * @return array
	 */
	public function getListRelations()
	{
		return array_merge($this->model->getListRelations(), [
			'validationRules' => 'rule'
		]);
	}
}
