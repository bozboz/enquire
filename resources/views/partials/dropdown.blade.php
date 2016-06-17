{{ Form::label($field->name, $field->label) }}
{!! Form::select($field->name, explode("\n", $field->options), null, array_filter([
    'class' => 'form__input',
    'required' => $field->required
])) !!}
{!! $errors->first($field->name, '<div class="form__error">:message</div>') !!}
