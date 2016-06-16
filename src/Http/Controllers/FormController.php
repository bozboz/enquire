<?php

namespace Bozboz\Enquire\Http\Controllers;

use Bozboz\Enquire\Forms\FormException;
use Bozboz\Enquire\Forms\FormInterface;
use Bozboz\Enquire\Forms\FormRepositoryInterface;
use Bozboz\Enquire\Submissions\Submission;
use Bozboz\Enquire\Submissions\Value;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class FormController extends Controller
{
	private $formRepository;
	private $form;

	use ValidatesRequests;

	public function __construct(FormRepositoryInterface $formRepository)
	{
		$this->formRepository = $formRepository;
	}

	public function processSubmission(Request $request, Mailer $mailer)
	{
		$input = $request->all();

		$form = $this->formRepository->find($input['form_id']);

		if ($form) {

			$validator = $this->validate($request, $this->getValidationRules($form));

			$fileInputs = $form->getFileInputs();
			if ($fileInputs) {
				$input = $this->uploadFiles($request, $form, $fileInputs, $input);
			}

			if ($form->newsletter_signup) {
				$this->newsletterSignUp();
			}

			$recipients = array_filter(explode(',', $form->recipients));
			if ($recipients) {
				$this->sendMail($mailer, $form, $input, $recipients);
			}

			$this->logSubmission($form, $input);

			$response = $this->getSuccessResponse($form);
		} else {
			return abort(500);
		}

		return $response;
	}

	protected function getValidationRules(FormInterface $form)
	{
		$validationRules = [];
		foreach ($form->fields as $field) {
			$rules = [];
			if ($field->required) {
				$rules[] = 'required';
			}
			if ($field->validation) {
				$rules[] = $field->validation;
			}
			if ($rules) {
				$validationRules[$field->name] = implode('|', $rules);
			}
		}
		return $validationRules;
	}

	/**
	 * No default implementation
	 */
	protected function newsletterSignUp()
	{
		throw new FormException("Attempting to use newletter signup with no implementation", 1);
	}

	protected function uploadFiles($request, FormInterface $form, array $fields, array $input)
	{
		$formStorage = 'uploads/'.str_replace(' ', '', snake_case($form->name));
		foreach ($fields as $field) {
			$file = $request->file($field->name);
			if ($file) {
				$filePath = "{$formStorage}/{$file->getFileName()}";
				$file->move(public_path($filePath), $file->getClientOriginalName());

				$input[$field->name] = url("{$filePath}/{$file->getClientOriginalName()}");
			}
		}

		return $input;
	}

	protected function sendMail($mailer, FormInterface $form, array $input, array $recipients)
	{
		$mailer->send($this->getEmailTemplate($form), ['form' => $form, 'input' => $input], function($message) use ($form, $recipients){
			$message->subject($form->name.' form submission');
			foreach($recipients as $recipient) {
				$message->to(trim($recipient));
			}
		});
	}

	protected function getEmailTemplate(FormInterface $form)
	{
		return 'enquire::emails.form-submission';
	}

	protected function logSubmission(FormInterface $form, array $input)
	{
		$submission = Submission::create([
			'form_name' => $form->name
		]);

		foreach ($form->fields as $field) {
			if (array_key_exists($field->name, $input)) {
				$value = new Value([
					'label' => $field->label,
					'value' => $input[$field->name]
				]);
				$value->submission()->associate($submission);
				$value->save();
			}
		}
	}

	protected function getSuccessResponse(FormInterface $form)
	{
		return $this->getDefaultResponse($form)->withSuccess(true);
	}

	protected function getDefaultResponse(FormInterface $form)
	{
		return Redirect::to(URL::previous($form) . '#'. $form->html_id);
	}
}
