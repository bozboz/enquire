<?php namespace Admin;

use Bozboz\Admin\Controllers\ModelAdminController;
use Bozboz\Admin\Reports\Report;
use Bozboz\Enquire\Forms\Fields\FieldDecorator;
use Bozboz\Enquire\Forms\FormRepository;
use Config, Input, Redirect;

class FormFieldAdminController extends ModelAdminController
{
	private $formRepository;

	public function __construct(FieldDecorator $decorator, FormRepository $formRepository)
	{
		parent::__construct($decorator);
		$this->formRepository = $formRepository;
	}

	public function index()
	{
		if (!Input::get('form_id')) {
			return Redirect::route('admin.enquiry-forms.index');
		}
		$report = new Report($this->decorator);
		$report->overrideView('enquire::admin.fields.overview');
		return $report->render(array('controller' => get_class($this)));
	}

	public function createForForm($formId, $fieldTypeAlias)
	{
		$instance = $this->decorator->newModelInstance();

		$form = $this->formRepository->find($formId);
		$instance->form()->associate($form);

		$type = Config::get("enquire::fields.{$fieldTypeAlias}");
		$instance->input_type = $type;

		return $this->renderCreateFormFor($instance);
	}

	/**
	 * The generic response after a successful create/edit/delete action.
	 */
	protected function getSuccessResponse($instance)
	{
		return Redirect::action(get_class($this) . '@index', ['form_id' => $instance->form_id]);
	}

	protected function getListingUrl($instance)
	{
		return action(get_class($this) . '@index', ['form_id' => $instance->form_id]);
	}
}
