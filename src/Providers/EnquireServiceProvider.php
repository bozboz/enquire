<?php

namespace Bozboz\Enquire\Providers;

use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Forms\FormInterface;
use Bozboz\Enquire\Forms\FormRepository;
use Bozboz\Enquire\Forms\FormRepositoryInterface;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class EnquireServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bind(Forminterface::class, Form::class);
		$this->app->bind(FormRepositoryInterface::class, FormRepository::class);

		// Register honeypot package dependency and alias
		$this->app->register('Msurguy\Honeypot\HoneypotServiceProvider');
		AliasLoader::getInstance()->alias('Honeypot', '\Msurguy\Honeypot\HoneypotFacade');
	}

	public function boot()
	{
        $packageRoot = __DIR__ . '/../..';

		require "$packageRoot/src/Http/routes.php";

		$permissions = $this->app['permission.handler'];
		require "$packageRoot/src/permissions.php";

		$this->loadViewsFrom("$packageRoot/resources/views", 'enquire');

        $this->publishes([
            "$packageRoot/database/migrations" => database_path('migrations'),
            "$packageRoot/config/enquire.php" => config_path('enquire.php'),
        ]);

		$this->app['events']->listen('admin.renderMenu', function($menu)
		{
			if ($menu->gate('view_enquire_forms')) {
				$menu['Enquiries'] = array_filter([
					'Forms' => route('admin.enquiry-forms.index'),
					'Submissions' => route('admin.enquiry-form-submissions.index'),
				]);
			}
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
