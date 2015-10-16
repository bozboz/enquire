# Enquire Package

## Installation

- Install package `composer require bozboz/enquire <version>`
- Add service provider to provider array
	```'php
	Bozboz\Enquire\EnquireServiceProvider',
	```
- Include form partial in any view you want to be able to display forms
	```
	@include('enquire::partials.form')
	```

## Usage

The default front end route doesn't support newsletter signup functionality due to not being able to anticipate
implementation. There is a MailChimpFormController which contains a basic MailChimp signup implementation.

To use this just override the package's frontend route with:

```php
Route::post('process-enquiry', [
	'as' => 'process-enquiry',
	'uses' => 'MailChimpFormController@processSubmission'
]);
```
