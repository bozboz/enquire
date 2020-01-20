<fieldset>
    <legend>{{ $field->label.($field->required ? ' *' : '') }}</legend>

    {{ Form::hidden($field->name, 'None chosen', ['id' => '']) }}

    @foreach (explode(PHP_EOL, $field->options) as $option)
        {{ Form::checkbox("{$field->name}[]", $option, null, ['id' => str_slug($field->name.$option), 'class' => 'checkbox']) }}
        {{ Form::label(str_slug($field->name.$option), $option) }}
    @endforeach

    {!! $errors->first($field->name, '<div class="form__error">:message</div>') !!}
</fieldset>