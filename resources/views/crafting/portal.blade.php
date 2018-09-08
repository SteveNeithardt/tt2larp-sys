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
			<h2>@lang ('i.crafting')</h2>
		</div>
		<div class="col-md-6" v-if="listing_recipes" v-cloak>
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.search')" v-model="filter_name"></div>
				<div class="card-body">
					<ul class="list">
						<li class="thumb" v-for="recipe in filtered_recipes" v-on:click="editRecipe(recipe.id)">@{{ recipe.name }} (@{{ recipe.code }})</li>
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
					<div>TODO: abilities that show the thing</div>
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
						<span class="btn btn-outline-danger mr-2" v-on:click="deleteRecipe()" v-if="can_delete_recipe">@lang ('i.delete')</span>
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

			editing_recipe: false,
			recipe_id: null,
			name: null,
			code: null,
			description: null,
			ingredients: [],
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
		can_delete_recipe() {
			if (this.recipe_id == null) return false;

			return true;
		},
	},
	methods: {
		fetch_recipes() {
			axios.get("{{ route('get recipes') }}")
				.then(response => {
					this.recipes = response.data;
					this.listing_recipes = true;
				}).catch(errors => {});
		},
		resetRecipe() {
			this.editing_recipe = false;
			this.recipe_id = null;
			this.name = null;
			this.code = null;
			this.description = null;
			this.ingredients = [];
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
			}
		},
		addIngredient() {
			this.new_ingredient_id = this.new_ingredient_id - 1;
			this.ingredients.push({ id: this.new_ingredient_id, name: "", code: "" });
		},
		deleteIngredient(id) {
			this.ingredients = this.ingredients.filter(i => i.id != id);
		},
		deleteRecipe() {
			if (!this.can_delete_recipe) return;
			axios.post("{{ route('delete recipe') }}", {
				recipe_id: this.recipe_id,
			}).then(response => {
				if (response.data.success) {
					this.resetRecipe();
					this.fetch_recipes();
				}
			}).catch(errors => {});
		},
		storeRecipe() {
			if (!this.valid_recipe) return;
			axios.post("{{ route('store recipe') }}", {
					recipe_id: this.recipe_id,
					name: this.name,
					code: this.code,
					description: this.description,
					ingredients: this.ingredients,
				}).then(response => {
					if (response.data.success) {
						this.fetch_recipes();
					}
				}).catch(errors => {});
		},
		fetch_data() {
			this.fetch_recipes()
		},
	},
	mounted() {
		this.$nextTick(this.fetch_data);
	},
});
</script>
@endsection
