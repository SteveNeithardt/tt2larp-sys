<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

	@yield ('css')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
					@auth
						<ul class="navbar-nav mr-auto">
							<li class="nav-item"><a class="nav-link" href="{{ route('station portal') }}">@lang ('i.stations')</a></li>
							<li class="nav-item"><a class="nav-link" href="{{ route('crafting portal') }}">@lang ('i.crafting')</a></li>
							<li class="nav-item"><a class="nav-link" href="{{ route('library portal') }}">@lang ('i.library')</a></li>
							<li class="nav-item"><a class="nav-link" href="{{ route('problem portal') }}">@lang ('i.problems')</a></li>
							<li class="nav-item"><a class="nav-link" href="{{ route('character portal') }}">@lang ('i.characters')</a></li>
							<li class="nav-item"><a class="nav-link" href="{{ route('ability portal') }}">@lang ('i.abilities')</a></li>
						</ul>
					@endauth

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield ('content')
        </main>

		@auth
		<div id="chat" class="fixed-bottom" v-cloak>
			<span class="chat-icon" v-on:click="toggle(true)">
				<span class="chat-icon-text">
					@lang ('i.chat')
				</span>
				<span class="chat-icon-number d-flex align-items-center justify-content-center" v-if="number != 0">
					@{{ number }}
				</span>
			</span>
			<div class="chat-frame" v-if="visible">
				<iframe src="{{ route('chat index') }}" class="h-100 w-100 border-0"></iframe>
				<span class="chat-close-icon" v-on:click="toggle(false)"></span>
			</div>
		</div>
		@endauth
    </div>

<script src="{{ asset('js/app.js') }}"></script>
@yield ('js')
@auth
<script>
new Vue({
	el: '#chat',
	data() {
		return {
			visible: false,
			number: 0,
			id: 1,
		}
	},
	methods: {
		//todo: something about just getting the number 'since last time' kinda...?
		fetch_messages() {
			axios.get("{{ route('get chat list') }}", { params: {
				chat_id: this.id,
			}}).then(response => {
				if (response.data.success) {
					this.messages = response.data.messages;
					//this.number = response.data.messages.count;
				}
			}).catch(errors => {});
		},
		toggle(visible) {
			this.visible = visible;
		},
		fetch_data() {
			this.fetch_messages();

			setInterval(function() {
				this.fetch_messages();
			}.bind(this), 2000);
		}
	},
	mounted() {
		this.$nextTick(this.fetch_data());
	},
});
</script>
@endauth
</body>
</html>
