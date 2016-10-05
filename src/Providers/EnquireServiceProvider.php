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

		$this->definePermissions();

		$this->loadViewsFrom("$packageRoot/resources/views", 'enquire');

        $this->publishes([
            "$packageRoot/database/migrations" => database_path('migrations'),
        ]);

        $this->mergeConfigFrom(
            "$packageRoot/config/enquire.php", 'enquire'
        );

		$this->app['events']->listen('admin.renderMenu', function($menu)
		{
			$links = [];
			if ($menu->gate('view_enquire_forms')) {
				$links['Enquiry Forms'] = route('admin.enquiry-forms.index');
			}
			if ($menu->gate('view_enquire_submissions')) {
				$links['Enquiry Submissions'] = route('admin.enquiry-form-submissions.index');
			}

			if ($links) {
				$menu['Enquiries'] = $links;
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

	private function definePermissions()
	{
		$permissions = $this->app['permission.handler'];
		$permissions->define([

		    'view_enquire_forms'   => 'Bozboz\Permissions\Rules\GlobalRule',
		    'create_enquire_forms' => 'Bozboz\Permissions\Rules\ModelRule',
		    'edit_enquire_forms'   => 'Bozboz\Permissions\Rules\ModelRule',
		    'delete_enquire_forms' => 'Bozboz\Permissions\Rules\ModelRule',

		    'view_enquire_submissions'   => 'Bozboz\Permissions\Rules\GlobalRule',
		    'create_enquire_submissions' => 'Bozboz\Permissions\Rules\ModelRule',
		    'edit_enquire_submissions'   => 'Bozboz\Permissions\Rules\ModelRule',
		    'delete_enquire_submissions' => 'Bozboz\Permissions\Rules\ModelRule',

		]);

	}
}
