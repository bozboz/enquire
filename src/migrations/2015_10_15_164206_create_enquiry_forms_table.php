<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnquiryFormsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enquiry_forms', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 500);
			$table->boolean('newsletter_signup');
			$table->string('recipients', 500)->nullable();
			$table->text('confirmation_message');
			$table->boolean('status');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('enquiry_forms');
	}

}
