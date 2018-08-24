@extends ('layouts.app')

@section ('css')
<style>
[v-cloak] { display:none; }
</style>
@endsection

@section ('content')
<div class="container" id="vue">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">@lang ('i.abilities')</div>
				<div class="card-body row">
					<div class="col-md-6">
						<ul class="list">
							<li class="thumb" v-for="ability in abilities" v-on:click="edit(ability.id)" v-cloak>@{{ ability.name }}</li>
							<li class="thumb text-primary" v-on:click="edit()" v-cloak>@lang ('i.add')</li>
						</ul>
					</div>
					<div class="col-md-6">
						<div v-if="editing" v-cloak>
							<input class="form-control" type="text" v-model="name">
							<span class="btn btn-primary mt-3" v-on:click="submit()">@lang ('i.save')</span>
						</div>
					</div>
				</div>
			</div>
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
			abilities: {!! json_encode($abilities) !!},
			id: null,
			name: null,
			editing: false,
		}
	},
	methods: {
		edit: function(id = -1) {
			result = this.abilities.filter(a => a.id == id);
			if (result.length == 1) {
				this.id = result[0].id;
				this.name = result[0].name;
			} else {
				this.id = null;
				this.name = null;
			}
			this.editing = true;
		},
		submit: function() {
			axios.post("{{ route('store ability') }}", { name: this.name, id: this.id })
				.then(response => {
					this.abilities = response.data;
					this.id = null;
					this.name = null;
					this.editing = false;
				})
				.catch(errors => {});
		},
	},
});
</script>
@endsection
