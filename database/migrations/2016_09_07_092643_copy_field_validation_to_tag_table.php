<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CopyFieldValidationToTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rules = DB::table('enquiry_form_fields')->selectRaw('
                validation,
                GROUP_CONCAT(id) as field_ids
            ')
            ->whereNotNull('validation')
            ->groupBy('validation')
            ->get();

        collect($rules)->each(function($rule) {
            $fields = collect(explode(',', $rule->field_ids));
            collect(explode('|', $rule->validation))->each(function($rule) use ($fields) {

                $ruleId = DB::table('enquiry_form_validation')->insertGetId([
                    'rule' => $rule,
                    'created_at' => new Carbon,
                    'updated_at' => new Carbon,
                ]);

                $fields->each(function($fieldId) use ($ruleId) {
                    DB::table('enquiry_form_field_validation')->insert([
                        'rule_id' => $ruleId,
                        'field_id' => $fieldId,
                        'created_at' => new Carbon,
                        'updated_at' => new Carbon,
                    ]);
                });
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
