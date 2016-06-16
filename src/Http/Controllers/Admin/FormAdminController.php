<?php

namespace Bozboz\Enquire\Http\Controllers\Admin;

use Bozboz\Admin\Http\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Actions\Permissions\IsValid;
use Bozboz\Admin\Reports\Actions\Presenters\Link;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Custom;
use Bozboz\Enquire\Forms\FormDecorator;

class FormAdminController extends ModelAdminController
{
    protected $useActions = true;

	public function __construct(FormDecorator $decorator)
	{
		parent::__construct($decorator);
	}

    protected function getRowActions()
    {

        return array_merge([
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
}
