<?php

namespace Bozboz\Enquire\Http\Controllers\Admin;

use Bozboz\Admin\Http\Controllers\BulkAdminController;
use Bozboz\Admin\Http\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Actions\Permissions\IsValid;
use Bozboz\Admin\Reports\Actions\Presenters\Link;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Custom;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Route;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Url;
use Bozboz\Admin\Reports\CSVReport;
use Bozboz\Admin\Reports\Report;
use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Submissions\Submission;
use Bozboz\Enquire\Submissions\SubmissionDecorator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Bozboz\Permissions\RuleStack;

class FormSubmissionAdminController extends BulkAdminController
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
			$this->actions->custom(
				new \Bozboz\Admin\Reports\Actions\Presenters\Form(new Route('admin.enquiry-form-submissions.bulk-delete'), 'Delete Selected', 'fa fa-trash', [
				    'class' => 'btn-sm btn-warning',
                    'data-warn' => 'Are you sure you want to delete the selected submissions',
                ], [
                    'class' => 'pull-right space-left js-bulk-update',
                ]),
				new IsValid([$this, 'canBulkDestroy'])
			),
			$this->actions->custom(
				new \Bozboz\Admin\Reports\Actions\Presenters\Form(new Route('admin.enquiry-form-submissions.delete-all'), 'Delete All', 'fa fa-trash', [
				    'class' => 'btn-sm btn-danger',
                    'data-warn' => 'Are you sure you want to delete all submissions',
                ], [
                    'class' => 'pull-right space-left js-bulk-update',
                ]),
				new IsValid([$this, 'canBulkDestroy'])
			)
		];
	}

    protected function getSuccessResponse($instance)
    {
		return redirect()->action($this->getActionName('index'), ['form' => $instance->form_id]);
    }

    public function bulkDelete()
    {
        parse_str(parse_url(url()->previous(), PHP_URL_QUERY), $query);
        Submission::whereIn('id', request()->instances)->delete();
        return $this->getSuccessResponse((object)['form_id' => Arr::get($query, 'form')]);
    }

    public function deleteAll()
    {
        parse_str(parse_url(url()->previous(), PHP_URL_QUERY), $query);
        $formId = Arr::get($query, 'form');
        if ($formId) {
            Submission::whereFormId($formId)->delete();
        } else {
            Submission::query()->delete();
        }
        return $this->getSuccessResponse((object)['form_id' => $formId]);
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
        if ( ! $this->canReport()) {
            return abort(403);
        }

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

	public function canBulkDestroy()
	{
		$stack = new RuleStack;

		$stack->add('delete_anything');

		$this->deletePermissions($stack, null);

		return $stack->isAllowed();	}

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
