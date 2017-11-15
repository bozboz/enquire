<?php

namespace Bozboz\Enquire\Http\Controllers;

use Bozboz\Enquire\Events\SuccessfulFormSubmission;
use Bozboz\Enquire\Exceptions\FormException;
use Bozboz\Enquire\Exceptions\SignupException;
use Bozboz\Enquire\Forms\FormInterface;
use Bozboz\Enquire\Forms\FormRepositoryInterface;
use Bozboz\Enquire\Submissions\Submission;
use Bozboz\Enquire\Submissions\Value;
use Event;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;

class FormController extends Controller
{
	protected $formRepository;
	protected $mailer;
	protected $request;
	protected $form;

	use ValidatesRequests;

	public function __construct(FormRepositoryInterface $formRepository, Mailer $mailer, Request $request)
	{
		$this->formRepository = $formRepository;
		$this->mailer = $mailer;
		$this->request = $request;
	}

	public function processSubmission()
	{
		$input = $this->request->all();

		$form = $this->formRepository->find($input['form_id']);
		$this->form = $form;

		if ( ! $form) {
			throw FormException::notFound($input['form_id']);
		}

		$this->validate($this->request, $this->getValidationRules($form, $input));

		$fileInputs = $form->getFileInputs();
		if ($fileInputs) {
			$input = $this->uploadFiles($form, $fileInputs, $input);
		}

		if ($form->newsletter_signup) {
			try {
				$this->newsletterSignUp($form);
			} catch (SignupException $e) {
				return $this->getFailResponse($form, $e->getMessage());
			}
		}

		$recipients = array_filter(explode(',', $form->recipients));
		if ($recipients) {
			$this->sendMail($form, $input, $recipients);
		}

		Event::fire(new SuccessfulFormSubmission($form, $input, $recipients));

		$this->logSubmission($form, $input);

		$response = $this->getSuccessResponse($form);

		return $response;
	}

	protected function getValidationRules(FormInterface $form, $input)
	{
		$validationRules = [
			'my_name' => 'honeypot',
		];

		if ( ! key_exists('retry', $input)) {
			$validationRules['my_time'] = 'required|honeytime:5';
		}

		$form->load('fields.validationRules');

		foreach ($form->fields as $field) {
			$validationRules = array_merge($validationRules, $field->getValidation($input));
		}

		return $validationRules;
	}

    /**
     * Get the URL we should redirect to after form errors.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        return url()->previous() . '#' . $this->form->html_id;
    }

	/**
	 * No default implementation
	 */
	protected function newsletterSignUp($form)
	{
		throw FormException::noSignup();
	}

	protected function uploadFiles(FormInterface $form, array $fields, array $input)
	{
		$formStorage = 'uploads/'.str_slug($form->name);
		foreach ($fields as $field) {
			$file = $this->request->file($field->name);
			if ($file) {
				$filename = time() . '-' . str_replace(' ', '-', $file->getClientOriginalName());
				$file->move(public_path($formStorage), $filename);
				$input[$field->name] = url($formStorage . '/' . $filename);
			}
		}

		return $input;
	}

	protected function sendMail(FormInterface $form, array $input, array $recipients)
	{
		$this->mailer->send($this->getEmailTemplate($form), compact('form', 'input'), function($message) use ($form, $input, $recipients){
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
			'form_name' => $form->name
		]);
		$submission->form()->associate($form);
		$submission->save();

		$submission->logFields($form->fields, $input);
	}

	protected function getSuccessResponse(FormInterface $form)
	{
		if ($this->request->ajax()) {
			return $this->getAjaxResponse($form->confirmation_message);
		} else {
			return $this->getDefaultResponse($form)->with([$form->getSessionHandle() => true]);
		}
	}

	protected function getFailResponse(FormInterface $form, $message)
	{
		if ($this->request->ajax()) {
			return $this->getAjaxFailResponse($message);
		} else {
			return $this->getDefaultResponse($form)->withErrors($message);
		}
	}

	protected function getDefaultResponse(FormInterface $form)
	{
		return Redirect::to(URL::previous($form) . '#'. $form->html_id);
	}

	protected function getAjaxResponse($message)
	{
		return [
			'message' => $message
		];
	}

	protected function getAjaxFailResponse($error)
	{
		return Response::json([
			'error' => $error
		], 422);
	}
}
