<fieldset>
    <legend>{{ $field->label.($field->required ? ' *' : '') }}</legend>
    @if ( ! $field->required)
        {{ Form::hidden($field->name, 'None chosen', ['id' => '']) }}
    @endif
    @foreach (explode(PHP_EOL, $field->options) as $option)
        {{ Form::label(str_slug($field->name.$option), $option) }}
        {{ Form::radio($field->name, $option, null, ['id' => str_slug($field->name.$option)]) }}
    @endforeach
    {!! $errors->first($field->name, '<div class="form__error">:message</div>') !!}
</fieldset>
