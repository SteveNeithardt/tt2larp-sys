@extends ('layouts.min')

@section ('css')
<style>
html,body { height:100%; }
</style>
@endsection

@section ('content')
<div class="h-100 row no-gutters">
	<div class="col-md-8 h-100 border-right" id="vue">
		<div class="d-flex justify-content-center align-items-center h-100">
			<div class="d-flex flex-wrap no-gutters p-3" v-if="listing_stations" v-cloak>
				<div v-for="station in stations" class="col-md-4">
					<div class="card m-3">
						<div class="card-header" :class="alert_status(station)">@{{ station.name }}</div>
						<div class="card-body" :class="alert_status(station)">
							@{{ station.alert.message }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<iframe src="{{ route('chat index') }}" class="col-md-4 h-100 border-0"></iframe>
</div>
@endsection

@section ('js')
<script>
new Vue({
	el: '#vue',
	data() {
		return {
			listing_stations: false,
			stations: null,
		}
	},
	methods: {
		fetch_stations() {
			axios.get("{{ route('command get stations') }}")
				.then(response => {
					if (response.data.success) {
						this.stations = response.data.stations;
						this.listing_stations = true;
					}
				}).catch(errors => {
				});
		},
		alert_status(station) {
			if (station.alert.active) {
				return "alert-danger";
			}
			return "";
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
