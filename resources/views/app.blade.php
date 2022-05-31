<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Short Url Generator</title>
<link href="{{asset('/favicon.ico')}}" rel="shortcut icon" type="image/x-icon">
<link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body>
	<div id="app">
		<app-header></app-header>
		<div class="main"><router-view></router-view></div>
	</div>
	<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
