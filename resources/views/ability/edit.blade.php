@extends ('layouts.app')

@section ('content')
<div class="container" id="vue">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<h3>@lang ('i.ability')</h3>
			<div class="row form-group">
				<div class="text-right col-md-2">@lang ('i.name')</div>
				<div class="col-md-10"><input class="form-control" type="text" v-model="name" placeholder="@lang ('i.name')"></div>
			</div>
			<span class="btn btn-primary" v-on:click="submit()">@lang ('i.submit')</span>
		</div>
	</div>
</div>
@endsection

@section ('js')
<script>
new Vue({
	el: '#id',
	data() {
		return {
@if ($ability === null)
			name: "",
			id: null,
@else
			name: "{{ $ability->name }}",
			id: {{ $ability->id }},
@endif
		}
	},
	methods: {
		submit: function() {
			console.log('ih');return;
			axios.post("{{ route('store ability') }}", { name: this.name, id: this.id })
				.then(response => {})
				.catch(errors => {});
		},
	},
});
</script>
@endsection
