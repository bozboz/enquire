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
        collect($this->getConfig())->each(function($view, $type) {
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
        collect($this->getConfig())->each(function($view, $type) {
            DB::table('enquiry_form_fields')
                ->where('input_type', $type)
                ->update(['input_type' => $view]);
        });
    }

    private function getConfig()
    {
        return array_merge([
            'checkbox'      => 'enquire::partials.checkbox',
            'dropdown'      => 'enquire::partials.dropdown',
            'phone'         => 'enquire::partials.phone',
            'radio_buttons' => 'enquire::partials.radios',
            'text'          => 'enquire::partials.text',
            'textarea'      => 'enquire::partials.textarea',
            'current-url'   => 'enquire::partials.current-url',
            'file_upload'   => 'enquire::partials.file',
            'email'         => 'enquire::partials.email',
        ], config('enquire.fields'));
    }
}
