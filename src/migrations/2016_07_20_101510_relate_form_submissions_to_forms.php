<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RelateFormSubmissionsToForms extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$formSubmissions = DB::table('enquiry_submissions')->groupBy('form_name')->get();

		foreach ($formSubmissions as $formSubmission) {
			$form = DB::table('enquiry_forms')->where('name', $formSubmission->form_name)->first();
			DB::table('enquiry_submissions')->where('form_name', $form->name)->update([
				'form_id' => $form->id
			]);
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('enquiry_submissions')->update(['form_id' => null]);
	}

}
