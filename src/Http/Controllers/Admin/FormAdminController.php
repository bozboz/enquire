<?php

namespace Bozboz\Enquire\Http\Controllers\Admin;

use Bozboz\Admin\Http\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Actions\Permissions\IsValid;
use Bozboz\Admin\Reports\Actions\Presenters\Link;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Custom;
use Bozboz\Enquire\Forms\FormDecorator;

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
            $this->actions->custom(
                new Link(
                    new Custom(function($instance) {
                        return route('admin.enquiry-form-submissions.index', ['form' => $instance->id]);
                    }),
                    'Submissions',
                    'fa fa-eye',
                    ['class' => 'btn btn-primary btn-sm']
                ),
                new IsValid([$this->submissions, 'canView'])
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
