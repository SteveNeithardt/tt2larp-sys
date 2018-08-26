@extends ('layouts.app')

@section ('css')
<style>
[v-cloak] { display:none; }
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
						<li class="thumb" v-for="problem in filtered_problems" v-on:click="editProblem(problem.id)" v-cloak>@{{ problem.name }}</li>
					</ul>
					<span class="btn btn-primary" v-on:click="editProblem()">@lang ('i.add')</li>
				</div>
			</div>
		</div>
		<div class="col-md-6" v-if="editing_problem" v-cloak>
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.problem name')" v-model="problem_name"></div>
				<div class="card-body">
					<div id="vis" style="height:300px;"></div>
					</ul>
					<span class="btn btn-primary" v-on:click="addStep()">@lang ('i.add step')</span>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card" v-if="editing_step" v-cloak>
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.step name')" v-model="step_name"></div>
				<div class="card-body">
					<textarea class="form-control" v-model="step_description" placeholder="@lang ('i.step description')"></textarea>
				</div>
			</div>
			<div class="card" v-if="editing_edge" v-cloak>
				<div class="card-header">@{{ edge_source }} -- @{{ edge_target }}</div>
				<div class="card-body">
					Placeholder for @{{ edge_ability_id }} : @{{ edge_ability_value }}
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

			editing_problem: false,
			problem_id: null,
			problem_name: null,
			steps: null,
			tree: null,

			editing_step: false,
			step_id: null,
			step_name: null,
			step_description: null,

			editing_edge: false,
			edge_source_id: null,
			edge_target_id: null,
			edge_ability_id: null,
			edge_ability_value: 0,
		}
	},
	vis: {
		instance: null,
		options: {
			layout: {
				hierarchical: {
					direction: 'UD',
				},
			},
		},
	},
	computed: {
		filtered_problems: function() {
			if (this.filter_name == null) return this.problems;
			else return this.problems.filter(a => a.name.indexOf(this.filter_name) > -1);
		},
		edge_source: function() {
			if (this.edge_source_id != null) {
				var result = this.steps.filter(s => s.id == this.edge_source_id);
				if (result.length == 1) return result[0].name;
			}
		},
		edge_target: function() {
			if (this.edge_target_id != null) {
				var result = this.steps.filter(s => s.id == this.edge_target_id);
				if (result.length == 1) return result[0].name;
			}
		},
	},
	methods: {
		fetch_problems: function() {
			axios.get("{{ route('get problems') }}")
				.then(response => {
					this.problems = response.data;
					this.listing_problems = true;
				})
				.catch(errors => {});
		},
		fetch_steps: function() {
			if (this.problem_id == null) return;
			const url = "{{ route('get steps', ['id' => '%R%']) }}";
			axios.get(url.replace('%R%', this.problem_id))
				.then(response => {
					this.steps = response.data.steps;
					this.tree = response.data.tree;
					this.vis_make();
				})
				.catch(errors => {});
		},
		resetProblem: function() {
			this.editing_problem = false;
			this.problem_id = null;
			this.problem_name = null;
			this.steps = null;
			this.tree = null;

			this.vis_destroy();
			this.resetStep();
			this.resetEdge();
		},
		resetStep: function() {
			this.editing_step = false;
			this.step_id = null;
			this.step_name = null;
			this.step_description = null;
		},
		resetEdge: function() {
			this.editing_edge = false;
			this.edge_source_id = null;
			this.edge_target_id = null;
			this.edge_ability_id = null;
			this.edge_ability_value = 0;
		},
		editProblem: function(id = -1) {
			this.resetProblem();
			this.listing_problems = false;
			this.editing_problem = true;
			if (id == -1) return;

			var result = this.problems.filter(a => a.id == id);
			if (result.length == 1) {
				this.problem_id = result[0].id;
				this.problem_name = result[0].name;
				this.fetch_steps();
			}
		},
		editStep: function(id = -1) {
			this.resetStep();
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
		editEdge: function(edge_id) {
			this.resetEdge();
			var ids = edge_id.split(':');
			var id_A = ids[0];
			var id_B = ids[1];
			var result_A = this.steps.filter(s => s.id == id_A);
			var result_B = this.steps.filter(s => s.id == id_B);
			if (result_A.length == 1 && result_B.length == 1) {
				this.editing_edge = true;
				this.edge_source_id = result_A[0].id;
				this.edge_target_id = result_B[0].id;
				var result = this.tree.edges.filter(e => e.id == edge_id);
				if (result.length == 1) {
					var edge = result[0];
					this.edge_ability_id = edge.ability_id;
					this.edge_ability_value = edge.min_value;
				}
			}
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
				if (params.nodes.length > 0) {
					_this.editStep(params.nodes[0]);
				} else if (params.edges.length > 0) {
					_this.editEdge(params.edges[0]);
				}
			});
		},
	},
	mounted() {
		this.$nextTick(this.fetch_problems);
	},
});
</script>
@endsection
