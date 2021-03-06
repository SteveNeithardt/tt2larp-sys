@extends ('layouts.app')

@section ('content')
<div class="container" id="vue">
	<div class="row justify-content-center">
		<div class="col-md-12 mb-3">
			<h2 class="d-flex align-items-center">
				@lang ('i.library')
				<div class="delete-icon ml-2" v-if="listing_articles && !deleting_articles && !editing_article" v-on:click="delete_articles(true)" v-cloak></div>
				<div class="cancel-icon ml-2" v-if="listing_articles && deleting_articles" v-on:click="delete_articles(false)" v-cloak></div>
			</h2>
			<span class="btn btn-secondary my-3" v-on:click="back()" v-if="editing_article" v-cloak>@lang ('i.back')</span>
		</div>
		<div class="col-md-8" v-if="listing_articles" v-cloak>
			<div class="card">
				<div class="card-header d-flex align-items-center">
					<div class="col-md-6">
						<input class="form-control" type="text" placeholder="@lang ('i.search')" v-model="filter_name">
					</div>
					<div class="col-md-6">
						<select2 :options="stations" v-model="filter_station" v-if="stations != null">
							<option value="-1">@lang ('i.No filter')</option>
						</select2>
					</div>
				</div>
				<div class="card-body">
					<span class="btn btn-primary mb-3" v-if="!adding_article" v-on:click="addArticle()">@lang ('i.add')</span>
					<div class="d-flex mb-3 justify-content-between" v-if="adding_article">
						<input class="form-control col-md-3" type="text" v-model="article_name" placeholder="@lang ('i.name')">
						<input class="form-control col-md-3" type="text" v-model="article_code" placeholder="@lang ('i.code')">
						<div class="col-md-2">
							<select2 :options="stations" v-model="article_station"></select2>
						</div>
						<div class="col-md-3 d-flex align-items-center">
							<div class="cancel-icon" v-on:click="resetArticle()"></div> 
							<div class="save-icon ml-2" v-if="valid_article" v-on:click="storeArticle()"></div>
						</div>
					</div>
					<ul class="list">
						<li class="d-flex align-items-center" v-for="article in filtered_articles">
							<div class="flex-grow-1 thumb" v-on:click="editArticle(article.id)">@{{ article.name }} (@{{ article.code }})</div>
							<div class="delete-icon" v-on:click="deleteArticle(article.id)" v-if="deleting_articles"></div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-6 mb-4" v-if="editing_article" v-cloak>
			<div class="card">
				<div class="card-header">
					<div class="d-flex align-items-center my-1" v-if="!adding_article">
						@{{ article_name }} (@{{ article_code }}) @{{ station_name }}
						<div class="edit-icon ml-3" v-on:click="addArticle()"></div>
					</div>
					<div class="d-flex justify-content-between align-items-center" v-if="adding_article">
						<input class="form-control col-md-3" type="text" v-model="article_name" placeholder="@lang ('i.name')">
						<input class="form-control col-md-3" type="text" v-model="article_code" placeholder="@lang ('i.code')">
						<div class="col-md-2">
							<select2 :options="stations" v-model="article_station"></select2>
						</div>
						<div class="col-md-3 d-flex align-items-center">
							<div class="cancel-icon" v-on:click="resetAddArticle()"></div> 
							<div class="save-icon ml-2" v-if="valid_article" v-on:click="storeArticle(false)"></div>
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
					{{--<div class="d-flex justify-content-between mb-3">
						<div><select2 v-model="part_ability_id" :options="abilities">
							<option value="-1">@lang ('i.no ability')</option>
						</select2></div>
						<input type="number" class="form-control col-md-4" v-model="part_ability_value">
					</div>--}}
					<textarea class="form-control" v-model="part_description" placeholder="@lang ('i.part description')"></textarea>
					<hr>
					<ul class="list">
						<li v-for="part_ability in part_abilities" class="d-flex justify-content align-items-center">
							<div class="col-md-8"><select2 v-model="part_ability.id" :options="abilities"></select2></div>
							<input type="number" class="form-control col-md-2" v-model="part_ability.value">
							<div class="delete-icon" v-on:click="deletePartAbility(part_ability.id)"></div>
						</li>
					</ul>
					<div class="btn btn-primary my-2" v-on:click="addPartAbility()">@lang ('i.add ability')</div>
					<hr>
					<ul class="list">
						<li v-for="c in part_codes" class="d-flex justify-content-between align-items-center">
							<input type="text" class="form-control col-md-5" v-model="c.code" placeholder="@lang ('i.code')">
							<div class="delete-icon" v-on:click="deletePartCode(c.code)"></div>
						</li>
					</ul>
					<div class="btn btn-primary my-2" v-on:click="addPartCode()">@lang ('i.add code')</div>
					<hr>
					<div class="mt-3">
						<span class="btn btn-primary mr-2" v-on:click="storePart()" v-if="valid_part">@lang ('i.save part')</span>
						<span class="btn btn-danger mr-2" v-on:click="deletePart()" v-if="can_delete_part">@lang ('i.delete')</span>
						<span class="btn btn-secondary" v-on:click="resetPart()">@lang ('i.cancel')</span>
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
			stations: null,
			filter_station: -1,
			articles: null,
			listing_articles: false,
			filter_name: null,

			deleting_articles: false,
			adding_article: false,
			editing_article: false,
			article_id: null,
			article_name: null,
			article_code: null,
			article_station: null,

			abilities: null,
			parts: null,

			editing_part: false,
			part_id: null,
			part_name: null,
			part_description: null,
			//part_ability_id: null,
			//part_ability_value: null,
			part_abilities: [],
			part_codes: [],
		}
	},
	computed: {
		filtered_by_library() {
			if (this.filter_station < 0) return this.articles;
			else return this.articles.filter(a =>
				a.library_station_id == this.filter_station);
		},
		filtered_articles() {
			if (this.filter_name == null) return this.filtered_by_library;
			else return this.filtered_by_library.filter(a =>
				a.name.indexOfInsensitive(this.filter_name) > -1 ||
				(a.code != null && a.code.indexOfInsensitive(this.filter_name) > -1)
			);
		},
		valid_article() {
			return (this.article_name != null &&
				this.article_name.length > 2 &&
				this.article_code != null &&
				this.article_code.length > 2 &&
				this.article_station != null)
		},
		valid_part() {
			return (
				this.part_name != null &&
				this.part_name.length > 2 &&
				this.part_description != null// && (
					//(
						//(this.part_ability_id == null || this.part_ability_id < 0) &&
						//(this.part_ability_value == null || this.part_ability_value.length == 0)
					//) || (
						//(this.part_ability_id != null && this.part_ability_id > 0) &&
						//(this.part_ability_value != null && this.part_ability_value > 0)
					//)
				//)
			);
		},
		station_name() {
			if (this.article_station == null) return 'undefined';
			var res = this.stations.filter(s => s.id == this.article_station);
			if (res.length == 1) {
				return res[0].text;
			}
			return 'undefined';
		},
		can_delete_part() {
			return this.part_id != null;
		},
	},
	watch: {
	},
	methods: {
		back() {
			if (this.editing_article) {
				this.fetch_articles();
				return;
			}
		},
		fetch_stations() {
			axios.get("{{ route('get library stations') }}")
				.then(response => {
					if (response.data.success) {
						this.stations = response.data.stations
					}
				})
				.catch(errors => {});
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
		delete_articles(active) {
			if (this.editing_article) return;
			this.deleting_articles = active;
		},
		resetArticle() {
			this.deleting_articles = false;
			this.adding_article = false;
			this.editing_article = false;
			this.article_id = null;
			this.article_name = null;
			this.article_code = null;
			this.article_station = null;
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
			//this.part_ability_id = -1;
			//this.part_ability_value = null;
			this.part_abilities = [];
			this.part_codes = [];
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
				this.article_station = article.library_station_id;
				this.fetch_parts();
			}
		},
		addPartCode() {
			this.part_codes.push({ code: "" });
		},
		addPartAbility() {
			this.part_abilities.push({ id: -1, value: 0});
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
				this.part_abilities = part.abilities;
				this.part_codes = part.codes;
			}
		},
		storeArticle(fetch = true) {
			if (!this.valid_article) return;
			axios.post("{{ route('store article') }}", {
					id: this.article_id,
					name: this.article_name,
					code: this.article_code,
					station_id: this.article_station,
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
		get_article_name(id) {
			var result = this.articles.filter(a => a.id == id);
			return result.length == 1 ? result[0].name : 'undefined';
		},
		async deleteArticle(id) {
			if (!this.deleting_articles) return;
			const res = await swal({
				title: "@lang ('i.Are you sure?')",
				text: "@lang ('i.This will delete \'%P%\' permanently.')".replace('%P%', this.get_article_name(id)),
				type: 'error',
				showCancelButton: true,
				focusCancel: true,
			});
			this.deleting_articles = false;
			if (res.value == true) {
				axios.post("{{ route('delete article') }}", {
					id: id,
				}).then(response => {
					if (response.data.success) {
						this.fetch_articles();
					}
				}).catch(errors => {
				});
			}
		},
		storePart() {
			if (!this.valid_part) return;
			const url = "{{ route('store part', ['article_id' => '%R%']) }}";
			axios.post(url.replace('%R%', this.article_id), {
					id: this.part_id,
					name: this.part_name,
					description: this.part_description,
					//ability_id: this.part_ability_id < 0 ? null : this.part_ability_id,
					//min_value: this.part_ability_value,
					abilities: this.part_abilities,
					codes: this.part_codes,
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
		deletePartCode(code) {
			this.part_codes = this.part_codes.filter(c => c.code != code);
		},
		deletePartAbility(ability_id) {
			this.part_abilities = this.part_abilities.filter(a => a.id != ability_id);
		},
		async deletePart() {
			if (!this.can_delete_part) return;
			const res = await swal({
				title: "@lang ('i.Are you sure?')",
				text: "@lang ('i.This will delete \'%P%\' permanently.')".replace('%P%', this.part_name),
				type: 'warning',
				showCancelButton: true,
				focusCancel: true,
			});
			if (res.value == true) {
				const url = "{{ route('delete part', ['article_id' => '%R%']) }}";
				axios.post(url.replace('%R%', this.article_id), {
					id: this.part_id,
				}).then(response => {
					if (response.data.success) {
						this.fetch_parts();
					}
				}).catch(errors => {});
			}
		},
		fetch_data: function() {
			this.fetch_stations();
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
