<?php

namespace Bozboz\Enquire\Http\Controllers\Admin;

use Bozboz\Admin\Http\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Actions\Permissions\IsValid;
use Bozboz\Admin\Reports\Actions\Presenters\Link;
use Bozboz\Admin\Reports\Actions\Presenters\Urls\Route;
use Bozboz\Admin\Reports\Report;
use Bozboz\Enquire\Forms\Fields\FieldDecorator;
use Bozboz\Enquire\Forms\FormRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class FormFieldAdminController extends ModelAdminController
{
	protected $useActions = true;
	private $formRepository;

	public function __construct(FieldDecorator $decorator, FormRepository $formRepository)
	{
		parent::__construct($decorator);
		$this->formRepository = $formRepository;
	}

	public function index()
	{
		if ( ! Request::get('form')) {
			return Redirect::route('admin.enquiry-forms.index');
		}
		return parent::index();
	}

	public function createForForm($formId, $fieldTypeAlias)
	{
		$instance = $this->decorator->newModelInstance();

		$form = $this->formRepository->find($formId);
		$instance->form()->associate($form);

		$type = Config::get("enquire.fields.{$fieldTypeAlias}");
		$instance->input_type = $type;

		return $this->renderFormFor($instance, $this->createView, 'POST', 'store');
	}

	protected function getReportActions()
	{
		return [
			$this->actions->dropdown(
				collect(Config::get('enquire.fields'))->map(function($namespace, $fieldType) {
					return $this->actions->custom(
						new Link(
							[$this->getActionName('createForForm'), [Request::get('form'), $fieldType]],
							studly_case($fieldType)
						),
						new IsValid([$this, 'canCreate'])
					);
				}),
				'New Field',
				'fa fa-plus',
				['class' => 'btn-success'],
				['class' => 'pull-right space-left']
			),
			$this->actions->custom(
				new Link(new Route('admin.enquiry-forms.index'), 'Back to forms', 'fa fa-list-alt', [
					'class' => 'btn-default pull-right',
				]),
				new IsValid([$this, 'canView'])
			),
		];
	}

	/**
	 * The generic response after a successful create/edit/delete action.
	 */
	protected function getSuccessResponse($instance)
	{
		return Redirect::action('\\' . static::class . '@index', ['form' => $instance->form_id]);
	}

	protected function getListingUrl($instance)
	{
		return action('\\' . static::class . '@index', ['form' => $instance->form_id]);
	}

    public function createPermissions($stack, $instance)
    {
        $stack->add('create_enquire_forms', $instance);
        $stack->add('create_enquire_form_fields', $instance);
    }

    protected function editPermissions($stack, $instance)
    {
        $stack->add('edit_enquire_forms', $instance);
        $stack->add('edit_enquire_form_fields', $instance);
    }

    protected function deletePermissions($stack, $instance)
    {
        $stack->add('delete_enquire_forms', $instance);
        $stack->add('delete_enquire_form_fields', $instance);
    }

    protected function viewPermissions($stack)
    {
        $stack->add('view_enquire_forms');
    }
}
