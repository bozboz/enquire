<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Base\Model;
use Bozboz\Enquire\Forms\Form;
use Illuminate\Support\Facades\Config;
use Bozboz\Admin\Base\Sorting\Sortable;
use Bozboz\Admin\Base\Sorting\SortableTrait;
use Bozboz\Enquire\Forms\Fields\FieldMapper;
use Bozboz\Enquire\Forms\Fields\Validation\Rule;

class Field extends Model implements Sortable
{
    use SortableTrait;

    protected $table = 'enquiry_form_fields';

    protected $fillable = [
        'form_id',
        'label',
        'input_type',
        'placeholder',
        'help_text',
        'required',
        'options',
    ];

    protected $nullable = [
        'help_text',
    ];

    protected $view;

    protected static $mapper;

    public static function setMapper(FieldMapper $mapper)
    {
        static::$mapper = $mapper;
    }

    public static function getMapper()
    {
        return static::$mapper;
    }

    public function getView()
    {
        return $this->view;
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function sortBy()
    {
        return 'sorting';
    }

    public function getNameAttribute()
    {
        return trim(preg_replace('/[^\w]+/', '_', (strtolower($this->label))), trim('_'));
    }

    public function getTypeAttribute()
    {
        return array_search($this->input_type, Config::get('enquire.fields'));
    }

    public function getTypeLabelAttribute()
    {
        return studly_case($this->type);
    }

    public function hasOptions()
    {
        return array_search($this->type, Config::get('enquire.fields_with_options'));
    }

    protected function sortPrependOnCreate()
    {
        return false;
    }

    public function validationRules()
    {
        return $this->belongsToMany(Rule::class, 'enquiry_form_field_validation')->withTimestamps();
    }

    public function getValidator()
    {
        return new FieldValidator;
    }

    public function formatInputForLog($value)
    {
        return trim(implode(', ', (array)$value));
    }

    public function formatInputForEmail($value)
    {
        return nl2br(e($field->formatInputForLog($value)));
    }

    public function getDescriptiveName()
    {
        return preg_replace('/([A-Z])/', ' $1', studly_case($this->input_type));
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool   $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        if (array_key_exists('input_type', $attributes)) {
            $class = config("enquire.field-models.{$attributes['input_type']}") ?: static::class;
        } else {
            $class = static::class;
        }
        $model = new $class((array) $attributes);
        $model->exists = $exists;

        if (array_key_exists('view', $attributes)) {
            $model->view = $attributes['view'];
        }

        return $model;
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array  $attributes
     * @param  string|null  $connection
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $newInstanceAttributes = [];
        $attributes = (array) $attributes;
        if (array_key_exists('input_type', $attributes)) {
            $newInstanceAttributes['view'] = config('enquire.fields.'.$attributes['input_type']);
            $newInstanceAttributes['input_type'] = $attributes['input_type'];
        }
        $model = $this->newInstance($newInstanceAttributes, true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->connection);

        return $model;
    }
}
