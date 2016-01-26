<?php

namespace Bozboz\Enquire;

use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Forms\FormRepository;
use Bozboz\Enquire\Forms\FormInterface;
use Bozboz\Enquire\Forms\FormRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class EnquireServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind(Forminterface::class, Form::class);
		$this->app->bind(FormRepositoryInterface::class, FormRepository::class);
	}

	public function boot()
	{
		$this->package('bozboz/enquire');

		require __DIR__ . '/../../routes.php';

		$this->app['events']->listen('admin.renderMenu', function($menu)
		{
			$menu['Enquiries'] = [
				'Forms' => route('admin.enquiry-forms.index'),
				'Submissions' => route('admin.enquiry-form-submissions.index'),
			];
		});

		// When the form partial is used, bind the form for the current request
		// to it, if it exists.
		$this->app['view']->composer('enquire::partials.form', function($view)
		{
			$view->with([
				'forms' => $this->app[FormRepositoryInterface::class]->getForCurrentPath()
			]);
		});
	}
}
