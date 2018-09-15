@extends ('layouts.min')

@section ('css')
<style>
html,body { background: #000400; height:100%; }
* { font-family: monospace; }
.overflow-y-scroll { overflow: scroll; }
textarea { height: 40px; };
.chat-fullframe { position: absolute; top: 0; left: 0; right: 0; bottom: 0; }
.chat-input { position: absolute; left: 0; right: 0; bottom: 0; height: 80px; }
.chat-messages { position: absolute; top: 0; left: 0; right: 0; bottom: 84px; }
</style>
@endsection

@section ('content')
<div class="h-100" id="vue">
	<div class="chat-fullframe">
		<div class="chat-input d-flex mt-2">
			<textarea type="text" class="form-control" v-model="new_msg"></textarea>
			<div class="h-100 thumb d-flex align-items-center p-4" v-on:click="newMessage()">&gt;&gt;</div>
		</div>
		<div class="chat-messages d-flex flex-column-reverse overflow-y-scroll">
			<div v-for="message in messages" class="mx-3">
				<span :class="deleted_class(message.deleted)">@{{ message.message }}</span>
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
			id: {{ $chat_id }},
			new_msg: "",
			messages: null,
			unreadCount: 0,
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
					this.messages.unshift({ message: response.data.message });
				}
			}).catch(errors => {
			});
		},
		deleted_class(deleted) {
			return deleted ? 'deleted' : '';
		},
		fetch_data() {
			this.fetch_messages();

			setInterval(function() {
				this.fetch_messages();
			}.bind(this), 4000);
		},
	},
	mounted() {
		this.$nextTick(this.fetch_data);
	},
});
</script>
@endsection
