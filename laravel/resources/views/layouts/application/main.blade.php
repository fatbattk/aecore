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
    <script type="text/javascript" src="{!! asset('/js/jquery-ui/jquery-ui.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('/js/bootstrap.js') !!}"></script>
    <script type="text/javascript" src="{!! URL::asset('/js/uploadifive/jquery.uploadifive.min.js') !!}"></script>
    <script type="text/javascript">
      $(function(){
        $('body').on('hidden.bs.modal', '.modal', function () {
          $(this).removeData('bs.modal');
        });
      });
    </script>
    
    <!-- load css -->
    <link rel="shortcut icon" href="{!! asset('/css/img/logos/favicon.ico') !!}">
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{!! asset('/css/app.css') !!}">
    <link rel="stylesheet" href="{!! asset('/css/app-custom.css') !!}">
    <link rel="stylesheet" href="{!! asset('/css/bootstrapmod.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('/js/jquery-ui/css/jquery-ui.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('/js/uploadifive/uploadifive.css') !!}">
    <link rel="stylesheet" href="{!! URL::asset('/js/jcrop/css/jquery.jcrop.css') !!}">
    
  </head>
  <body>
    <header class="row">
      <nav class="navbar navbar-fixed-top navbar-default">
        <div class="container">
          @include('layouts.application.header')
        </div>
      </nav>
    </header>
    @yield('content')
    <footer class="row">
      @include('layouts.application.footer')
    </footer>
    
    <!-- Initialize Modal -->
    <div class="modal fade" id="modal" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
        </div> <!-- End Modal Content -->
      </div> <!-- End Modal Dialog -->
    </div> <!-- End Modal -->
  </body>
</html>