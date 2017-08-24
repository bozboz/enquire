<?php

namespace Bozboz\Enquire\Http\Controllers\Admin;

use Bozboz\Admin\Http\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Actions\Permissions\IsValid;
use Bozboz\Admin\Reports\Actions\Presenters\Link;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Custom;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Url;
use Bozboz\Enquire\Forms\CSVReport;
use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Forms\FormDecorator;
use Bozboz\Enquire\Forms\FormInterface;
use Bozboz\Enquire\Http\Controllers\Admin\FormFieldAdminController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Collection;

class FormAdminController extends ModelAdminController
{
    protected $submissions;

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
            $this->actions->custom(
                new Link(
                    new Custom(function($instance) {
                        return route('admin.enquiry-forms.duplicate-form', ['form' => $instance->id]);
                    }),
                    'Duplicate',
                    'fa fa-clone',
                    ['class' => 'btn btn-default btn-sm']
                ),
                new IsValid([$this, 'canDuplicate'])
            ),
        ], parent::getRowActions());
    }

    public function getFormActions($instance)
    {
        return [
            (
                $instance->load('fields')->fields->isEmpty()
                    ? $this->actions->submit('Save and Create Fields', 'fa fa-save', [
                        'name' => 'after_save',
                        'value' => 'fields',
                    ])
                    : $this->actions->submit('Save and Exit', 'fa fa-save', [
                        'name' => 'after_save',
                        'value' => 'exit',
                    ])
            ),
            $this->actions->submit('Save', 'fa fa-save', [
                'name' => 'after_save',
                'value' => 'continue',
            ]),
            $this->actions->custom(
                new Link(new Url($this->getListingUrl($instance)), 'Back to listing', 'fa fa-list-alt', [
                    'class' => 'btn-default pull-right space-left',
                ]),
                new IsValid([$this, 'canView'])
            ),
        ];
    }

    protected function reEdit($instance)
    {
        switch (Input::get('after_save')) {
            case 'fields':
                return Redirect::action('\\' . FormFieldAdminController::class . '@index', ['form' => $instance->id]);
            break;

            default:
                return parent::reEdit($instance);
        }
    }

    public function downloadReport(Form $form)
    {
        if ( ! $this->canReport()) {
            return abort(403);
        }

        $report = new CSVReport($form);

        return $report->render(['filename' => str_slug($form->name) . '-report-' . date('Y-m-d') . '.csv',]);
    }

    public function duplicateForm(Form $form)
    {
        if ( ! $this->canDuplicate()) {
            return abort(403);
        }

        $clone = $form->replicate();
        $clone->save();

        $this->duplicateRelatedModels($clone, $form->fields()->get());
        $this->duplicateRelatedModels($clone, $form->paths()->get());

        return parent::edit($clone->id);
    }

    protected function duplicateRelatedModels(Form $form, Collection $collection)
    {
        $collection->each(function($model) use($form) {
            $clone = $model->replicate();
            $clone->form()->associate($form);
            $clone->save();
        });
    }

    public function canReport()
    {
        return $this->submissions->canView();
    }

    public function canDuplicate()
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
