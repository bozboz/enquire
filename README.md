# Enquire Package

## Installation

- Install package `composer require bozboz/enquire <version>`
- Add service provider to provider array `'Bozboz\Enquire\EnquireServiceProvider',`
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