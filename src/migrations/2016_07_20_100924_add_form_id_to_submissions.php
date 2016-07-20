<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormIdToSubmissions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('enquiry_submissions', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';

			$table->integer('form_id')
				->unsigned()
				->nullable();

			$table->foreign('form_id')
				->references('id')
				->on('enquiry_forms')
				->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('enquiry_submissions', function(Blueprint $table)
		{
			$table->dropForeign('enquiry_submissions_form_id_foreign');
			$table->dropColumn('form_id');
		});
	}

}
