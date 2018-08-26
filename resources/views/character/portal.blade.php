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
			<h2>@lang ('i.characters')</h2>
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.search')" v-model="filter_name"></div>
				<div class="card-body">
					<ul class="list">
						<li class="thumb" v-for="character in filtered_characters" v-on:click="edit(character.id)" v-cloak>@{{ character.name }}</li>
						<li class="thumb text-primary" v-on:click="edit()" v-cloak>@lang ('i.add')</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card" v-if="editing" v-cloak>
				<div class="card-header">@lang ('i.edit')</div>
				<div class="card-body">
					<input class="form-control my-2" type="text" v-model="name" placeholder="@lang ('i.name')">
					<input class="form-control my-2" type="text" v-model="player" placeholder="@lang ('i.player')">
					<textarea class="form-control my-2" v-model="description"></textarea>
					<hr>
					<div class="d-flex justify-content-around flex-wrap">
						<div v-for="ability in abilities" class="p-2 col-md-3">
							<!--<ability-select :name="ability.name" :value="ability.value" :id="ability.id" v-on:update:value="update_ability_value($event.id, $event.value)"></ability-select>-->
							<div>@{{ ability.name }}</div>
							<div>
								<input type="radio" id="ability.name" value="0" v-model="ability.value">
								<input type="radio" id="ability.name" value="1" v-model="ability.value">
								<input type="radio" id="ability.name" value="2" v-model="ability.value">
								<input type="radio" id="ability.name" value="3" v-model="ability.value">
							</div>
						</div>
					</div>
					<span v-if="valid" class="btn btn-primary mt-3" v-on:click="submit()" v-cloak>@lang ('i.submit')</span>
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
			characters: null,
			filter_name: null,
			editing: false,
			id: null,
			name: null,
			player: null,
			abilities: null,
			description: null,
		}
	},
	computed: {
		filtered_characters: function() {
			if (this.filter_name == null) return this.characters;
			else return this.characters.filter(c => c.name.indexOf(this.filter_name) > -1);
		},
		valid: function() {
			return (this.name != null && this.player != null &&
				this.name.length > 3 && this.player.length > 3);
		},
	},
	watch: {
		filter_name: function() {
			this.editing = false;
		},
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
		edit: function(id = -1) {
			this.editing = true;
			this.id = null;
			this.name = null;
			this.player = null;
			this.description = null;
			for (index in this.abilities) {
				this.abilities[index].value = 0;
			}
			if (id == -1) return;
			var result = this.characters.filter(c => c.id == id);
			if (result.length == 1) {
				var character = result[0];
				this.id = character.id;
				this.name = character.name;
				this.player = character.player;
				this.description = character.description;
				for (var index in this.abilities) {
					var ability = this.abilities[index];
					var ab = character.abilities.filter(a => a.id == ability.id);
					if (ab.length == 1) {
						ability.value = ab[0].value;
					} else {
						ability.value = 0;
					}
				}
			}
		},
		update_ability_value: function(ability_id, new_value) {
			var ability = this.abilities.filter(a => a.id = ability_id);
			if (ability.length == 1)
				console.log(ability[0]);
			console.log(ability_id + ' -> ' + new_value);
		},
		submit: function() {
			axios.post("{{ route('store character') }}", {
				id: this.id,
				name: this.name,
				player: this.player,
				abilities: this.abilities,
				description: this.description,
			}).then(response => {
				this.characters = response.data;
				this.id = null;
				this.name = null;
				this.player = null;
				this.editing = false;
				this.description = null;
			}).catch(errors => {});
		},
	},
	mounted() {
		this.$nextTick(this.fetch_data);
	},
});
</script>
@endsection
