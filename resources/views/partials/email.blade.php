{{ Form::label($field->name, $field->label.($field->required ? ' *' : '')) }}
{{ Form::email($field->name, null, array_filter([
    'class' => 'form__input',
    'placeholder' => $field->placeholder,
    'required' => $field->required
])) }}
{!! $errors->first($field->name, '<div class="form__error">:message</div>') !!}
