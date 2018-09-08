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
				@lang ('i.crafting')
				<div class="delete-icon ml-2" v-if="listing_recipes && !deleting_recipes && !editing_recipe" v-on:click="delete_recipes(true)" v-cloak></div>
				<div class="cancel-icon ml-2" v-if="listing_recipes && deleting_recipes" v-on:click="delete_recipes(false)" v-cloak></div>
			</h2>
		</div>
		<div class="col-md-6" v-if="listing_recipes" v-cloak>
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.search')" v-model="filter_name"></div>
				<div class="card-body">
					<ul class="list">
						<li class="d-flex align-items-center" v-for="recipe in filtered_recipes">
							<div class="flex-grow-1 thumb" v-on:click="editRecipe(recipe.id)">@{{ recipe.name }} (@{{ recipe.code }})</div>
							<div class="delete-icon" v-on:click="deleteRecipe(recipe.id)" v-if="deleting_recipes"></div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<span class="btn btn-primary mb-2" v-on:click="editRecipe()" v-if="!editing_recipe" v-cloak>@lang ('i.add')</span>
			<div class="card" v-if="editing_recipe" v-cloak>
				<div class="card-header">@lang ('i.edit')</div>
				<div class="card-body">
					<input class="form-control my-2" type="text" v-model="name" placeholder="@lang ('i.name')">
					<input class="form-control my-2" type="text" v-model="code" placeholder="@lang ('i.code')">
					<textarea class="form-control my-2" v-model="description"></textarea>
					<hr>
					<ul class="list">
						<li v-for="recipe_ability in recipe_abilities" class="d-flex justify-content-between align-items-center">
							<div class="col-md-8"><select2 v-model="recipe_ability.id" :options="abilities"></select2></div>
							<input type="number" class="form-control col-md-2" v-model="recipe_ability.value">
							<div class="delete-icon" v-on:click="deleteAbility(recipe_ability.id)"></div>
						</li>
					</ul>
					<div class="btn btn-primary my-2" v-on:click="addAbility()">@lang ('i.add ability')</div>
					<hr>
					<ul class="list">
						<li v-for="ingredient in ingredients" class="d-flex justify-content-between align-items-center">
							<input type="text" class="form-control col-md-5" v-model="ingredient.name" placeholder="@lang ('i.name')">
							<input type="text" class="form-control col-md-5" v-model="ingredient.code" placeholder="@lang ('i.code')">
							<div class="delete-icon" v-on:click="deleteIngredient(ingredient.id)"></div>
						</li>
					</ul>
					<div class="btn btn-primary my-2" v-on:click="addIngredient()">@lang ('i.add ingredient')</div>
					<hr>
					<div class="mt-3">
						<span class="btn btn-primary mr-2" v-on:click="storeRecipe()" v-if="valid_recipe">@lang ('i.save recipe')</span>
						<span class="btn btn-outline-secondary" v-on:click="resetRecipe()">@lang ('i.cancel')</span>
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
			listing_recipes: false,
			recipes: null,
			filter_name: null,
			abilities: null,

			deleting_recipes: false,
			editing_recipe: false,
			recipe_id: null,
			name: null,
			code: null,
			description: null,
			ingredients: [],
			recipe_abilities: [],
			new_ingredient_id: -1,
		}
	},
	computed: {
		filtered_recipes() {
			if (this.filter_name == null) return this.recipes;
			else return this.recipes.filter(r =>
				r.name.indexOf(this.filter_name) > -1 ||
				(r.code != null && r.code.indexOf(this.filter_name) > -1)
			);
		},
		valid_recipe() {
			return (this.name != null && this.name.length > 2 &&
				this.code != null && this.code.length > 2 &&
				this.description != null && this.description.length > 0);
		},
	},
	methods: {
		fetch_recipes() {
			this.resetRecipe();
			axios.get("{{ route('get recipes') }}")
				.then(response => {
					this.recipes = response.data;
					this.listing_recipes = true;
				}).catch(errors => {});
		},
		fetch_abilities() {
			axios.get("{{ route('get abilities') }}")
				.then(response => {
					this.abilities = response.data.map(a => {
						return { id: a.id, text: a.name };
					});
				})
				.catch(errors => {});
		},
		delete_recipes(active) {
			if (this.editing_recipe) return;
			this.deleting_recipes = active;
		},
		resetRecipe() {
			this.deleting_recipes = false;
			this.editing_recipe = false;
			this.recipe_id = null;
			this.name = null;
			this.code = null;
			this.description = null;
			this.ingredients = [];
			this.recipe_abilities = [];
		},
		editRecipe(id = -1) {
			this.resetRecipe();
			this.editing_recipe = true;
			if (id == -1) return;

			var result = this.recipes.filter(r => r.id == id);
			if (result.length == 1) {
				var recipe = result[0];
				this.recipe_id = recipe.id;
				this.name = recipe.name;
				this.code = recipe.code;
				this.description = recipe.description;
				this.ingredients = recipe.ingredients;
				this.recipe_abilities = recipe.abilities;
			}
		},
		addIngredient() {
			this.new_ingredient_id = this.new_ingredient_id - 1;
			this.ingredients.push({ id: this.new_ingredient_id, name: "", code: "" });
		},
		deleteIngredient(id) {
			this.ingredients = this.ingredients.filter(i => i.id != id);
		},
		addAbility() {
			this.recipe_abilities.push({ id: -1, value: 0 });
		},
		deleteAbility(id) {
			this.recipe_abilities = this.recipe_abilities.filter(a => a.id != id);
		},
		get_recipe_name(id) {
			var result = this.recipes.filter(r => r.id = id);
			return result.length == 1 ? result[0].name : 'undefined';
		},
		async deleteRecipe(id) {
			const res = await swal({
				title: "@lang ('i.Are you sure?')",
				text: "@lang ('i.This will delete \'%P%\' permanently.')".replace('%P%', this.get_recipe_name(id)),
				type: 'error',
				showCancelButton: true,
			});
			this.deleting_recipes = false;
			if (res.value == true) {
				axios.post("{{ route('delete recipe') }}", {
					id: id,
				}).then(response => {
					if (response.data.success) {
						this.fetch_recipes();
					}
					}).catch(errors => {
					});
			}
		},
		storeRecipe() {
			if (!this.valid_recipe) return;
			axios.post("{{ route('store recipe') }}", {
					recipe_id: this.recipe_id,
					name: this.name,
					code: this.code,
					description: this.description,
					ingredients: this.ingredients,
					abilities: this.recipe_abilities,
				}).then(response => {
					if (response.data.success) {
						this.fetch_recipes();
					}
				}).catch(errors => {});
		},
		fetch_data() {
			this.fetch_recipes();
			this.fetch_abilities();
		},
	},
	mounted() {
		this.$nextTick(this.fetch_data);
	},
});
</script>
@endsection
