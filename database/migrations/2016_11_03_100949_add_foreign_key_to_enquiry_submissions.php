<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToEnquirySubmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enquiry_submissions', function (Blueprint $table) {
            $table->unsignedInteger('form_id')->after('form_name')->nullable();

            $table->foreign('form_id')->references('id')->on('enquiry_forms')->onDelete('set null');
        });

        DB::statement('
            UPDATE enquiry_submissions, enquiry_forms
            SET enquiry_submissions.form_id = enquiry_forms.id
            WHERE enquiry_submissions.form_name = enquiry_forms.name
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('
            UPDATE enquiry_submissions, enquiry_forms
            SET enquiry_submissions.form_name = enquiry_forms.name
            WHERE enquiry_submissions.form_id = enquiry_forms.id
        ');

        Schema::table('enquiry_submissions', function (Blueprint $table) {
            $table->dropForeign('enquiry_submissions_form_id_foreign');
            $table->dropColumn('form_id');
        });
    }
}
