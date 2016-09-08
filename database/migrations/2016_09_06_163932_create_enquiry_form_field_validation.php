<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnquiryFormFieldValidation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enquiry_form_field_validation', function (Blueprint $table) {
            $table->unsignedInteger('field_id');
            $table->unsignedInteger('rule_id');
            $table->timestamps();

            $table->primary(['field_id', 'rule_id']);

            $table->foreign('field_id')->references('id')->on('enquiry_form_fields')->onDelete('cascade');
            $table->foreign('rule_id')->references('id')->on('enquiry_form_validation')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('enquiry_form_field_validation');
    }
}
