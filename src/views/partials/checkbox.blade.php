{{ Form::label($field->name, $field->label) }}
{{ Form::hidden($field->name, 'No', ['id' => '']) }}
{{ Form::checkbox($field->name, 'Yes', Input::get($field->name), ['class' => 'form__input']) }}
{{ $errors->first($field->name, '<div class="form__error">:message</div>') }}
