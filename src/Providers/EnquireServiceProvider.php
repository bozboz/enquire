<?php

namespace Bozboz\Enquire\Providers;

use Bozboz\Enquire\Forms\Fields\Checkboxes;
use Bozboz\Enquire\Forms\Fields\Radios;
use Bozboz\Enquire\Forms\Form;
use Bozboz\Enquire\Forms\Fields\Field;
use Illuminate\Foundation\AliasLoader;
use Bozboz\Enquire\Forms\FormInterface;
use Illuminate\Support\ServiceProvider;
use Bozboz\Enquire\Forms\FormRepository;
use Bozboz\Enquire\Forms\Fields\FieldMapper;
use Bozboz\Enquire\Forms\FormRepositoryInterface;

class EnquireServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(Forminterface::class, Form::class);
        $this->app->bind(FormRepositoryInterface::class, FormRepository::class);

        $this->app->singleton('EnquireFieldMapper', function ($app) {
            return new FieldMapper;
        });
        Field::setMapper($this->app['EnquireFieldMapper']);

        // Register honeypot package dependency and alias
        $this->app->register('Msurguy\Honeypot\HoneypotServiceProvider');
        AliasLoader::getInstance()->alias('Honeypot', '\Msurguy\Honeypot\HoneypotFacade');
    }

    public function boot()
    {
        $this->loadConfig();
        $this->registerFields();
        $this->viewComposers();
        $this->definePermissions();
        $this->adminMenu();
        $this->jamFields();
    }

    protected function loadConfig()
    {
        $packageRoot = __DIR__ . '/../..';

        require "$packageRoot/src/Http/routes.php";

        $this->loadViewsFrom("$packageRoot/resources/views", 'enquire');

        $this->publishes([
            "$packageRoot/database/migrations" => database_path('migrations'),
        ]);

        $this->mergeConfigFrom(
            "$packageRoot/config/enquire.php", 'enquire'
        );
    }

    protected function registerFields()
    {
        collect(config('enquire.fields'))->each(function($view, $type) {
            $this->app['EnquireFieldMapper']->register($type, (new Field([
                'input_type' => $type,
            ]))->setView($view));
        });
        $this->app['EnquireFieldMapper']->register([
            'file_upload' => new \Bozboz\Enquire\Forms\Fields\FileUpload,
            'email'       => new \Bozboz\Enquire\Forms\Fields\Email,
            'dropdown'    => new \Bozboz\Enquire\Forms\Fields\Dropdown,
            'recipient-dropdown' => new \Bozboz\Enquire\Forms\Fields\RecipientDropdown,
            'checkboxes' => new Checkboxes,
            'radios_buttons' => new Radios,
        ]);
    }

    protected function viewComposers()
    {
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

            'create_enquire_form_fields' => 'Bozboz\Permissions\Rules\ModelRule',
            'edit_enquire_form_fields' => 'Bozboz\Permissions\Rules\ModelRule',
            'delete_enquire_form_fields' => 'Bozboz\Permissions\Rules\ModelRule',

        ]);

    }

    protected function jamFields()
    {
        if ($this->app->bound('FieldMapper')) {
            $this->app['FieldMapper']->register([
                'form' => \Bozboz\Enquire\Jam\Fields\Form::class,
            ]);
        }
    }

    protected function adminMenu()
    {
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
    }
}
