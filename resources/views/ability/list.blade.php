@extends ('layouts.app')

@section ('content')
<div class="container">
    <div class="row justify-content-center">
	<ul>
	@foreach ($abilities as $ability)
		<li><a href="{{ route('view ability', [ 'id' => $ability->id ]) }}">{{ $ability->name }}</a><a class="btn btn-primary btn-outline" href="{{ route('edit ability', [ 'id' => $ability->id ]) }}">@lang ('i.edit')</a></li>
	@endforeach
	</ul>
	<a class="btn btn-primary" href="{{ route('edit ability') }}">@lang ('i.add')</a></li>
    </div>
</div>
@endsection
