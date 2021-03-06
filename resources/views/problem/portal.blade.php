@extends ('layouts.app')

@section ('css')
<style>
#vis {
height:600px;
border: 1px solid #bbb;
background: #000;
}
</style>
<link href="{{ asset('css/vis.css') }}" rel="stylsheet" type="text/css">
@endsection

@section ('content')
<div class="container" id="vue">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<h2 class="d-flex align-items-center">
				@lang ('i.problems')
				<div class="delete-icon ml-2" v-if="listing_problems && !deleting_problems && !editing_problem" v-on:click="delete_problems(true)" v-cloak></div>
				<div class="cancel-icon ml-2" v-if="listing_problems && deleting_problems" v-on:click="delete_problems(false)" v-cloak></div>
			</h2>
			<span class="btn btn-secondary my-3" v-on:click="back()" v-if="editing_problem" v-cloak>@lang ('i.back')</span>
		</div>
		<div class="col-md-6" v-if="listing_problems" v-cloak>
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.search')" v-model="filter_name"></div>
				<div class="card-body">
					<span class="btn btn-primary mb-3" v-if="!adding_problem" v-on:click="addProblem()">@lang ('i.add')</span>
					<div class="d-flex mb-3 justify-content-between" v-if="adding_problem">
						<input class="form-control col-md-10" type="text" placeholder="@lang ('i.problem name')" v-model="problem_name" v-if="adding_problem">
						<div class="col-md-2 d-flex align-items-center">
							<div class="cancel-icon" v-on:click="resetProblem()"></div>
							<div class="save-icon ml-2" v-if="valid_problem" v-on:click="storeProblem()"></div>
						</div>
					</div>
					<ul class="list">
						<li class="d-flex align-items-center" v-for="problem in filtered_problems">
							<div class="flex-grow-1 thumb" v-on:click="editProblem(problem.id)">@{{ problem.name }}</div>
							<div class="delete-icon" v-on:click="deleteProblem(problem.id)" v-if="deleting_problems"></div>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-12 mb-4" v-if="editing_problem" v-cloak>
			<div class="card">
				<div class="card-header">
					<div class="d-flex align-items-center my-1" v-if="!adding_problem">
						@{{ problem_name }}
						<div class="edit-icon ml-3" v-on:click="addProblem()"></div>
					</div>
					<div class="d-flex align-items-center" v-if="adding_problem">
						<input class="form-control col-md-6" type="text" placeholder="@lang ('i.problem name')" v-model="problem_name">
						<div class="col-md-3 d-flex align-items-center ml-5">
							<div class="cancel-icon" v-on:click="resetAddProblem()"></div>
							<div class="save-icon ml-2" v-on:click="storeProblem(false)"></div>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div id="vis"></div>
					<span class="btn btn-primary mt-3" v-on:click="addEdge(true)" v-if="!selecting_edge && !editing_step && !editing_edge">@lang ('i.add edge')</span>
					<span class="btn btn-secondary mt-3" v-on:click="addEdge(false)" v-if="selecting_edge">@lang ('i.cancel')</span>
					<span class="btn btn-success mt-3 ml-2" v-on:click="vis_make()" v-if="!selecting_edge && !editing_step && !editing_edge">@lang ('i.regen graph')</span>
				</div>
			</div>
		</div>
		<div class="col-md-6" v-if="editing_step" v-cloak>
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.step name')" v-model="step_name"></div>
				<div class="card-body">
					<textarea class="form-control" v-model="step_description" placeholder="@lang ('i.step description')"></textarea>
					<div class="mt-3">
						<span class="btn btn-primary mr-2" v-on:click="storeStep()" v-if="valid_step">@lang ('i.save step')</span>
						<span class="btn btn-danger mr-2" v-on:click="deleteStep()" v-if="can_delete_step">@lang ('i.delete')</span>
						<span class="btn btn-secondary" v-on:click="resetStep()">@lang ('i.cancel')</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6" v-if="editing_problem && !editing_step && !editing_edge && !selecting_edge" v-cloak>
			<span class="btn btn-primary" v-on:click="editStep()">@lang ('i.add step')</span>
		</div>
		<div class="col-md-9" v-if="editing_edge" v-cloak>
			<div class="card">
				<div class="card-header">
					<b>@{{ edge_source }}</b> --&gt; <b>@{{ edge_target }}</b> (@{{ edge_id }})
				</div>
				<div class="card-body">
					<div class="d-flex flex-wrap">
						<div class="col-md-12">
							<textarea class="form-control my-3" placeholder="@lang ('i.failure message')" v-model="edge_message"></textarea>
						</div>
						<div class="col-md-6">
							<ul class="list">
								<li v-for="c in edge_codes" class="d-flex justify-content-between align-items-center">
									<input type="text" class="form-control col-md-10" v-model="c.code">
									<div class="delete-icon" v-on:click="deleteEdgeCode(c.code)"></div>
								</li>
							</ul>
							<span class="btn btn-primary mt-2" v-on:click="addEdgeCode()">@lang ('i.add code')</span>
						</div>
						<div class="col-md-6">
							<ul class="list">
								<li v-for="edge_ability in edge_abilities" class="d-flex justify-content-between align-items-center">
									<div class="col-md-8"><select2 v-model="edge_ability.id" :options="abilities"/></div>
									<input type="number" class="form-control col-md-2" v-model="edge_ability.value">
									<div class="delete-icon" v-on:click="deleteEdgeAbility(edge_ability.id)"></div>
								</li>
							</ul>
							<span class="btn btn-primary mt-2" v-on:click="addEdgeAbility()">@lang ('i.add ability')</span>
						</div>
					</div>
					<hr/>
					<div class="mt-3">
						<span class="btn btn-primary mr-2" v-on:click="storeEdge()" v-if="valid_edge">@lang ('i.save edge')</span>
						<span class="btn btn-danger mr-2" v-on:click="deleteEdge()" v-if="can_delete_edge">@lang ('i.delete')</span>
						<span class="btn btn-secondary" v-on:click="resetEdge()">@lang ('i.cancel')</span>
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

			deleting_problems: false,
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
			edge_codes: [],
			edge_abilities: [],
			edge_message: null,

			abilities: null,
		}
	},
	vis: {
		instance: null,
		options: {
			edges: {
				arrows: "to",
				length: 300,
				smooth: {
					enabled: true,
					type: "dynamic",
					roundness: 0.7,
				},
				font: {
					color: '#FFFFFF',
					background: '#003200',
					strokeWidth: 0,
				},
			},
			nodes: {
				color: {
					border: '#00BC20',
					background: '#006400',
					highlight: {
						border: '#00CC28',
						background: '#007200',
					},
				},
				font: {
					color: '#FFFFFF',
				},
			},
			physics: {
				solver: "repulsion",
				repulsion: {
					nodeDistance: 150,
				},
				stabilization: {
					enabled: true,
				},
			},
		},
	},
	computed: {
		filtered_problems() {
			if (this.filter_name == null) return this.problems;
			else return this.problems.filter(a => a.name.indexOfInsensitive(this.filter_name) > -1);
		},
		valid_problem() {
			return (this.problem_name != null && this.problem_name.length > 2);
		},
		valid_step() {
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
		edge_source() {
			if (this.edge_source_id != null) {
				var result = this.steps.filter(s => s.id == this.edge_source_id);
				if (result.length == 1) return result[0].name;
			}
			return null;
		},
		edge_target() {
			if (this.edge_target_id != null) {
				var result = this.steps.filter(s => s.id == this.edge_target_id);
				if (result.length == 1) return result[0].name;
			}
			return null;
		},
		valid_edge() {
			if (this.edge_source == null) return false;
			if (this.edge_target == null) return false;
			if (this.edge_codes.some(c => c.code.length < 3 || c.code.length > 8)) return false;
			return true;
		},
		tree() {
			var _this = this;
			var nodes = this.steps.map(s => {
				if (s.first_step == 1) return { id: s.id, label: s.name, color: { background: '#002000', highlight: { background: '#002000' } } };
				return { id: s.id, label: s.name };
			});
			var edges = this.edges.map(e => {
				var label = 'undefined';
				if (e.codes.length > 0) {
					label = "'" + e.codes.map(c => c.code).join("'\n'") + "'";
				}
				if (e.abilities.length > 0) {
					if (label == 'undefined') { label = ''; }
					if (label != '') { label = label + "\n"; }
					label = label + e.abilities.map(a => {
						return _this.ability_name(a.id) + "(" + a.value + ")";
					}).join("\n");
				}
				return { id: e.id, from: e.from, to: e.to, label: label };
			});
			return { nodes: nodes, edges: edges };
		},
	},
	methods: {
		fetch_problems() {
			this.resetProblem();
			axios.get("{{ route('get problems') }}")
				.then(response => {
					this.problems = response.data;
					this.listing_problems = true;
				})
				.catch(errors => {});
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
		fetch_steps() {
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
		delete_problems(active) {
			if (this.editing_problem) return;
			this.deleting_problems = active;
		},
		resetProblem() {
			this.deleting_problems = false;
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
			this.edge_id = null;
			this.edge_source_id = null;
			this.edge_target_id = null;
			this.edge_codes = [];
			this.edge_abilities = [];
			this.edge_message = null;
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
		addEdgeCode() {
			this.edge_codes.push({ code: "" });
		},
		addEdgeAbility() {
			this.edge_abilities.push({ id: -1, value: 0 });
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
				this.edge_abilities = edge.abilities;
				this.edge_codes = edge.codes;
				this.edge_message = edge.message;
				this.editing_edge = true;
			}
		},
		storeProblem(fetch = true) {
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
		get_problem_name(id) {
			var result = this.problems.filter(p => p.id == id);
			return result.length == 1 ? result[0].name : 'undefined';
		},
		async deleteProblem(id) {
			if (! this.deleting_problems) return;
			const res = await swal({
				title: "@lang ('i.Are you sure?')",
				text: "@lang ('i.This will delete \'%P%\' permanently.')".replace('%P%', this.get_problem_name(id)),
				type: 'error',
				showCancelButton: true,
				focusCancel: true,
			});
			this.deleting_problems = false;
			if (res.value == true) {
				axios.post("{{ route('delete problem') }}", {
					id: id,
				}).then(response => {
					if (response.data.success) {
						this.fetch_problems();
					}
				}).catch(errors => {
				});
			}
		},
		storeStep() {
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
		async deleteStep() {
			if (!this.can_delete_step) return;

			const res = await swal({
				title: "@lang ('i.Are you sure?')",
				text: "@lang ('i.This will delete \'%P%\' permanently.')".replace('%P%', this.step_name),
				type: 'error',
				showCancelButton: true,
				focusCancel: true,
			});

			if (res.value == true) {
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
			}
		},
		storeEdge() {
			if (!this.valid_edge) return;

			const url = "{{ route('store edge', ['problem_id' => '%R%']) }}";
			axios.post(url.replace('%R%', this.problem_id), {
					id: this.edge_id,
					step_id: this.edge_source_id,
					next_step_id: this.edge_target_id,
					abilities: this.edge_abilities,
					codes: this.edge_codes,
					message: this.edge_message,
				})
				.then(response => {
					if (response.data.success) {
						this.fetch_steps();
					}
				})
				.catch(errors => {});
		},
		deleteEdgeCode(code) {
			this.edge_codes = this.edge_codes.filter(c => c.code != code);
		},
		deleteEdgeAbility(ability_id) {
			this.edge_abilities = this.edge_abilities.filter(a => a.id != ability_id);
		},
		async deleteEdge() {
			if (!this.can_delete_edge) return;

			const res = await swal({
				title: "@lang ('i.Are you sure?')",
				text: "@lang ('i.This will delete \'%P%\' permanently.')".replace('%P%', this.get_problem_name(id)),
				type: 'error',
				showCancelButton: true,
				focusCancel: true,
			});

			if (res.value == true) {
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
			}
		},
		back() {
			if (this.editing_problem) {
				this.resetProblem();
				this.listing_problems = true;
				return;
			}
		},
		ability_name(ability_id) {
			var result = this.abilities.filter(a => a.id == ability_id);
			if (result.length == 1) {
				return result[0].text;
			}
			return 'N/A';
		},
		vis_destroy() {
			if (this.$options.vis.instance != null) {
				this.$options.vis.instance.destroy();
				this.$options.vis.instance = null;
			}
		},
		vis_make() {
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
			this.$options.vis.instance.on('stabilizationIterationsDone', function() {
				_this.$options.vis.instance.setOptions({ nodes: { physics: false } });
			});
		},
		fetch_data() {
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
