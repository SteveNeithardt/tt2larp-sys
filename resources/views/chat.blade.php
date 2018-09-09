@extends ('layouts.min')

@section ('css')
<style>
[v-cloak] { display:none; }
html,body { height:100%; }
* { font-family: monospace; }
</style>
@endsection

@section ('content')
<div class="h-100" id="vue">
	<div class="h-100 d-flex flex-column-reverse">
		<div class="d-flex">
			<textarea type="text" class="form-control" v-model="new_msg"></textarea>
			<div class="btn btn-primary" v-on:click="newMessage()">SEND</div>
		</div>
		<div v-for="message in messages" class="alert alert-success">
			@{{ message.message }}
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
			id: {{ $chat_id }},
			new_msg: "",
			messages: null,
		}
	},
	methods: {
		fetch_messages() {
			axios.get("{{ route('get chat list') }}", { params: {
				chat_id: this.id,
			}}).then(response => {
				if (response.data.success) {
					this.messages = response.data.messages;
				}
			}).catch(errors => {
			});
		},
		newMessage() {
			if (this.new_msg == null || this.new_msg.length < 1) return;
			var message = this.new_msg;
			this.new_msg = "";
			axios.post("{{ route('new chat message') }}", {
				chat_id: this.id,
				message: message,
			}).then(response => {
				if (response.data.success) {
					this.messages.push({ message: message });
				}
			}).catch(errors => {
			});
		},
		fetch_data() {
			this.fetch_messages();

			setInterval(function() {
				this.fetch_messages();
			}.bind(this), 2000);
		},
	},
	mounted() {
		this.$nextTick(this.fetch_data);
	},
});
</script>
@endsection
