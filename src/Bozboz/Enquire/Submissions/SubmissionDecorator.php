<?php

namespace Bozboz\Enquire\Submissions;

use DateTime, Link;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\TextareaField;
use Bozboz\Admin\Fields\HTMLEditorField;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\BelongsToManyField;
use Bozboz\Admin\Decorators\ModelAdminDecorator;
use Illuminate\Database\Eloquent\Builder;

class SubmissionDecorator extends ModelAdminDecorator
{
	public function __construct(Submission $model)
	{
		parent::__construct($model);
	}

	public function getLabel($instance)
	{
		return $instance->form_name . ' - ' . $instance->created_at;
	}

	public function getColumns($instance)
	{
		return [
			'Form' => $instance->form_name,
			'Date' => $instance->created_at,
			'Preview' => str_limit(implode(', ', $instance->values->lists('value')))
		];
	}

	public function getFields($instance)
	{
		$fields = [];
		$fields[] = new SubmissionValueField('Form Name', $instance->form_name);
		$fields[] = new SubmissionValueField('Date', $instance->created_at);
		foreach ($instance->values as $value) {
			$fields[] = new SubmissionValueField($value->label, $value->value);
		}
		return $fields;
	}

	public function modifyListingQuery(Builder $query)
	{
		$query->with('form', 'values')->latest();
	}
}
