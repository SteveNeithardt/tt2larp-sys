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
			<span class="btn btn-outline-secondary my-3" v-on:click="back()" v-if="editing_article" v-cloak>@lang ('i.back')</span>
		</div>
		<div class="col-md-8" v-if="listing_articles" v-cloak>
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.search')" v-model="filter_name"></div>
				<div class="card-body">
					<ul class="list">
						<li class="thumb" v-for="article in filtered_articles" v-on:click="editArticle(article.id)">@{{ article.name }} (@{{ article.code }})</li>
					</ul>
					<span class="btn btn-primary" v-if="!adding_article" v-on:click="addArticle()">@lang ('i.add')</span>
					<div class="d-flex mt-3 justify-content-between" v-if="adding_article">
						<input class="form-control col-md-5" type="text" v-model="article_name" placeholder="@lang ('i.name')">
						<input class="form-control col-md-4" type="text" v-model="article_code" placeholder="@lang ('i.code')">
						<div class="col-md-2"><span class="btn btn-primary" v-if="valid_article" v-on:click="storeArticle()">@lang ('i.submit')</span></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12 mb-4" v-if="editing_article" v-cloak>
			<div class="card">
				<div class="card-header">
					<div v-if="!adding_article">
						@{{ article_name }} (@{{ article_code }})
						<span class="btn btn-outline-primary ml-3" v-on:click="addArticle()">@lang ('i.edit article')</span>
					</div>
					<div class="d-flex justify-content-between" v-if="adding_article">
						<input class="form-control col-md-4" type="text" v-model="article_name" placeholder="@lang ('i.name')">
						<input class="form-control col-md-4" type="text" v-model="article_code" placeholder="@lang ('i.code')">
						<div class="col-md-3">
							<span class="btn btn-primary" v-if="valid_article" v-on:click="storeArticle(false)">@lang ('i.submit')</span>
							<span class="btn btn-outline-secondary" v-on:click="resetAddArticle()">@lang ('i.cancel')</span> 
						</div>
					</div>
				</div>
				<div class="card-body">
					<ul class="list">
						<li class="thumb" v-for="part in parts" v-on:click="editPart(part.id)">@{{ part.name }}</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-6" v-if="editing_article" v-cloak>
			<div class="card" v-if="editing_part">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.part name')" v-model="part_name"></div>
				<div class="card-body">
					<div class="d-flex justify-content-between mb-3">
						<div><select2 v-model="part_ability_id" :options="abilities">
							<option value="-1">@lang ('i.no ability')</option>
						</select2></div>
						<input type="number" class="form-control col-md-4" v-model="part_ability_value">
					</div>
					<textarea class="form-control" v-model="part_description" placeholder="@lang ('i.part description')"></textarea>
					<div class="mt-3">
						<span class="btn btn-primary mr-2" v-on:click="storePart()" v-if="valid_part">@lang ('i.save part')</span>
						<span class="btn btn-outline-danger mr-2" v-on:click="deletePart()">@lang ('i.delete')</span>
						<span class="btn btn-outline-secondary" v-on:click="resetPart()">@lang ('i.cancel')</span>
					</div>
				</div>
			</div>
			<div v-if="!editing_part" v-cloak>
				<span class="btn btn-primary" v-on:click="editPart()">@lang ('i.add part')</span>
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
			listing_articles: false,
			articles: null,
			filter_name: null,

			adding_article: false,
			editing_article: false,
			article_id: null,
			article_name: null,
			article_code: null,

			abilities: null,
			parts: null,

			editing_part: false,
			part_id: null,
			part_name: null,
			part_description: null,
			part_ability_id: null,
			part_ability_value: null,
		}
	},
	computed: {
		filtered_articles() {
			if (this.filter_name == null) return this.articles;
			else return this.articles.filter(a =>
				a.name.indexOf(this.filter_name) > -1 ||
				(a.code != null && a.code.indexOf(this.filter_name) > -1)
			);
		},
		valid_article() {
			return (this.article_name != null &&
				this.article_name.length > 2 &&
				this.article_code != null &&
				this.article_code.length > 2);
		},
		valid_part() {
			return (
				this.part_name != null &&
				this.part_name.length > 2 &&
				this.part_description != null && (
					(
						(this.part_ability_id == null || this.part_ability_id < 0) &&
						(this.part_ability_value == null || this.part_ability_value.length == 0)
					) || (
						(this.part_ability_id != null && this.part_ability_id > 0) &&
						(this.part_ability_value != null && this.part_ability_value > 0)
					)
				)
			);
		},
	},
	watch: {
	},
	methods: {
		back() {
			if (this.editing_article) {
				this.resetArticle();
				this.listing_articles = true;
				return;
			}
		},
		fetch_articles: function() {
			this.resetArticle();
			axios.get("{{ route('get articles') }}")
				.then(response => {
					this.articles = response.data;
					this.listing_articles = true;
				})
				.catch(errors => {});
		},
		fetch_parts() {
			this.resetPart();
			const url = "{{ route('get parts', ['article_id' => '%R%']) }}";
			axios.get(url.replace('%R%', this.article_id))
				.then(response => {
					this.parts = response.data;
				})
				.catch(errors =>{});
		},
		fetch_abilities: function() {
			axios.get("{{ route('get abilities') }}")
				.then(response => {
					this.abilities = response.data.map(a => {
						return { id: a.id, text: a.name };
					});
				})
				.catch(errors => {});
		},
		resetArticle() {
			this.adding_article = false;
			this.editing_article = false;
			this.article_id = null;
			this.article_name = null;
			this.parts = null;
			this.resetPart();
		},
		resetAddArticle() {
			this.adding_article = false;
		},
		resetPart() {
			this.editing_part = false;
			this.part_id = null;
			this.part_name = null;
			this.part_description = null;
			this.part_ability_id = -1;
			this.part_ability_value = null;
		},
		addArticle() {
			this.adding_article = true;
		},
		editArticle(id = -1) {
			this.resetArticle();
			if (id == -1) return;
			this.listing_articles = false;
			this.editing_article = true;

			var result = this.articles.filter(a => a.id == id);
			if (result.length == 1) {
				var article = result[0];
				this.article_id = article.id;
				this.article_name = article.name;
				this.article_code = article.code;
				this.fetch_parts();
			}
		},
		editPart(id = -1) {
			this.resetPart();
			this.editing_part = true;
			if (id == -1) return;

			var result = this.parts.filter(p => p.id == id);
			if (result.length == 1) {
				var part = result[0];
				this.part_id = part.id;
				this.part_name = part.name;
				this.part_description = part.description;
				this.part_ability_id = part.ability_id == null ? -1 : part.ability_id;
				this.part_ability_value = part.min_value;
			}
		},
		storeArticle(fetch = true) {
			if (!this.valid_article) return;
			axios.post("{{ route('store article') }}", {
					id: this.article_id,
					name: this.article_name,
					code: this.article_code,
				})
				.then(response => {
					if (response.data.success) {
						if (fetch) {
							this.fetch_articles();
						} else {
							this.adding_article = false;
						}
					} else {
						alert(response.data.message);
					}
				})
				.catch(errors => {});
		},
		storePart() {
			if (!this.valid_part) return;
			const url = "{{ route('store part', ['article_id' => '%R%']) }}";
			axios.post(url.replace('%R%', this.article_id), {
					id: this.part_id,
					name: this.part_name,
					description: this.part_description,
					ability_id: this.part_ability_id < 0 ? null : this.part_ability_id,
					min_value: this.part_ability_value,
				})
				.then(response => {
					if (response.data.success) {
						this.fetch_parts();
					} else {
						alert(response.data.message);
					}
				})
				.catch(errors => {});
		},
		deletePart() {
			alert("TODO");
		},
		fetch_data: function() {
			this.fetch_articles();
			this.fetch_abilities();
		},
	},
	mounted() {
		this.$nextTick(this.fetch_data);
	},
});
</script>
@endsection
