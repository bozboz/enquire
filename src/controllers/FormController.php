<?php

use Bozboz\Enquire\Forms\FormException;
use Bozboz\Enquire\Forms\FormInterface;
use Bozboz\Enquire\Forms\FormRepositoryInterface;
use Bozboz\Enquire\Submissions\Submission;
use Bozboz\Enquire\Submissions\Value;

class FormController extends BaseController
{
	private $formRepository;
	private $form;

	public function __construct(FormRepositoryInterface $formRepository)
	{
		$this->formRepository = $formRepository;
	}

	public function processSubmission()
	{
		$input = Input::all();

		$form = $this->formRepository->find($input['form_id']);

		if ($form) {

			$validator = $this->validate($input, $form);

			if ($validator->passes()) {

				$fileInputs = $form->getFileInputs();
				if ($fileInputs) {
					$input = $this->uploadFiles($form, $fileInputs, $input);
				}

				if ($form->newsletter_signup) {
					$this->newsletterSignUp();
				}

				$recipients = array_filter(explode(',', $form->recipients));
				if ($recipients) {
					$this->sendMail($form, $input, $recipients);
				}

				$this->logSubmission($form, $input);

				$response = $this->getSuccessResponse($form);
			} else {
				$response = $this->getFailureResponse($form, $validator->messages());
			}
		} else {
			$response = $this->getDefaultResponse($form);
		}

		return $response;
	}

	protected function validate(array $input, FormInterface $form)
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
		return Validator::make($input, $validationRules);
	}

	/**
	 * No default implementation
	 */
	protected function newsletterSignUp()
	{
		throw new FormException("Attempting to use newletter signup with no implementation", 1);
	}

	protected function uploadFiles(FormInterface $form, array $fields, array $input)
	{
		$formStorage = 'uploads/'.str_replace(' ', '', snake_case($form->name));
		foreach ($fields as $field) {
			$file = Input::file($field->name);
			if ($file) {
				$filePath = "{$formStorage}/{$file->getFileName()}";
				$file->move(public_path($filePath), $file->getClientOriginalName());

				$input[$field->name] = url("{$filePath}/{$file->getClientOriginalName()}");
			}
		}

		return $input;
	}

	protected function sendMail(FormInterface $form, array $input, array $recipients)
	{
		Mail::send($this->getEmailTemplate($form), ['form' => $form, 'input' => $input], function($message) use ($form, $recipients){
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
		$submission = new Submission([
			'form_name' => $form->name,
			'form_id' => $form->id,
		]);
		$submission->save();

		$values = [];
		foreach ($form->fields as $field) {
			if (array_key_exists($field->name, $input)) {
				$values[] = new Value([
					'label' => $field->label,
					'value' => $input[$field->name]
				]);
			}
		}
		$submission->values()->saveMany($values);
	}

	protected function getSuccessResponse(FormInterface $form)
	{
		return $this->getDefaultResponse($form)->withSuccess(true);
	}

	protected function getFailureResponse(FormInterface $form, $errors)
	{
		return $this->getDefaultResponse($form)->withErrors($errors)->withInput();
	}

	protected function getDefaultResponse(FormInterface $form)
	{
		return Redirect::to(URL::previous($form) . '#'. $form->html_id);
	}
}
