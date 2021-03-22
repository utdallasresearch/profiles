<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title') - {{ $settings['site_title'] ?? 'Profiles' }}</title>
	@if(isset($settings['favicon']))
		<!--[if lt IE 11]>
			<link rel="shortcut icon" href="{{ $settings['logo'] ?? '' }}">
		<![endif]-->
		<link rel="icon" href="{{ $settings['favicon'] ?? '' }}">
	@else
		<link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2016%2016'%3E%3Ctext%20x='0'%20y='14'%3E👩🏻‍🔬%3C/text%3E%3C/svg%3E" type="image/svg+xml" />
	@endif
	<link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
	<!-- <link rel="stylesheet" href="/css/style.css"> -->

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	@include('analytics')
	@yield('head')
</head>
<body>
	@yield('header')
	<noscript>
		<div class="alert alert-danger">
			<div class="container">
				<strong>Warning: This site <em>requires</em> a JavaScript-enabled browser in order to function properly. If you have disabled JavaScript, please enable it for this site.</strong>
			</div>
		</div>
	</noscript>
	@if (config('app.banner_message'))
		<div class="alert alert-info mb-0" role="alert">
			<div class="container">
				{!! config('app.banner_message') !!}
			</div>
		</div>
	@endif
	@if (Session::has('flash_message'))
		@include('alert', ['message' => session('flash_message'), 'type' => 'success'])
	@endif
	@yield('content')
	@yield('footer')
	<div id="footer-container" class="full-width" style="background-image:url('{{asset('/img/60-lines.png')}}');">
		<footer class="container">
			@if(isset($settings['footer']))
				{!! $settings['footer'] !!}
			@else
				Questions?<br><a href="/faq">Check our FAQ</a> or <a href="mailto:email@example.com?subject=Profiles">contact us.</a><br><br>
				<a href="https://example.com">Example Link</a><br><br>
				<a href="https://example.com">Example Institution</a><br>
			@endif
		</footer>
	</div>
	<script>
		var this_url = '{{ url('/') }}';
		window.this_url = this_url;
	</script>
	<script src="{{ asset(mix('js/manifest.js')) }}"></script>
	<script src="{{ asset(mix('js/vendor.js')) }}"></script>
	<script src="{{ asset(mix('js/app.js')) }}"></script>
	@yield('scripts')
	@if(isset($settings['primary_color']) || isset($settings['secondary_color']) || isset($settings['tertiary_color'] ))
	<style>
		.bg-primary, .btn-primary, .dropdown-item:active, .badge-primary, .page-item.active .page-link, input:checked + .slider{
			background-color: {{ $settings['primary_color'] ?? '#008542' }} !important;
			border-color: {{ $settings['primary_color'] ?? '#008542' }};
		}
		.profile h6{
			color: {{ $settings['secondary_color'] ?? '#C75B12' }};
		}
		h1, h2, h3, h5, .page-link{
			color: {{ $settings['primary_color'] ?? '#008542' }};
		}
		a, #home-top #home-search a:hover, #home-top #home-search a:active{
			color: {{ $settings['primary_color'] ?? '#008542' }};
		}
		#stats{
			background-color: {{ $settings['secondary_color'] ?? '#C75B12' }};
		}
		.btn-success, .btn-success:active, .btn-success:hover, .btn-success:not(:disabled):not(.disabled):active, .btn-success:not(:disabled):not(.disabled).active, .show > .btn-success.dropdown-toggle{
			background-color: {{ $settings['tertiary_color'] ?? '#69BE28' }};
		}
		#home-top #home-search a{
			color: {{ $settings['secondary_color'] ?? '#C75B12' }};
		}
		#footer-container{
			border-color: {{ $settings['tertiary_color'] ?? '#69BE28' }};
		}
	</style>
	@endif
</body>
</html>
