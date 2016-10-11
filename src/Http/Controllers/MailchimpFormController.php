<?php

namespace Bozboz\Enquire\Http\Controllers;

use Bozboz\Enquire\Exceptions\SignupException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Mailchimp;
use Mailchimp_List_AlreadySubscribed;
use Mailchimp_ValidationError;
use Mailchimp_Error;

class MailchimpFormController extends FormController
{
	public function newsletterSignUp($form)
	{
		$this->validate($this->request, $this->getMailchimpValidationRules());

		try {
			$api = new Mailchimp(Config::get('enquire.mailchimp_api_key'));
			$api->lists->subscribe(
				$form->list_id,
				$this->getEmail(),
				$this->getMergeVars()
			);
		} catch (Mailchimp_List_AlreadySubscribed $e) {
			throw new SignupException($e->getMessage());
		} catch(Mailchimp_ValidationError $e) {
			$this->reportMailchimpError($e);
		} catch (Mailchimp_Error $e) {
			$this->reportMailchimpError($e);
		}
	}

	private function reportMailchimpError($exception)
	{
		if (App::environment('production')) {
			$message = 'Mailchimp error on ' . gethostname() . '. Please pass the following info onto a dev:' . PHP_EOL . PHP_EOL;
			$message .= sprintf('%s: message = %s, error code = %s', get_class($exception), $exception->getMessage(), $exception->getCode()) . PHP_EOL;
			$message .= 'Form data = ' . print_r($this->request->all(), true) . PHP_EOL;
			$message .= '$_SERVER = '. print_r($_SERVER, true) . PHP_EOL;

			mail(Config::get('app.support_email'), sprintf('Mailchimp error on %s', gethostname()), $message);
		}
	}

	protected function getMailchimpValidationRules()
	{
		return [
			'email' => 'required|email',
		];
	}

	protected function getEmail()
	{
		return ['email' => $this->request->get('email')];
	}

	protected function getMergeVars()
	{
		return [];
	}
}