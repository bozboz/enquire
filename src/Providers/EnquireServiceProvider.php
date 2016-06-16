<?php

namespace Bozboz\Enquire\Providers;

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
        $packageRoot = __DIR__ . '/../..';

		require "$packageRoot/src/Http/routes.php";

		$this->loadViewsFrom("$packageRoot/resources/views", 'enquire');

        $this->publishes([
            "$packageRoot/database/migrations" => database_path('migrations'),
            "$packageRoot/config/enquire.php" => config_path('enquire.php'),
        ]);

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
