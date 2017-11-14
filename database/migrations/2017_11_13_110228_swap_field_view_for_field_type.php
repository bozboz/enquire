<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SwapFieldViewForFieldType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        collect(config('enquire.fields'))->each(function($view, $type) {
            DB::table('enquiry_form_fields')
                ->where('input_type', $view)
                ->update(['input_type' => $type]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        collect(config('enquire.fields'))->each(function($view, $type) {
            DB::table('enquiry_form_fields')
                ->where('input_type', $type)
                ->update(['input_type' => $view]);
        });
    }
}
