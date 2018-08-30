@extends ('layouts.app')

@section ('css')
<style>
[v-cloak] { display:none; }
</style>
@endsection

@section ('content')
<div class="container" id="vue">
	<div class="row justify-content-center">
		<div class="col-md-12 mb-3">
			<h2>@lang ('i.library')</h2>
		</div>
	</div>
</div>
@endsection

@section ('js')
<script>
new Vue({
	el: '#vue',
	data() {
		return {
		}
	},
	computed: {
	},
	watch: {
	},
	methods: {
		fetch_data: function() {
			this.fetch_characters();
			this.fetch_abilities();
		},
		fetch_characters: function() {
			axios.get("{{ route('get characters') }}")
				.then(response => {
					this.characters = response.data;
				})
				.catch(errors => {});
		},
		fetch_abilities: function() {
			axios.get("{{ route('get abilities') }}")
				.then(response => {
					this.abilities = response.data;
				})
				.catch(errors => {});
		},
	},
	mounted() {
	},
});
</script>
@endsection
