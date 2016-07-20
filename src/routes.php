<?php

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'before' => 'auth'], function()
{
	Route::resource('enquiry-forms', 'FormAdminController');
	Route::resource('enquiry-form-fields', 'FormFieldAdminController');
	Route::get('enquiry-form-fields/{formId}/{fieldTypeAlias}/create', [
		'as' => 'admin.enquiry-form-field.create',
		'uses' => 'FormFieldAdminController@createForForm'
	]);

	Route::resource('enquiry-form-submissions', 'FormSubmissionAdminController');

	Route::model('form', 'Bozboz\\Enquire\\Forms\\Form');
	Route::get('enquiry-forms/download/{form}', [
		'as' => 'admin.enquiry-form.download.csv',
		'uses' => 'FormAdminController@downloadCsv',
	]);
});

Route::post('process-enquiry', [
	'as' => 'process-enquiry',
	'uses' => 'FormController@processSubmission'
]);
