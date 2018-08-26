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
			<h2>@lang ('i.abilities')</h2>
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.search')" v-model="filter_name"></div>
				<div class="card-body">
					<ul class="list">
						<li class="thumb" v-for="ability in filtered_abilities" v-on:click="edit(ability.id)" v-cloak>@{{ ability.name }}</li>
						<li class="thumb text-primary" v-on:click="edit()" v-cloak>@lang ('i.add')</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card" v-if="editing" v-cloak>
				<div class="card-header">@lang ('i.edit')</div>
				<div class="card-body">
					<input class="form-control" type="text" v-model="name">
					<span class="btn btn-primary mt-3" v-on:click="submit()">@lang ('i.save')</span>
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
			filter_name: null,
			editing: false,
			id: null,
			name: null,
		}
	},
	computed: {
		filtered_abilities: function() {
			if (this.filter_name == null) return this.abilities;
			else return this.abilities.filter(a => a.name.indexOf(this.filter_name) > -1);
		},
	},
	methods: {
		edit: function(id = -1) {
			var result = this.abilities.filter(a => a.id == id);
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
			axios.post("{{ route('store ability') }}", {
				name: this.name,
				id: this.id,
			}).then(response => {
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
