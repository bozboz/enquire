{{ Form::label($field->name, $field->label.($field->required ? ' *' : '')) }}
{!! Form::select(
    $field->name,
    $field->getSelectOptions(),
    null,
    array_filter([
        'class' => 'form__input',
        'required' => $field->required
    ])
) !!}
{!! $errors->first($field->name, '<div class="form__error">:message</div>') !!}
