<?php

namespace Bozboz\Enquire;

use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Forms\FormRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class EnquireServiceProvider extends ServiceProvider
{
	public function register()
	{
		App::bind('Bozboz\Enquire\Forms\FormInterface', Form::class);
		App::bind('Bozboz\Enquire\Forms\FormRepositoryInterface', FormRepository::class);

		View::composer('enquire::partials.form', function($view)
		{
			$form = Form::forPath(Request::url())->with(['fields' => function($query) {
				$query->orderBy('sorting');
			}])->first();

			$view->with([
				'form' => $form
			]);
		});
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
	}
}
