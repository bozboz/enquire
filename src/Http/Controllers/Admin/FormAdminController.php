<?php

namespace Bozboz\Enquire\Http\Controllers\Admin;

use Bozboz\Admin\Http\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Actions\Permissions\IsValid;
use Bozboz\Admin\Reports\Actions\Presenters\Link;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Custom;
use Bozboz\Enquire\Forms\CSVReport;
use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Forms\FormDecorator;
use Bozboz\Enquire\Forms\FormInterface;

class FormAdminController extends ModelAdminController
{
    private $submissions;

    protected $useActions = true;

	public function __construct(FormDecorator $decorator, FormSubmissionAdminController $submissions)
	{
		parent::__construct($decorator);
        $this->submissions = $submissions;
	}

    protected function getRowActions()
    {
        return array_merge([
            $this->actions->dropdown(
                [
                    $this->actions->custom(
                        new Link(
                            new Custom(function($instance) {
                                return route('admin.enquiry-form-submissions.index', ['form' => $instance->id]);
                            }),
                            'View',
                            'fa fa-eye'
                        ),
                        new IsValid([$this->submissions, 'canView'])
                    ),
                    $this->actions->custom(
                        new Link(
                            new Custom(function($instance) {
                                return route('admin.enquiry-forms.download-report', ['form' => $instance->id]);
                            }),
                            'Download Report',
                            'fa fa-download'
                        ),
                        new IsValid([$this, 'canReport'])
                    )
                ], 'Submissions', 'fa fa-list', [
                    'class' => 'btn-default btn-sm',
                ]
            ),
            $this->actions->custom(
                new Link(
                    new Custom(function($instance) {
                        return route('admin.enquiry-form-fields.index', ['form' => $instance->id]);
                    }),
                    'Edit Fields',
                    'fa fa-list',
                    ['class' => 'btn btn-default btn-sm']
                ),
                new IsValid([$this, 'canEdit'])
            ),
        ], parent::getRowActions());
    }

    public function downloadReport(Form $form)
    {
        if ( ! $this->canReport()) {
            return abort(403);
        }

        $report = new CSVReport($form);

        return $report->render(['filename' => str_slug($form->name) . '-report-' . date('Y-m-d') . '.csv',]);
    }

    public function canReport()
    {
        return $this->submissions->canView();
    }

    protected function createPermissions($stack, $instance)
    {
        $stack->add('create_enquire_forms', $instance);
    }

    protected function editPermissions($stack, $instance)
    {
        $stack->add('edit_enquire_forms', $instance);
    }

    protected function deletePermissions($stack, $instance)
    {
        $stack->add('delete_enquire_forms', $instance);
    }

    protected function viewPermissions($stack)
    {
        $stack->add('view_enquire_forms');
    }
}
