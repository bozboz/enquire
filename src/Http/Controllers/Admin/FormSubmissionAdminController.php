<?php

namespace Bozboz\Enquire\Http\Controllers\Admin;

use Bozboz\Admin\Http\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Actions\Permissions\IsValid;
use Bozboz\Admin\Reports\Actions\Presenters\Link;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Custom;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Url;
use Bozboz\Admin\Reports\CSVReport;
use Bozboz\Admin\Reports\Report;
use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Submissions\SubmissionDecorator;
use Illuminate\Support\Facades\Request;

class FormSubmissionAdminController extends ModelAdminController
{
	protected $useActions = true;

	public function __construct(SubmissionDecorator $decorator)
	{
		parent::__construct($decorator);
	}

	protected function getReportActions()
	{
		$forms = app(FormAdminController::class);

		return [
			$this->actions->custom(
				new Link(
					[$this->getActionName('downloadReport'), request()->except('per-page')],
					'Download Report',
					'fa fa-download',
					['class' => 'btn-default pull-right space-left', 'style' => 'margin-right: 15px;']
				),
				new IsValid([$this, 'canReport'])
			),
			$this->actions->custom(
				new Link(
					new Custom(function($instance) {
						return route('admin.enquiry-forms.download-report', ['form' => Request::get('form')]);
					}),
					'Download Report',
					'fa fa-download',
					['class' => 'btn-default pull-right space-left', 'style' => 'margin-right: 15px;']
				),
				new IsValid([$this, 'canReportForm'])
			),
			$this->actions->custom(
				new Link(new Url($forms->getListingUrl(Form::first())), 'Back to forms', 'fa fa-list-alt', [
					'class' => 'btn-default pull-right space-left',
				]),
				new IsValid([$forms, 'canView'])
			),
		];
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

	public function downloadReport()
	{
		$report = new CSVReport($this->decorator);
		return $report->render([
			'filename' => 'enquiry-report-' . date('Y-m-d') . '.csv',
		]);
	}

	public function canReport()
	{
		return $this->canView() && ! Request::get('form');
	}

	public function canReportForm()
	{
		return $this->canView() && Request::get('form');
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
