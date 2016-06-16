<?php

namespace Bozboz\Enquire\Http\Controllers;

class MailchimpFormController extends FormController
{
	public function newsletterSignUp()
	{
		$validator = $this->validateSignup();
		if ($validator->passes()) {
			try {
				$api = new Mailchimp(Config::get('app.mailchimp_api_key'));
				$api->lists->subscribe(
					Config::get('app.mailchimp_main_list_id'),
					$this->getEmail(),
					$this->getMergeVars()
				);
			} catch(Mailchimp_ValidationError $e) {
				$this->reportMailchimpError($e);
			} catch (Mailchimp_Error $e) {
				$this->reportMailchimpError($e);
			}
		}
	}

	private function reportMailchimpError($exception)
	{
		if (App::environment('production')) {
			$message = 'Mailchimp error on ' . gethostname() . '. Please pass the following info onto a dev:' . PHP_EOL . PHP_EOL;
			$message .= sprintf('%s: message = %s, error code = %s', get_class($exception), $exception->getMessage(), $exception->getCode()) . PHP_EOL;
			$message .= 'Form data = ' . print_r(Input::all(), true) . PHP_EOL;
			$message .= '$_SERVER = '. print_r($_SERVER, true) . PHP_EOL;

			mail(Config::get('app.support_email'), sprintf('Mailchimp error on %s', gethostname()), $message);
		}
	}

	protected function validateSignup()
	{
		return Validator::make(
			['email' => Input::get('email')],
			['email' => 'required|email']
		);
	}

	protected function getEmail()
	{
		return ['email' => Input::get('email')];
	}

	protected function getMergeVars()
	{
		return [];
	}
}