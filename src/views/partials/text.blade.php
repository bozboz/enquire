{{ Form::label($field->name, $field->label, ['class' => 'hidden']) }}
{{ Form::text($field->name, null, ['class' => 'form__input', 'placeholder' => $field->placeholder.($field->required ? ' *' : '')]) }}
{{ $errors->first($field->name, '<div class="form__error">:message</div>') }}
