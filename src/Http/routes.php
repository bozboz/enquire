<?php

Route::group(['middleware' => 'web', 'prefix' => 'admin', 'namespace' => 'Bozboz\Enquire\Http\Controllers\Admin', 'before' => 'auth'], function()
{
    Route::resource('enquiry-forms', 'FormAdminController');
    Route::get('enquiry-forms/report/{form}', [
        'uses' => 'FormAdminController@downloadReport',
        'as' => 'admin.enquiry-forms.download-report',
    ]);

    Route::resource('enquiry-form-fields', 'FormFieldAdminController');
    Route::get('enquiry-form-fields/{formId}/{fieldTypeAlias}/create', [
        'as' => 'admin.enquiry-form-field.create',
        'uses' => 'FormFieldAdminController@createForForm'
    ]);

    Route::resource('enquiry-form-submissions', 'FormSubmissionAdminController', ['except' => ['show']]);
    Route::get('enquiry-form-submissions/report', [
        'uses' => 'FormSubmissionAdminController@downloadReport',
        'as' => 'admin.enquiry-form-submissions.download-report',
    ]);
});

Route::group(['middleware' => 'web', 'namespace' => 'Bozboz\Enquire\Http\Controllers'], function()
{
    Route::post('process-enquiry', [
        'as' => 'process-enquiry',
        'uses' => 'FormController@processSubmission'
    ]);
});
