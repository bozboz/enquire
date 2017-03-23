@if (isset($forms))
    @foreach($forms ?: [] as $form)
		@include('enquire::form')
    @endforeach
@endif
