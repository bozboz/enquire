    <div class="enquiry-form__form" id="{{ $form->html_id }}"><!--
        @if ($form->wasSubmitted())
         --><div class="enquiry-form__confirmation">{!! $form->confirmation_message !!}</div><!--
        @else
            @if ($form->description)
             --><div class="enquiry-form__description">
                    {!!$form->description!!}
                </div><!--
            @endif

            @if ( ! $errors->isEmpty())
             --><div class="form__error">
                    <p><strong>There are errors in your submission. </strong></p>
                    <p>Please refer to the errors in the form and try again.</p>
                </div><!--
            @endif

         -->{{ Form::open(['route' => 'process-enquiry', 'files' => true, 'class' => 'js-form']) }}<!--
             -->{!! Honeypot::generate('my_name', 'my_time') !!}<!--
             -->{{ Form::hidden('form_id', $form->id) }}<!--
                @foreach ($form->fields as $field)
                 --><div class="enquiry-form__field">
                        @include($field->getView())
                    </div><!--
                @endforeach
             --><div class="enquiry-form__submit">
                    {!! Form::button($form->submit_button_text ?: 'Submit', ['class' => 'btn--small', 'type' => 'submit']) !!}
                </div><!--
         -->{{ Form::close() }}<!--
        @endif
 --></div>
