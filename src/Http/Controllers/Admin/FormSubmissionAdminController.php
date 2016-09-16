<?php

namespace Bozboz\Enquire\Http\Controllers\Admin;

use Bozboz\Admin\Http\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Actions\Permissions\IsValid;
use Bozboz\Admin\Reports\Actions\Presenters\Link;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Url;
use Bozboz\Admin\Reports\Report;
use Bozboz\Enquire\Submissions\SubmissionDecorator;

class FormSubmissionAdminController extends ModelAdminController
{
	protected $useActions = true;

	public function __construct(SubmissionDecorator $decorator)
	{
		parent::__construct($decorator);
	}

	protected function getReportActions()
	{
		return [];
	}

	protected function getRowActions()
	{
		return [
			$this->actions->custom(
				new Link($this->getEditAction(), 'View', 'fa fa-eye', [
					'class' => 'btn-primary'
				]),
				new IsValid([$this, 'canEdit'])
			),
			$this->actions->destroy(
				$this->getActionName('destroy'),
				[$this, 'canDestroy']
			)
		];
	}

	protected function getFormActions($instance)
	{
		return [
			$this->actions->custom(
				new Link(new Url($this->getListingUrl($instance)), 'Back to listing', 'fa fa-list-alt', [
					'class' => 'btn-default pull-right',
				]),
				new IsValid([$this, 'canView'])
			),
		];
	}

	public function viewPermissions($stack)
	{
		$stack->add('view_enquire_submissions');
	}

	public function createPermissions($stack, $instance)
	{
		$stack->add('create_enquire_submissions', $instance);
	}

	public function editPermissions($stack, $instance)
	{
		$stack->add('edit_enquire_submissions', $instance);
	}

	public function deletePermissions($stack, $instance)
	{
		$stack->add('delete_enquire_submissions', $instance);
	}
}
