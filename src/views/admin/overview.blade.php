@extends('admin::overview')

@section('report')
<div class="table-responsive">
@if ($report->hasRows())
	<ol class="secret-list faux-table{{ $sortableClass }}" data-model="{{ $identifier }}">

		<li class="faux-table-row faux-table-heading">
		@if ($sortableClass)
			<div class="faux-cell cell-small"></div>
		@endif
		@foreach ($report->getHeadings() as $columnHeading)
			<div class="faux-cell">{{ $columnHeading }}</div>
		@endforeach
			<div class="no-wrap faux-cell"></div>
		</li>

	@foreach ($report->getRows() as $row)
		<li class="faux-table-row" data-id="{{ $row->getId() }}">
		@if ($sortableClass)
			<div class="faux-cell cell-small">
				<i class="fa fa-sort sorting-handle"></i>
			</div>
		@endif
		@foreach ($row->getColumns() as $name => $value)
			<div class="faux-cell">{{ $value }}</div>
		@endforeach
			<div class="no-wrap faux-cell">
				<a href="{{ URL::route('admin.enquiry-form.download.csv', [$row->getId()]) }}" class="btn btn-warning btn-sm" type="submit">
					<i class="fa fa-file"></i>
					Export
				</a>

				@if ($row->check($canEdit))
					<a href="{{ URL::action($editAction, [$row->getId()]) }}" class="btn btn-info btn-sm" type="submit">
						<i class="fa fa-pencil"></i>
						Edit
					</a>
				@endif

				@if ($row->check($canDelete))
					{{ Form::open(['class' => 'inline-form', 'action' => [ $destroyAction, $row->getId() ], 'method' => 'DELETE']) }}
						<button class="btn btn-danger btn-sm" data-warn="true" type="submit"><i class="fa fa-minus-square"></i> Delete</button>
					{{ Form::close() }}
				@endif
			</div>
		</li>
	@endforeach
	</ol>
@else
	<p>Nothing here yet. Why not add something?</p>
@endif
</div>
@stop
