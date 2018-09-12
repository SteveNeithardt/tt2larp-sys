@extends ('layouts.app')

@section ('content')
<div class="container" id="vue">
	<div class="row justify-content-center">
		<div class="col-md-12 mb-3">
			<h2 class="d-flex align-items-center">
				@lang ('i.characters')
				<div class="delete-icon ml-2" v-if="!deleting_characters && !editing" v-on:click="delete_characters(true)" v-cloak></div>
				<div class="cancel-icon ml-2" v-if="deleting_characters" v-on:click="delete_characters(false)" v-cloak></div>
			</h2>
		</div>
		<div class="col-md-6">
			<div class="card" v-cloak>
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.search')" v-model="filter_name"></div>
				<div class="card-body">
					<ul class="list">
						<li class="d-flex align-items-center" v-for="character in filtered_characters">
							<div class="flex-grow-1 thumb" v-on:click="editCharacter(character.id)">@{{ character.name }} (@{{ character.code }})</div>
							<div class="delete-icon" v-on:click="deleteCharacter(character.id)" v-if="deleting_characters"></div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<span class="btn btn-primary mb-2" v-on:click="editCharacter()" v-if="!editing" v-cloak>@lang ('i.add')</span>
			<div class="card" v-if="editing" v-cloak>
				<div class="card-header">@lang ('i.edit')</div>
				<div class="card-body">
					<input class="form-control my-2" type="text" v-model="name" placeholder="@lang ('i.name')">
					<input class="form-control my-2" type="text" v-model="player" placeholder="@lang ('i.player')">
					<input class="form-control my-2" type="text" v-model="code" placeholder="@lang ('i.code')">
					<textarea class="form-control my-2" v-model="description"></textarea>
					<hr>
					<div class="d-flex justify-content-around flex-wrap">
						<div v-for="ability in abilities" class="p-2 col-md-3">
							<div>@{{ ability.name }}</div>
							<div>
								<input type="radio" id="ability.name" value="0" v-model="ability.value">
								<input type="radio" id="ability.name" value="1" v-model="ability.value">
								<input type="radio" id="ability.name" value="2" v-model="ability.value">
								<input type="radio" id="ability.name" value="3" v-model="ability.value">
							</div>
						</div>
					</div>
					<span v-if="valid_character" class="btn btn-primary mt-3" v-on:click="submit()" v-cloak>@lang ('i.submit')</span>
					<span class="btn btn-secondary mt-3" v-on:click="resetCharacter()">@lang ('i.cancel')</span>
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
			code: null,
			player: null,
			abilities: null,
			description: null,
			deleting_characters: false,
		}
	},
	computed: {
		filtered_characters() {
			if (this.filter_name == null) return this.characters;
			else return this.characters.filter(c =>
				c.name.indexOfInsensitive(this.filter_name) > -1 ||
				(c.code != null && c.code.indexOfInsensitive(this.filter_name) > -1)
			);
		},
		valid_character() {
			return (this.name != null && this.name.length > 2 &&
				this.player != null && this.player.length > 2 &&
				this.code != null && this.code.length > 2 && this.code.length < 9);
		},
	},
	watch: {
		filter_name() {
			this.editing = false;
		},
	},
	methods: {
		fetch_data() {
			this.fetch_characters();
			this.fetch_abilities();
		},
		fetch_characters() {
			this.resetCharacter();
			axios.get("{{ route('get characters') }}")
				.then(response => {
					this.characters = response.data;
				})
				.catch(errors => {});
		},
		fetch_abilities() {
			axios.get("{{ route('get abilities') }}")
				.then(response => {
					this.abilities = response.data;
				})
				.catch(errors => {});
		},
		delete_characters(active) {
			if (this.editing) return;
			this.deleting_characters = active;
		},
		resetCharacter() {
			this.deleting_characters = false;
			this.editing = false;
			this.id = null;
			this.name = null;
			this.code = null;
			this.player = null;
			this.description = null;
		},
		editCharacter(id = -1) {
			this.resetCharacter();
			this.editing = true;
			for (index in this.abilities) {
				this.abilities[index].value = 0;
			}
			if (id == -1) return;
			var result = this.characters.filter(c => c.id == id);
			if (result.length == 1) {
				var character = result[0];
				this.id = character.id;
				this.name = character.name;
				this.code = character.code;
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
		submit() {
			if (!this.valid_character) return;
			axios.post("{{ route('store character') }}", {
				id: this.id,
				name: this.name,
				code: this.code,
				player: this.player,
				abilities: this.abilities,
				description: this.description,
			}).then(response => {
				this.characters = response.data;
				this.resetCharacter();
			}).catch(errors => {});
		},
		character_name(id) {
			var result = this.characters.filter(c => c.id == id);
			return result.length == 1 ? result[0].name : 'undefined';
		},
		async deleteCharacter(id) {
			if (! this.deleting_characters) return;
			const res = await swal({
				title: "@lang ('i.Are you sure?')",
				text: "@lang ('i.This will delete \'%P%\' permanently.')".replace('%P%', this.character_name(id)),
				type: 'error',
				showCancelButton: true,
				focusCancel: true,
			});
			this.deleting_characters = false;
			if (res.value == true) {
				axios.post("{{ route('delete character') }}", {
					id: id,
				}).then(response => {
					if (response.data.success) {
						this.fetch_characters();
					}
				}).catch(errors => {
				});
			}
		},
	},
	mounted() {
		this.$nextTick(this.fetch_data);
	},
});
</script>
@endsection
