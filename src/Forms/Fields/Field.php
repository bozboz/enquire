<?php

namespace Bozboz\Enquire\Forms\Fields;

use Bozboz\Admin\Base\Model;
use Bozboz\Enquire\Forms\Form;
use Bozboz\Admin\Fields\TextField;
use Bozboz\Admin\Fields\HiddenField;
use Bozboz\Enquire\Submissions\Value;
use Bozboz\Admin\Fields\CheckboxField;
use Bozboz\Admin\Fields\TextareaField;
use Illuminate\Support\Facades\Config;
use Bozboz\Admin\Base\Sorting\Sortable;
use Bozboz\Admin\Fields\BelongsToManyField;
use Bozboz\Admin\Base\Sorting\SortableTrait;
use Bozboz\Enquire\Forms\Fields\FieldMapper;
use Bozboz\Enquire\Forms\Fields\Validation\Rule;

class Field extends Model implements Sortable
{
    use SortableTrait;

    protected $table = 'enquiry_form_fields';

    protected $guarded = [
        'id',
        'validationRules_relationship',
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

    public function getForeignKey()
    {
        return 'field_id';
    }

    public function getView()
    {
        return $this->view;
    }

    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function sortBy()
    {
        return 'sorting';
    }

    public function isRecipient()
    {
        return false;
    }

    public function getNameAttribute()
    {
        return trim(preg_replace('/[^\w]+/', '_', (strtolower($this->label))), trim('_'));
    }

    public function getTypeAttribute()
    {
        return $this->input_type;
    }

    public function getTypeLabelAttribute()
    {
        return $this->getDescriptiveName();
    }

    protected function sortPrependOnCreate()
    {
        return false;
    }

    public function getValidation($input)
    {
        $rules = collect();
        $validationRules = [];

        if ($this->required) {
            $rules->push('required');
        }

        $this->validationRules->each(function($validation) use ($rules) {
            $rules->push($validation->rule);
        });

        if ($rules->count()) {
            if (array_key_exists($this->name, $input) && is_array($input[$this->name])) {
                foreach ($input[$this->name] as $name => $value) {
                    $validationRules["{$this->name}.{$name}"] = $rules->implode('|');
                }
            } else {
                $validationRules[$this->name] = $rules->implode('|');
            }
        }

        return $validationRules;
    }

    public function validationRules()
    {
        return $this->belongsToMany(Rule::class, 'enquiry_form_field_validation')->withTimestamps();
    }

    public function getValidator()
    {
        return new FieldValidator;
    }

    public function logValue($submission, $input)
    {
        $value = new Value([
            'label' => $this->label,
            'value' => $this->formatInputForLog($input)
        ]);
        $value->submission()->associate($submission);
        $value->save();
    }

    public function formatInputForLog($input)
    {
        if ( ! key_exists($this->name, $input)) {
            return '';
        }
        return trim(implode(', ', (array)$input[$this->name]));
    }

    public function formatInputForEmail($input)
    {
        return nl2br(e($this->formatInputForLog($input)));
    }

    public function getDescriptiveName()
    {
        return preg_replace('/([A-Z])/', ' $1', studly_case($this->input_type));
    }

    public function getDefaultFields()
    {
        return [
            new HiddenField(['name' => 'input_type']),
            new TextField(['name' => 'type_label', 'disabled' => 'disabled']),
            new TextField(['name' => 'label', 'label' => 'Name']),
            new TextField(['name' => 'placeholder']),
            new CheckboxField(['name' => 'required']),
            new BelongsToManyField(app(Validation\RuleDecorator::class), $this->validationRules(), [
                'key' => 'rule',
                'data-tags' => 'true',
                'help_text' => '
                <p>Type the name of a validation rule and press the enter key to add it.<br>
                Available rules:</p>
                <p>
                <strong>min:value</strong><br>
                Minimum text length
                </p>
                <p>
                <strong>max:value</strong><br>
                Maximum text length
                </p>
                <p>
                <strong>email</strong><br>
                Value must be a valid email address
                </p>
                <a href="https://laravel.com/docs/5.2/validation#available-validation-rules" target="_blank">See list of available validation rules.</a>
                ',
            ]),
            new HiddenField(['name' => 'form_id']),
        ];
    }

    public function getOptionFields()
    {
        return [
            in_array($this->input_type, Config::get('enquire.fields_with_options', []))
                ? new TextareaField('options', [
                    'help_text' => 'Enter options with a new line between each one'
                ])
                : null
        ];
    }

    public function saveOptionFields($input) {}

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool   $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        if (array_key_exists('input_type', $attributes) && static::getMapper()->has($attributes['input_type'])) {
            $model = static::getMapper()->get($attributes['input_type']);
        } else {
            $model = new self;
        }
        $model->fill((array) $attributes);
        $model->exists = $exists;

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

    /**
     * Get the names of the many-to-many relationships defined on the model
     * that need to be processed.
     *
     * @return array
     */
    public function getSyncRelations()
    {
        return [];
    }

    /**
     * Get the names of the sortable many-to-many relationships on the model
     * return array
     */
    public function getSortableSyncRelations()
    {
        return [];
    }

    /**
     * Get the names (and associated attribute to use) of list-style
     * many-to-many relationship on the model that should be saved.
     *
     * @return array
     */
    public function getListRelations()
    {
        return [];
    }
}
