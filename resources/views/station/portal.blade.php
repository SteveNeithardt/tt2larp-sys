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
			<h2>@lang ('i.stations')</h2>
		</div>
		<div class="col-md-8" v-if="listing_stations" v-cloak>
			<div class="card">
				<div class="card-header"><input class="form-control" type="text" placeholder="@lang ('i.search')" v-model="filter_name"></div>
				<div class="card-body">
					<ul class="list">
						<li class="thumb" v-for="station in filtered_stations" v-on:click="editStation(station.id)">@{{ station.name }}</li>
					</ul>
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
			listing_stations: false,
			stations: null,
			filter_name: null,
		}
	},
	computed: {
		filtered_stations() {
			if (this.filter_name == null) return this.stations;
			else return this.stations.filter(s => s.name.indexOf(this.filter_name) > -1);
		},
	},
	methods: {
		fetch_stations() {
			this.resetStation();
			axios.get("{{ route('get stations') }}")
				.then(response => {
					this.stations = response.data;
					this.listing_stations = true;
				})
				.catch(errors => {});
		},
		resetStation() {
		},
	},
	mounted() {
		this.$nextTick(this.fetch_stations);
	},
});
</script>
@endsection
