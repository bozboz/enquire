# Enquire Package

## Installation

- `composer require bozboz/enquire <version>`
- Add routes, eg.
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

	Route::post('process-enquiry', [
		'as' => 'process-enquiry',
		'uses' => 'MailchimpFormController@processSubmission'
	]);
	```

