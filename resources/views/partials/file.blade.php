{{ Form::label($field->name, $field->placeholder ?: $field->label) }}
{{ ($field->required ? '<em>*</em>' : '') }}
<span class="btn--file js-btn-file">
	<i class="fa fa-folder-open-o"></i>
	Browse&hellip;
	{{ Form::file($field->name) }}
</span>
<span class="btn--file__selected js-selected-file"></span>
{!! $errors->first($field->name, '<div class="form__error">:message</div>') !!}
