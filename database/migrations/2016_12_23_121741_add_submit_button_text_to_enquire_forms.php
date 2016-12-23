<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubmitButtonTextToEnquireForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enquiry_forms', function (Blueprint $table) {
            $table->text('submit_button_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enquiry_forms', function (Blueprint $table) {
            $table->dropColumn('submit_button_text');
        });
    }
}
