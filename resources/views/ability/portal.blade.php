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
			<h2 class="d-flex align-items-center">
				@lang ('i.abilities')
				<div class="delete-icon ml-2" v-if="!deleting_abilities && !editing" v-on:click="delete_abilities(true)" v-cloak></div>
				<div class="cancel-icon ml-2" v-if="deleting_abilities" v-on:click="delete_abilities(false)" v-cloak></div>
			</h2>
		</div>
		<div class="col-md-6" v-if="listing_abilities" v-cloak>
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.search')" v-model="filter_name"></div>
				<div class="card-body">
					<ul class="list">
						<li class="d-flex justify-content-between align-items-center" v-for="ability in filtered_abilities" v-cloak>
							<div class="flex-grow-1 thumb" v-on:click="edit(ability.id)">@{{ ability.name }}</div>
							<div class="delete-icon" v-on:click="deleteAbility(ability.id)" v-if="deleting_abilities"></div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-6" v-if="listing_abilities" v-cloak>
			<span class="btn btn-primary mb-2" v-on:click="edit()" v-if="!editing" v-cloak>@lang ('i.add')</span>
			<div class="card" v-if="editing" v-cloak>
				<div class="card-header">@lang ('i.edit')</div>
				<div class="card-body">
					<input class="form-control" type="text" v-model="name">
					<span class="btn btn-primary mt-3" v-on:click="submit()" v-if="valid_ability">@lang ('i.submit')</span>
					<span class="btn btn-outline-secondary mt-3" v-on:click="reset()">@lang ('i.cancel')</span>
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
			listing_abilities: false,
			abilities: null,
			filter_name: null,
			editing: false,
			id: null,
			name: null,
			deleting_abilities: false,
		}
	},
	computed: {
		filtered_abilities() {
			if (this.filter_name == null) return this.abilities;
			else return this.abilities.filter(a => a.name.indexOf(this.filter_name) > -1);
		},
		valid_ability() {
			return (this.name != null &&
				this.name.length > 2);
		},
	},
	methods: {
		fetch_abilities() {
			if (this.editing) return;
			this.reset();
			axios.get("{{ route('get abilities') }}").then(response => {
				this.abilities = response.data;
				this.listing_abilities = true;
			}).catch(errors => {});
		},
		delete_abilities(active) {
			if (this.editing) return;
			this.deleting_abilities = active;
		},
		edit(id = -1) {
			this.reset();
			this.editing = true;
			if (id == -1) return;
			var result = this.abilities.filter(a => a.id == id);
			if (result.length == 1) {
				this.id = result[0].id;
				this.name = result[0].name;
			}
		},
		ability_name(id) {
			var result = this.abilities.filter(a => a.id == id);
			return result.length == 1 ? result[0].name : 'undefined';
		},
		async deleteAbility(id) {
			const res = await swal({
				title: "@lang ('i.Are you sure?')",
				text: "@lang ('i.This will delete \'%P%\' permanently.')".replace('%P%', this.ability_name(id)),
				type: 'error',
				showCancelButton: true,
			});
			if (res.value == true) {
				axios.post("{{ route('delete ability') }}", {
					id: id,
				}).then(response => {
					if (response.data.success) {
						this.editing = false;
						this.fetch_abilities();
					}
				}).catch(errors => {
				console.log(errors);
					swal({
						title: errors.response.status,
						text: errors.response.data.message,
						type: 'error',
						timeout: 2500,
					});
				});
			}
		},
		reset() {
			this.editing = false;
			this.id = null;
			this.name = null;
		},
		submit() {
			axios.post("{{ route('store ability') }}", {
				id: this.id,
				name: this.name,
			}).then(response => {
				if (response.data.success) {
					this.editing = false;
					this.fetch_abilities();
				}
			})
			.catch(errors => {});
		},
	},
	mounted() {
		this.$nextTick(this.fetch_abilities);
	},
});
</script>
@endsection
