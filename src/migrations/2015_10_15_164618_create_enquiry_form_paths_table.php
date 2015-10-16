<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnquiryFormPathsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('enquiry_form_paths', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('form_id')->unsigned();
			$table->string('path', 500);
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
		Schema::drop('enquiry_form_paths');
	}

}
