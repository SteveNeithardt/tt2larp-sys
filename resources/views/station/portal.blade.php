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
				@lang ('i.stations')
				<div class="edit-icon ml-2" v-if="!editing_names" v-on:click="edit_names(true)" v-cloak></div>
				<div class="cancel-icon ml-2" v-if="editing_names" v-on:click="edit_names(false)" v-cloak></div>
				<div class="save-icon ml-2" v-if="editing_names" v-on:click="save_names()" v-cloak></div>
				<div class="loading-icon ml-2" v-if="loading" v-cloak></div>
			</h2>
		</div>
		<div class="col-md-12 d-flex flex-wrap" v-if="listing_stations" v-cloak>
			<div class="col-md-4 my-3" v-for="station in stations">
				<div class="card" v-cloak>
					<div class="card-header" :class="activity_warning(station.last_ping)">
						<h4 class="my-1" v-if="!editing_names">@{{ station.name }}</h4>
						<input type="text" class="form-control" v-if="editing_names" v-model="station.name">
						<span>@{{ last_activity_text(station.last_ping) }}</span>
					</div>
					<div class="card-body">
						<div v-if="is_library(station)">
							<div class="alert alert-dark">@lang ('i.Nothing to do in the library')</div>
						</div>
						<div v-if="is_problem(station)">
							<div v-if="ps_has_problem(station)">
								<div class="alert alert-info">
									<h5>@{{ "@lang ("i.active problem is '%P%'")".replace('%P%', station.station.problem.name) }}</h5>
									<h5 class="d-flex align-items-center" v-if="ps_has_step(station)">
										@{{ "@lang ("i.currently on step '%S%'")".replace('%S%', ps_step_name(station)) }}
										<div class="left-chevron-icon ml-2" v-if="ps_assigning_step < 0 && ps_has_previous_steps(station)" v-on:click="ps_assign_step(station.id, -1)"></div>
										<div class="right-chevron-icon ml-2" v-if="ps_assigning_step < 0 && ps_has_next_steps(station)" v-on:click="ps_assign_step(station.id, 1)" v-cloak></div>

									</h5>
									<div class="d-flex align-items-center" v-if="ps_assigning_step == station.id">
										<select2 v-if="steps != null" v-model="step_id" :options="steps"></select2>
										<div class="cancel-icon ml-2" v-on:click="ps_assign_step(-1)"></div>
										<div class="save-icon ml-2" v-if="step_id >= 0" v-on:click="ps_save_step(station.id)"></div>
									</div>
								</div>
								<div class="alert alert-success mt-2" v-if="ps_is_finished(station)">@lang ('i.problem is finished')</div>
							</div>
							<div v-if="!ps_has_problem(station)">
								<div class="alert alert-dark">@lang ('i.no active problem')</div>
							</div>
							<div>
								<div class="d-flex align-items-center" v-if="ps_assigning_problem == station.id">
									<select2 v-model="problem_id" :options="problems">
										<option value="-1">@lang ('i.no problem')</option>
									</select2>
									<div class="cancel-icon ml-2" v-on:click="ps_assign_problem(-1)"></div>
									<div class="save-icon ml-2" v-on:click="ps_save_problem(station.id)"></div>
								</div>
								<div v-else>
									<div class="btn btn-outline-primary" v-on:click="ps_assign_problem(station.id)" v-if="ps_assigning_problem < 0 && !ps_has_problem(station)">@lang ('i.assign new problem')</div>
									<div class="btn btn-outline-danger" v-on:click="ps_cancel_problem(station.id)" v-if="ps_assigning_problem < 0 && ps_has_problem(station)">@lang ('i.cancel problem')</div>
								</div>
							</div>
						</div>
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
			loading: true,
			editing_names: false,
			listing_stations: false,
			stations: null,
			problems: null,
			problem_id: -1,
			ps_assigning_problem: -1,
			steps: null,
			step_id: -1,
			ps_assigning_step: -1,
			ps_step_forward: null,
		}
	},
	computed: {
	},
	watch: {
		ps_assigning_step(after) {
			if (after < 0) {
				this.steps = null;
			} else {
				this.fetch_steps();
			}
		}
	},
	methods: {
		fetch_stations() {
			if (this.ps_assigning_problem >= 0 ||
				this.ps_assigning_step >= 0 ||
				this.editing_names) {
				return;
			}
			this.loading = true;
			axios.get("{{ route('get stations') }}")
				.then(response => {
					this.stations = response.data;
					this.listing_stations = true;
					this.loading = false;
					this.problem_id = -1;
					this.ps_assigning_problem = -1;
				}).catch(errors => {
					this.loading = false;
				});
		},
		fetch_problems() {
			this.loading = true;
			axios.get("{{ route('get problems') }}")
				.then(response => {
					this.problems = response.data.map(p => {
						return { id: p.id, text: p.name };
					});
					this.loading = false;
				}).catch(errors => {
					this.loading = false;
				});
		},
		fetch_steps() {
			if (this.ps_assigning_step < 0) return;
			this.loaading = true;
			axios.get("{{ route('get station active step entourage') }}", { params: {
					station_id: this.ps_assigning_step,
					forward: this.ps_step_forward,
				} }).then(response => {
					this.steps = response.data;
					this.loading = false;
				}).catch(errors => {
					this.loading = false;
				});
		},
		last_activity_text(timestamp) {
			if (timestamp == null) {
				return "@lang ('i.offline')";
			}
			return "@lang ('i.last activity at %A%')".replace('%A%', timestamp);
		},
		activity_warning(timestamp) {
			if (timestamp == null) {
				//complexify this for offline
				return "alert-danger";
			}
			if ((new Date) - (new Date(timestamp)) > 30000) {
				return "alert-warning";
			}
			return "alert-success";
		},
		edit_names(edit) {
			if (this.loading) return;
			this.editing_names = edit;
			if (edit == false) {
				this.fetch_stations();
			}
		},
		save_names() {
			if (this.loading) return;
			this.loading = true;
			var stations = this.stations.map(s => {
				return { id: s.id, name: s.name };
			});
			axios.post("{{ route('set station names') }}", {
				stations: stations,
			}).then(response => {
				this.editing_names = false;
				if (response.data.success) {
					this.fetch_stations();
				}
				this.loading = false;
			}).catch(errors => {
				this.loading = false;
			});
		},
		is_library(station) { return station.station_type.indexOf('Library') > -1; },
		is_problem(station) { return station.station_type.indexOf('Problem') > -1; },
		ps_has_problem(station) { return station.station.problem != null; },
		ps_has_step(station) {
			return station.station.step != null ||
				this.ps_has_first_step(station);
		},
		ps_has_first_step(station) {
			return station.station.step == null &&
				station.station.problem.first_steps != null &&
				station.station.problem.first_steps[0] != null;
		},
		ps_step(station) {
			if (station.station.step != null) return station.station.step;
			if (this.ps_has_first_step(station)) return station.station.problem.first_steps[0];
			return null;
		},
		ps_has_next_steps(station) {
			var step = this.ps_step(station);
			return step != null &&
				step.nextEdgeCount > 0;
		},
		ps_has_previous_steps(station) {
			var step = this.ps_step(station);
			return step != null &&
				step.previousEdgeCount > 0;
		},
		ps_step_name(station) {
			var step = this.ps_step(station);
			if (step == null) return 'undefined';
			return step.name;
		},
		ps_is_finished(station) {
			var step = this.ps_step(station);
			return step == null ||
				step.nextEdgeCount == 0;
		},
		ps_assign_problem(station_id) {
			this.ps_assigning_problem = station_id;
		},
		ps_save_problem(station_id) {
			if (this.ps_assigning_problem < 0) return;
			if (this.problem_id != null && this.problem_id >= 0) {
				this.ps_commit_problem(station_id, this.problem_id);
			}
		},
		ps_cancel_problem(station_id) {
			var res = confirm("Are you sure?");
			if (res == true) {
				this.ps_commit_problem(station_id, -1);
			}
		},
		ps_commit_problem(station_id, problem_id) {
			this.loading = true;
			axios.post("{{ route('set station active problem') }}", {
				station_id: station_id,
				problem_id: problem_id,
			}).then(response => {
				if (response.data.success) {
					this.ps_assigning_problem = -1;
					this.fetch_stations();
				}
				this.loading = false;
			}).catch(errors => {
				this.loading = false;
			});
		},
		ps_assign_step(station_id, forward = null) {
			this.ps_assigning_step = station_id;
			this.ps_step_forward = forward;
		},
		ps_save_step(station_id) {
			var res = confirm("Are you sure?");
			if (res == true) {
				this.loading = true;
				axios.post("{{ route('set station active step') }}", {
					station_id: station_id,
					step_id: this.step_id,
				}).then(response => {
					if (response.data.success) {
						this.ps_assigning_step = -1;
						this.ps_step_forward = null;
						this.steps = null;
						this.step_id = -1;
						this.fetch_stations();
					}
				}).catch(errors => {
					this.loading = false;
				});
			}
		},
		fetch_data() {
			this.fetch_stations();
			this.fetch_problems();

			setInterval(function () {
				this.fetch_stations();
			}.bind(this), 5000);
		},
	},
	mounted() {
		this.$nextTick(this.fetch_data);
	},
});
</script>
@endsection
