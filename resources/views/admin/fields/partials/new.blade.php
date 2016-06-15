<div class="btn-group pull-right space-left">
	<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		<i class="fa fa-plus-square"></i>
		New Field
		<span class="caret"></span>
		<span class="sr-only">Toggle Dropdown</span>
	</button>
	<ul class="dropdown-menu" role="menu">
		@foreach (Config::get('enquire::fields') as $fieldType => $namespace)
			<li><a href="{{ action($controller . '@createForForm', [Input::get('form_id'), $fieldType]) }}">
				{{ studly_case($fieldType) }}
			</a></li>
		@endforeach
	</ul>
</div>