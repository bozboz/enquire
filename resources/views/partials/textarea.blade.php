{{ Form::label($field->name, $field->label, ['class' => 'sr-only']) }}
{{ Form::textarea($field->name, null, array_filter([
    'class' => 'form__input',
    'placeholder' => $field->placeholder.($field->required ? ' *' : ''),
    'required' => $field->required
])) }}
{!! $errors->first($field->name, '<div class="form__error">:message</div>') !!}
