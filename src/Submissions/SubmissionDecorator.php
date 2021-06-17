<?php

namespace Bozboz\Enquire\Submissions;

use Bozboz\Admin\Base\BulkAdminDecorator;
use Bozboz\Admin\Base\ModelAdminDecorator;
use Bozboz\Admin\Fields\BelongsToManyField;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\HTMLEditorField;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\TextareaField;
use Bozboz\Admin\Reports\Downloadable;
use Bozboz\Admin\Reports\Filters\ArrayListingFilter;
use Bozboz\Admin\Reports\Filters\DateFilter;
use Bozboz\Admin\Reports\Filters\RelationFilter;
use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Forms\FormDecorator;
use DateTime, Link;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class SubmissionDecorator extends BulkAdminDecorator implements Downloadable
{
	private $forms;

	public function __construct(Submission $model, FormDecorator $forms)
	{
		parent::__construct($model);
		$this->forms = $forms;
	}

	public function getHeading($plural = false)
	{
		$heading = Form::whereId(Request::get('form'))->value('name') . ' Submission';
		return str_plural($heading, $plural ? 2 : 1);
	}

	public function getLabel($instance)
	{
		return $instance->form_name . ' - ' . $instance->created_at;
	}

	public function getColumns($instance)
	{
		return [
			'Form' => $instance->form_name,
			'Date' => $instance->created_at->format('d M Y - H:i'),
			'Content' => e(str_limit($instance->values->pluck('value')->implode(', ')))
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

	public function getListingFilters()
	{
		return [
			new RelationFilter($this->model->form(), $this->forms),
			new DateFilter('created_at'),
		];
	}

	public function getColumnsForCSV($instance)
	{
		if ( ! Request::get('form')) {
			return [
				'Form' => $instance->form_name,
				'Date' => $instance->created_at->format('d M Y - H:i'),
				'Content' => $instance->values->pluck('value')->implode(', ')
			];
		}

		$keys = $this->getColumnKeys($instance);
		$columns = collect([
			'Form' => $instance->form_name,
			'Date' => $instance->created_at->format('d M Y - H:i'),
		]);

		$columns = $columns->merge($instance->values->pluck('value', 'label'));

		$columns = array_merge($keys, $columns->all());

		return $columns;
	}

	private function getColumnKeys($instance)
	{
		static $keys;

		if ( ! $keys) {
			$keys = $instance->form->submissions()
				->join('enquiry_submission_values', 'enquiry_submission_values.submission_id', '=', 'enquiry_submissions.id')
				->groupBy('enquiry_submission_values.label')
				->pluck('enquiry_submission_values.label')
				->all();
		}

		return $keys;
	}

    public function getBulkFields($instances)
    {
        return [];
    }
}
