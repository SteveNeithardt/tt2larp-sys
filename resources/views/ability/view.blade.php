@extends ('layouts.app')

@section ('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">@lang ('i.ability')</div>
				<div class="card-body">
					{{ $ability->name }}
				</div>
			</div>
			<a class="btn btn-primary" href="{{ route('edit ability', [ 'id' => $ability->id ]) }}">@lang ('i.edit')</a>
		</div>
	</div>
</div>
@endsection
