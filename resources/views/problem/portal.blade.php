@extends ('layouts.app')

@section ('css')
<style>
[v-cloak] { display:none; }
#vis {
height:400px;
border: 1px solid #bbb;
}
</style>
<link href="{{ asset('css/vis.css') }}" rel="stylsheet" type="text/css">
@endsection

@section ('content')
<div class="container" id="vue">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<h2>@lang ('i.problems')</h2>
			<span class="btn btn-outline-secondary my-3" v-on:click="back()" v-if="editing_problem" v-cloak>@lang ('i.back')</span>
		</div>
		<div class="col-md-6" v-if="listing_problems" v-cloak>
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.search')" v-model="filter_name"></div>
				<div class="card-body">
					<ul class="list">
						<li class="thumb" v-for="problem in filtered_problems" v-on:click="editProblem(problem.id)">@{{ problem.name }}</li>
					</ul>
					<span class="btn btn-primary" v-if="!adding_problem" v-on:click="addProblem()">@lang ('i.add')</span>
					<input class="form-control" type="text" v-model="problem_name" v-if="adding_problem">
					<span class="btn btn-primary mt-3" v-if="adding_problem && valid_problem" v-on:click="storeProblem()">@lang ('i.submit')</span>
				</div>
			</div>
		</div>
		<div class="col-md-12 mb-4" v-if="editing_problem" v-cloak>
			<div class="card">
				<div class="card-header d-flex">
					<div v-if="!adding_problem">
						@{{ problem_name }}
						<span class="btn btn-outline-primary ml-3" v-on:click="addProblem()">@lang ('i.edit name')</span>
					</div>
					<div class="col-md-8" v-if="adding_problem">
						<input class="form-control" type="text" placeholder="@lang ('i.problem name')" v-model="problem_name">
					</div>
					<div class="col-md-3" v-if="adding_problem">
						<span class="btn btn-primary" v-on:click="storeProblem(false)">@lang ('i.save name')</span>
						<span class="btn btn-outline-secondary" v-on:click="resetAddProblem()">@lang ('i.cancel')</span>
					</div>
				</div>
				<div class="card-body">
					<div id="vis"></div>
					<span class="btn btn-primary mt-3" v-on:click="addEdge(true)" v-if="!selecting_edge">@lang ('i.add edge')</span>
					<span class="btn btn-outline-info mt-3" v-on:click="addEdge(false)" v-if="selecting_edge">@lang ('i.cancel')</span>
				</div>
			</div>
		</div>
		<div class="col-md-6" v-if="editing_problem" v-cloak>
			<div class="card" v-if="editing_step" v-cloak>
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.step name')" v-model="step_name"></div>
				<div class="card-body">
					<textarea class="form-control" v-model="step_description" placeholder="@lang ('i.step description')"></textarea>
					<div class="mt-3">
						<span class="btn btn-primary mr-2" v-on:click="storeStep()" v-if="valid_step">@lang ('i.save step')</span>
						<span class="btn btn-outline-danger mr-2" v-on:click="deleteStep()" v-if="can_delete_step">@lang ('i.delete')</span>
						<span class="btn btn-outline-secondary" v-on:click="resetStep()">@lang ('i.cancel')</span>
					</div>
				</div>
			</div>
			<div v-if="!editing_step && !editing_edge" v-cloak>
				<span class="btn btn-primary" v-on:click="editStep()">@lang ('i.add step')</span>
			</div>
			<div class="card" v-if="editing_edge" v-cloak>
				<div class="card-header">@{{ edge_source }} --&gt; @{{ edge_target }}
					<select v-model="edge_type">
						<option>@lang ('i.select type')</option>
						<option value="ability">@lang ('i.ability type')</option>
						<option value="code">@lang ('i.code type')</option>
					</select>
				</div>
				<div class="card-body">
					<div v-if="edge_type == 'ability'">
						<select v-model="edge_ability_id">
							<option v-for="ability in abilities" v-bind:value="ability.id">@{{ ability.name }}</option>
						</select>
						<div>
							<input type="radio" id="ability.name" value="0" v-model="edge_ability_value">
							<input type="radio" id="ability.name" value="1" v-model="edge_ability_value">
							<input type="radio" id="ability.name" value="2" v-model="edge_ability_value">
							<input type="radio" id="ability.name" value="3" v-model="edge_ability_value">
						</div>
					</div>
					<div v-if="edge_type == 'code'">
						<input class="form-control" type="text" v-model="edge_code" placeholder="@lang ('i.code')">
					</div>
					<div class="mt-3">
						<span class="btn btn-primary mr-2" v-on:click="storeEdge()" v-if="valid_edge">@lang ('i.save edge')</span>
						<span class="btn btn-outline-danger mr-2" v-on:click="deleteEdge()" v-if="can_delete_edge">@lang ('i.delete')</span>
						<span class="btn btn-outline-secondary" v-on:click="resetEdge()">@lang ('i.cancel')</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section ('js')
<script src="{{ asset('js/vis.js') }}"></script>

<script>
new Vue({
	el: '#vue',
	data() {
		return {
			problems: null,
			listing_problems: false,
			filter_name: null,

			adding_problem: false,
			editing_problem: false,
			problem_id: null,
			problem_name: null,
			steps: null,
			edges: null,

			editing_step: false,
			step_id: null,
			step_name: null,
			step_description: null,

			selecting_edge: false,
			editing_edge: false,
			edge_id: null,
			edge_source_id: null,
			edge_target_id: null,
			edge_type: null,
			edge_ability_id: null,
			edge_ability_value: null,
			edge_code: null,

			abilities: null,
		}
	},
	vis: {
		instance: null,
		options: {
			edges: {
				arrows: "to",
				length: 200,
				smooth: {
					enabled: true,
					type: "dynamic",
					roundness: 0.7,
				},
			},
			physics: {
				solver: "repulsion",
			},
		},
	},
	computed: {
		filtered_problems: function() {
			if (this.filter_name == null) return this.problems;
			else return this.problems.filter(a => a.name.indexOf(this.filter_name) > -1);
		},
		valid_problem: function() {
			return (this.problem_name != null && this.problem_name.length > 2);
		},
		valid_step: function() {
			return (this.step_name != null &&
				this.step_name.length > 2 &&
				this.step_description != null &&
				this.step_description.length > 0);
		},
		can_delete_step() {
			if (this.step_id == null) return false;

			var first = this.steps.filter(s => s.first_step == 1 && s.id == this.step_id);
			if (first.length > 0) return false;

			var result = this.edges.filter(e => e.to == this.step_id || e.from == this.step_id);
			if (result.length > 0) return false;

			return true;
		},
		can_delete_edge() {
			if (this.edge_id == null) return false;

			return true;
		},
		edge_source: function() {
			if (this.edge_source_id != null) {
				var result = this.steps.filter(s => s.id == this.edge_source_id);
				if (result.length == 1) return result[0].name;
			}
			return null;
		},
		edge_target: function() {
			if (this.edge_target_id != null) {
				var result = this.steps.filter(s => s.id == this.edge_target_id);
				if (result.length == 1) return result[0].name;
			}
			return null;
		},
		valid_edge: function() {
			return (this.edge_source != null &&
				this.edge_target != null &&
				((this.edge_type == 'ability' && this.edge_ability_id != null && this.edge_ability_value != null) ||
				(this.edge_type == 'code' && this.edge_code != null && this.edge_code.length > 2))
			);
		},
		tree: function() {
			var nodes = this.steps.map(s => {
				if (s.first_step == 1) return { id: s.id, label: s.name, color: { background: 'lightgreen', highlight: { background: 'lightgreen' } } };
				return { id: s.id, label: s.name };
			});
			var edges = this.edges.map(e => {
				var label = 'undefined';
				if (e.type == 'ability') {
					var result = this.abilities.filter(a => a.id == e.ability_id);
					if (result.length == 1) {
						label = result[0].name + '(' + e.min_value + ')';
					}
				} else if (e.type == 'code') {
					label = e.code;
				}

				return { id: e.id, from: e.from, to: e.to, label: label };
			});
			return { nodes: nodes, edges: edges };
		},
	},
	methods: {
		fetch_problems: function() {
			this.resetProblem();
			axios.get("{{ route('get problems') }}")
				.then(response => {
					this.problems = response.data;
					this.listing_problems = true;
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
		fetch_steps: function() {
			if (this.problem_id == null) return;
			this.resetStep();
			this.resetEdge();
			const url = "{{ route('get steps', ['problem_id' => '%R%']) }}";
			axios.get(url.replace('%R%', this.problem_id))
				.then(response => {
					this.steps = response.data.steps;
					this.edges = response.data.edges;
					this.vis_make();
				})
				.catch(errors => {});
		},
		resetProblem: function() {
			this.adding_problem = false;
			this.editing_problem = false;
			this.vis_destroy();
			this.resetStep();
			this.resetEdge();
			this.problem_id = null;
			this.problem_name = null;
			this.steps = null;
			this.edges = null;

		},
		resetAddProblem() {
			this.adding_problem = false;
		},
		resetStep() {
			this.editing_step = false;
			this.step_id = null;
			this.step_name = null;
			this.step_description = null;
		},
		resetEdge() {
			this.editing_edge = false;
			this.selecting_edge = false;
			this.edge_source_id = null;
			this.edge_target_id = null;
			this.edge_ability_id = null;
			this.edge_ability_value = 0;
		},
		addProblem() {
			this.adding_problem = true;
		},
		editProblem(id = -1) {
			this.resetProblem();
			if (id == -1) return;
			this.listing_problems = false;
			this.editing_problem = true;

			var result = this.problems.filter(a => a.id == id);
			if (result.length == 1) {
				this.problem_id = result[0].id;
				this.problem_name = result[0].name;
				this.fetch_steps();
			}
		},
		editStep(id = -1) {
			this.resetStep();
			this.resetEdge();
			this.editing_step = true;
            if (id == -1) return;

			var result = this.steps.filter(s => s.id == id);
			if (result.length == 1) {
				var step = result[0];
				this.step_id = step.id;
				this.step_name = step.name;
				this.step_description = step.description;
			}
		},
		addEdge(editing) {
			if (editing) {
				this.selecting_edge = true;
			} else {
				this.resetEdge();
			}
		},
		editEdge(edge_id) {
			this.resetStep();
			this.resetEdge();
			var result = this.edges.filter(e => e.id == edge_id);
			if (result.length == 1) {
				var edge = result[0];
				this.edge_id = edge.id;
				this.edge_source_id = edge.from;
				this.edge_target_id = edge.to;
				this.edge_type = edge.type;
				this.edge_ability_id = edge.ability_id;
				this.edge_ability_value = edge.min_value;
				this.edge_code = edge.code;
				this.editing_edge = true;
			}
		},
		storeProblem: function(fetch = true) {
			if (!this.valid_problem) return;
			axios.post("{{ route('store problem') }}", {
					id: this.problem_id,
					name: this.problem_name,
				})
				.then(response => {
					if (response.data.success) {
						if (fetch) {
							this.fetch_problems();
						} else {
							this.adding_problem = false;
						}
					} else {
						alert(response.data.message);
					}
				})
				.catch(errors => {});
		},
		storeStep: function() {
			if (!this.valid_step) return;

			const url = "{{ route('store node', ['problem_id' => '%R%']) }}";
			axios.post(url.replace('%R%', this.problem_id), {
					step_id: this.step_id,
					name: this.step_name,
					description: this.step_description,
				})
				.then(response => {
					if (response.data.success) {
						this.fetch_steps();
					}
				})
				.catch(errors => {});
		},
		deleteStep() {
			if (!this.can_delete_step) return;

			const url = "{{ route('delete node', ['problem_id' => '%R%']) }}";
			axios.post(url.replace('%R%', this.problem_id), {
					step_id: this.step_id,
				})
				.then(response => {
					if (response.data.success) {
						this.fetch_steps();
					}
				})
				.catch(errors => {});
		},
		storeEdge: function() {
			if (!this.valid_edge) return;

			const url = "{{ route('store edge', ['problem_id' => '%R%']) }}";
			axios.post(url.replace('%R%', this.problem_id), {
					id: this.edge_id,
					step_id: this.edge_source_id,
					next_step_id: this.edge_target_id,
					type: this.edge_type,
					ability_id: this.edge_ability_id,
					min_value: this.edge_ability_value,
					code: this.edge_code,
				})
				.then(response => {
					if (response.data.success) {
						this.fetch_steps();
					}
				})
				.catch(errors => {});
		},
		deleteEdge() {
			if (!this.can_delete_edge) return;

			const url = "{{ route('delete edge', ['problem_id' => '%R%']) }}";
			axios.post(url.replace('%R%', this.problem_id), {
					edge_id: this.edge_id,
				})
				.then(response => {
					if (response.data.success) {
						this.fetch_steps();
					}
				})
				.catch(errors => {});
		},
		back: function() {
			if (this.editing_problem) {
				this.resetProblem();
				this.listing_problems = true;
				return;
			}
		},
		vis_destroy: function() {
			if (this.$options.vis.instance != null) {
				this.$options.vis.instance.destroy();
				this.$options.vis.instance = null;
			}
		},
		vis_make: function() {
			var _this = this;
			this.$options.vis.instance = new vis.Network(document.getElementById('vis'), this.tree, this.$options.vis.options);
			this.$options.vis.instance.on('select', function(params) {
				if (_this.selecting_edge) {
					if (params.nodes.length > 0) {
						if (_this.edge_source_id == null) {
							_this.edge_source_id = params.nodes[0];
						} else if (_this.edge_target_id == null) {
							_this.edge_target_id = params.nodes[0];
							_this.editing_edge = true;
						}
					}
					return;
				}
				if (params.nodes.length > 0) {
					_this.editStep(params.nodes[0]);
				} else if (params.edges.length > 0) {
					_this.editEdge(params.edges[0]);
				}
			});
		},
		fetch_data: function() {
			this.fetch_problems();
			this.fetch_abilities();
		},
	},
	mounted() {
		this.$nextTick(this.fetch_data);
	},
});
</script>
@endsection
