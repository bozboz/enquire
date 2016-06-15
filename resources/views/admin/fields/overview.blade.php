@extends('admin::overview')

@section('report_header')
	@include('enquire::admin.fields.partials.new')
	<h1>{{ $heading }}</h1>

	@if (Session::has('model'))
		@foreach(Session::get('model') as $msg)
			<div id="js-alert" class="alert alert-success" data-alert="alert">
				{{ $msg }}
			</div>
		@endforeach
	@endif

	@include('admin::partials.sort-alert')

	{{ $report->getHeader() }}
@stop

@section('report_footer')
	{{ $report->getFooter() }}
	@include('enquire::admin.fields.partials.new')
	<a href="{{ route('admin.enquiry-forms.index') }}" class="pull-right btn btn-default"><i class="fa fa-list-alt"></i>Back to Listing</a>
@stop
