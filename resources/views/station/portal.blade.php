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
				<div class="save-icon ml-2" v-if="editing_names" v-on:click="save_names()" v-cloak></div>
				<div class="cancel-icon ml-2" v-if="editing_names" v-on:click="edit_names(false)" v-cloak></div>
				<div class="loading-icon ml-2" v-if="loading" v-cloak></div>
			</h2>
		</div>
		<div class="col-md-12 d-flex flex-wrap" v-if="listing_stations" v-cloak>
			<div class="col-md-4 my-3" v-for="station in stations">
				<div class="card" v-cloak>
					<div class="card-header">
						<h4 class="my-1" v-if="!editing_names">@{{ station.name }}</h4>
						<input type="text" class="form-control" v-if="editing_names" v-model="station.name">
					</div>
					<div class="card-body">
						<div v-bind:class="activity_warning(station.last_ping)">@{{ last_activity_text(station.last_ping) }}</div>
						<div v-if="station.station_type.indexOf('Library') > 0">
							<p>@lang ('i.Nothing to do in the library')</p>
						</div>
						<div v-if="station.station_type.indexOf('Problem') > 0">
							<p>TODO :</p>
							<ul>
								<li>actions to perform from here
									<ul>
										<li>launch a new active problem</li>
										<li>advance problem one step, manually</li>
										<li>reverse problem one step, manually</li>
										<li>direct link to editor?</li>
									</ul>
								</li>
								<li>whatever...
									<ul>
										<li>active problem</li>
										<li>log of players that went here (omigod)</li>
										<li>webcam link..!</li>
									</ul>
								</li>
							</ul>
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
		}
	},
	computed: {
	},
	methods: {
		fetch_stations() {
			if (this.editing_names) return;
			this.loading = true;
			axios.get("{{ route('get stations') }}")
				.then(response => {
					this.stations = response.data;
					this.listing_stations = true;
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
				return "alert alert-danger";
			}
			if ((new Date) - (new Date(timestamp)) > 30000) {
				return "alert alert-warning";
			}
			return "alert alert-success";
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
		fetch_data() {
			this.fetch_stations();

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
