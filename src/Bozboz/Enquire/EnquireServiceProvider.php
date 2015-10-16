<?php

namespace Bozboz\Enquire;

use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Forms\FormRepository;
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
				'form' => $this->app[FormRepositoryInterface::class]->getForCurrentPath()
			]);
		});
	}
}
