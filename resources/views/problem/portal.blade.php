@extends ('layouts.app')

@section ('css')
<style>
[v-cloak] { display:none; }
</style>
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
						<li class="thumb" v-for="problem in filtered_problems" v-on:click="edit(problem.id)" v-cloak>@{{ problem.name }}</li>
					</ul>
					<span class="btn btn-primary" v-on:click="edit()">@lang ('i.add')</li>
				</div>
			</div>
		</div>
		<div class="col-md-6" v-if="listing_problems" v-cloak></div>
		<div class="col-md-6" v-if="editing_problem" v-cloak>
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.problem name')" v-model="problem_name"></div>
				<div class="card-body">
					<ul class="list">
						<li class="thumb" v-for="step in steps" v-on:click="editStep(step.id)">@{{ step.name }}</li>
					</ul>
					<span class="btn btn-primary" v-on:click="addStep()">@lang ('i.add step')</span>
				</div>
			</div>
		</div>
		<div class="col-md-6" v-if="editing_problem" v-cloak>
			hi
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
			problems: null,
			listing_problems: false,
			filter_name: null,
			editing_problem: false,
			id: null,
			problem_name: null,
			steps: null,
		}
	},
	computed: {
		filtered_problems: function() {
			if (this.filter_name == null) return this.problems;
			else return this.problems.filter(a => a.name.indexOf(this.filter_name) > -1);
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
			if (this.id == null) return;
			const url = "{{ route('get steps', ['id' => '%R%']) }}";
			axios.get(url.replace('%R%', this.id))
				.then(response => {
					this.steps = response.data;
				})
				.catch(errors => {});
		},
		edit: function(id = -1) {
			this.listing_problems = false;
			this.editing_problem = true;
			this.id = null;
			this.problem_name = null;
			if (id == -1) return;

			var result = this.problems.filter(a => a.id == id);
			if (result.length == 1) {
				this.id = result[0].id;
				this.problem_name = result[0].name;
				this.fetch_steps();
			}
		},
		back: function() {
			if (this.editing_problem) {
				this.editing_problem = false;
				this.listing_problems = true;
				this.id = null;
				this.problem_name = null;
				return;
			}
		}
	},
	mounted() {
		this.$nextTick(this.fetch_problems);
	},
});
</script>
@endsection
