@if (isset($form) && $form->exists)
	<div class="wrapper--listing enquire-form" id="{{ $form->html_id }}"><!--
	 --><div class="enquire-form__heading">{{ $form->name }}</div><!--

		@if (Session::has('success'))
		 --><div class="enquire-form__confirmation">{{ $form->confirmation_message }}</div><!--
		@else
		 -->{{ Form::open(['route' => 'process-enquiry', 'files' => true]) }}<!--
			 -->{{ Form::hidden('form_id', $form->id) }}<!--
				@foreach ($form->fields as $field)
				 --><div class="enquire-form__field">
						@include($field->input_type)
					</div><!--
				@endforeach
			 --><div class="enquire-form__field--submit">
					{{ HTML::decode(Form::button('Submit <i class="fa fa-check"></i>', ['class' => 'btn', 'type' => 'submit'])) }}
				</div><!--
		 -->{{ Form::close() }}<!--
		@endif

 --></div>
@endif
