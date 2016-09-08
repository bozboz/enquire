<?php

namespace Bozboz\Enquire\Http\Controllers;

use Bozboz\Enquire\Events\SuccessfulFormSubmission;
use Bozboz\Enquire\Forms\FormException;
use Bozboz\Enquire\Forms\FormInterface;
use Bozboz\Enquire\Forms\FormRepositoryInterface;
use Bozboz\Enquire\Submissions\Submission;
use Bozboz\Enquire\Submissions\Value;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Event;

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

		if ( ! $form) {
			return abort(500, "Form {$input['form_id']} not found!");
		}

		$this->validate($request, $this->getValidationRules($form, $input));

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

		Event::fire(new SuccessfulFormSubmission($form, $input, $recipients));

		$this->logSubmission($form, $input);

		$response = $this->getSuccessResponse($request, $form);

		return $response;
	}

	protected function getValidationRules(FormInterface $form, $input)
	{
		$validationRules = [
			'my_name' => 'honeypot',
			'my_time' => 'required|honeytime:5'
		];

		$form->load('fields.validationRules');

		foreach ($form->fields as $field) {
			$rules = collect();

			if ($field->required) {
				$rules->push('required');
			}

			$field->validationRules->each(function($validation) use ($rules) {
				$rules->push($validation->rule);
			});

			if ($rules->count()) {
				if (is_array($input[$field->name])) {
					foreach ($input[$field->name] as $name => $value) {
						$validationRules["{$field->name}.{$name}"] = $rules->implode('|');
					}
				} else {
					$validationRules[$field->name] = $rules->implode('|');
				}
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
		$mailer->send($this->getEmailTemplate($form), compact('form', 'input'), function($message) use ($form, $input, $recipients){
			$message->subject($form->name.' form submission');
			$message->from(
				array_key_exists('email', $input) ? $input['email'] : Config::get('enquire.from_address')
			);
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
					'value' => implode(' ', (array)$input[$field->name])
				]);
				$value->submission()->associate($submission);
				$value->save();
			}
		}
	}

	protected function getSuccessResponse(Request $request, FormInterface $form)
	{
		if ($request->ajax()) {
			return $this->getAjaxResponse($form);
		} else {
			return $this->getDefaultResponse($form)->withSuccess(true);
		}
	}

	protected function getDefaultResponse(FormInterface $form)
	{
		return Redirect::to(URL::previous($form) . '#'. $form->html_id);
	}

	protected function getAjaxResponse(FormInterface $form)
	{
		return [
			'message' => $form->confirmation_message
		];
	}
}
