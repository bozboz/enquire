<?php

namespace Bozboz\Enquire\Forms;

use DateTime, Link;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\TextareaField;
use Bozboz\Admin\Fields\HTMLEditorField;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\BelongsToManyField;
use Bozboz\Admin\Decorators\ModelAdminDecorator;
use Illuminate\Database\Eloquent\Builder;

class FormDecorator extends ModelAdminDecorator
{
	public function __construct(Form $model)
	{
		parent::__construct($model);
	}

	public function getLabel($instance)
	{
		return $instance->name;
	}

	protected function modifyListingQuery(Builder $query)
	{
		$query->orderBy($this->model->getTable() . '.name');
	}

	public function getColumns($instance)
	{
		$page_list = [];
		foreach ($instance->paths as $page) {
			$page_list[] = link_to($page->path, $page->path, ['target' => '_blank']);
		}
		return [
			'Name' => $instance->name,
			'Recipients' => $instance->recipients,
			'Pages' => implode('<br>', $page_list),
			'Newsletter Signup' => $instance->newsletter_signup ? '<i class="fa fa-check"></i>' : '',
			'Status' => $instance->getAttribute('status') == 1 ? 'Active' : 'Inactive',
			'' => link_to_route(
				'admin.enquiry-form-fields.index',
				'Edit Fields',
				['form_id' => $instance->id],
				['class' => 'btn btn-default btn-sm', 'style' => 'float:right;']
			)
		];
	}

	public function getFields($instance)
	{
		return [
			new TextField(['name' => 'name']),
			new CheckboxField(['name' => 'status']),
			new CheckboxField(['name' => 'newsletter_signup']),
			new TextField(['name' => 'recipients']),
			new TextareaField(['name' => 'page_list']),
			new HTMLEditorField(['name' => 'confirmation_message'])
		];
	}
}
