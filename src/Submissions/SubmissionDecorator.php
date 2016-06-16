<?php

namespace Bozboz\Enquire\Submissions;

use Bozboz\Admin\Base\ModelAdminDecorator;
use Bozboz\Admin\Fields\BelongsToManyField;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\HTMLEditorField;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\TextareaField;
use DateTime, Link;
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
			'Content' => str_limit($instance->values->pluck('value')->implode(', '))
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
		$query->with('values')->orderBy('created_at', 'desc');
	}
}
