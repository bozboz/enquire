<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnquiryFormFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enquiry_form_fields', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('form_id')->unsigned();
			$table->integer('sorting')->unsigned();
			$table->string('label');
			$table->string('input_type');
			$table->string('placeholder')->nullable();
			$table->string('help_text')->nullable();
			$table->boolean('required');
			$table->string('validation')->nullable();
			$table->timestamps();

			$table->foreign('form_id')
			      ->references('id')->on('enquiry_forms')
			      ->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('enquiry_form_fields');
	}

}
