<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Aecore</title>

    <!-- load js dependencies -->
    <script type="text/javascript" src="{!! asset('/js/jquery.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('/js/bootstrap.js') !!}"></script>

    <!-- load css -->
    <link rel="shortcut icon" href="{!! asset('/css/img/logos/favicon.ico') !!}">
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link href="{!! asset('/css/app.css') !!}" rel="stylesheet">
    <link href="{!! asset('/css/app-custom.css') !!}" rel="stylesheet">
  </head>
  
  <body>
    <div class="container">
      <header class="row">
        @include('layouts.storefront.header')
      </header>
      <div id="main" class="row">
        @yield('content')
      </div>
      <footer class="row">
        @include('layouts.storefront.footer')
      </footer>
    </div>
  </body>
</html>