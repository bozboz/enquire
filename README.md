# Enquire Package

## Installation

- Install package `composer require bozboz/enquire <version>`
- Add service provider to provider array
	```'php
	Bozboz\Enquire\EnquireServiceProvider',
	```
- Add admin routes
	```php
	Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'before' => 'auth'], function()
	{
		Route::resource('enquiry-forms', 'FormAdminController');
		Route::resource('enquiry-form-fields', 'FormFieldAdminController');
		Route::get('enquiry-form-fields/{formId}/{fieldTypeAlias}/create', [
			'as' => 'admin.enquiry-form-field.create',
			'uses' => 'FormFieldAdminController@createForForm'
		]);

		Route::resource('enquiry-form-submissions', 'FormSubmissionAdminController');
	});
	```
- Add front end route
	```php
	Route::post('process-enquiry', [
		'as' => 'process-enquiry',
		'uses' => 'FormController@processSubmission'
	]);
	```
    - Use `MailChimpFormController` if you intend to use MailChimp for news letter signup
- Add items to admin menu in site's AdminServiceProvider
	```php
	$menu['Enquiries'] = [
	        'Forms' => route('admin.enquiry-forms.index'),
	        'Submissions' => route('admin.enquiry-form-submissions.index'),
	];
	```
- Include form partial in any view you want to be able to display forms
	```
	@include('enquire::partials.form')
	```