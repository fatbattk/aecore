@extends('layouts.application.main_wide')
@extends('layouts.application.project_nav')
@section('content')

<!-- define previous and next -->
<?php
  if($previous != null) {
    $previous_code = $previous->sheet_code;
    $previous_number = $previous->sheet_number;
  } else {
    $previous_code = '';
    $previous_number = '';
  }
  if($next != null) {
    $next_code = $next->sheet_code;
    $next_number = $next->sheet_number;
  } else {
    $next_code = '';
    $next_number = '';
  }
?>

<script type="text/javascript" src="{!! URL::asset('js/planroom.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/leaflet-0.7.3/leaflet-src.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/leaflet-0.7.3/leaflet.fullscreen.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/leaflet-0.7.3/button-control.js') !!}"></script>
<link rel="stylesheet" href="{!! URL::asset('js/leaflet-0.7.3/leaflet.css') !!}" />

<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/leaflet.draw.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/Control.Draw.js') !!}"></script>

<!--<link rel="stylesheet" href="public/leaflet-0.7.3/leaflet-draw.css" />
<link rel="stylesheet" href="public/leaflet-0.7.3/leaflet.illustrate.css" />

<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/Toolbar.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/Tooltip.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/ext/GeometryUtil.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/ext/LatLngUtil.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/ext/LineUtil.Intersect.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/ext/Polygon.Intersect.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/ext/Polyline.Intersect.js') !!}"></script>

<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/draw/DrawToolbar.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/draw/handler/Draw.Feature.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/draw/handler/Draw.SimpleShape.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/draw/handler/Draw.Polyline.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/draw/handler/Draw.Polygon.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/draw/handler/Draw.Rectangle.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/draw/handler/Draw.Cloud.js') !!}"></script>

<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/draw/handler/Draw.Circle.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/draw/handler/Draw.Marker.js') !!}"></script>

<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/edit/EditToolbar.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/edit/handler/EditToolbar.Edit.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/edit/handler/EditToolbar.Delete.js') !!}"></script>

<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/edit/handler/Edit.Poly.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/edit/handler/Edit.SimpleShape.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/edit/handler/Edit.Circle.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/edit/handler/Edit.Rectangle.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('public/leaflet-0.7.3/leaflet-draw/edit/handler/Edit.Marker.js') !!}"></script>-->

<script type="text/javascript">
  $(document).ready(function() {
    
    // Initiate leaflet map
    var map = L.map('map', {
      //drawControl: true,
      fullscreenControl: true,
      attributionControl: false,
      center: [45, 0],
      zoom: 1,
      minZoom:1,
      maxZoom:3
    });
    
    // Add map tiles
    L.tileLayer('https://s3-us-west-1.amazonaws.com/tiles.aecore.com/<?php echo $sheet->sheet_code; ?>/{z}/{x}-{y}.png', {
      noWrap: true
    }).addTo(map);
    
   var myButton = L.control({ position: 'topright' });

    myButton.onAdd = function (map) {
        this._div = L.DomUtil.create('div', 'btn-group');
        this._div.innerHTML = '<?php if($previous != null) { ?><a href="/planroom/sheet/<?php echo $previous_code; ?>" class="btn btn-sm btn-default bold"><span class="glyphicon glyphicon-menu-left"></span> <?php echo $previous_number; ?></a><?php } ?> <?php if($next != null) { ?><a href="/planroom/sheet/<?php echo $next_code; ?>" class="btn btn-sm btn-default bold"><?php echo $next_number; ?> <span class="glyphicon glyphicon-menu-right"></span></a><?php } ?>';
        return this._div;
    };

    myButton.addTo(map); 
    
  });
</script>

<div class="page-wrapper" style="overflow:hidden;">
  <div class="pagehead" style="border-bottom:1px solid #ccc;">
    <div class="container-fluid">
      <span class="btn btn-primary pull-left toggle-nav" style="margin-right:10px;padding:7px;" onClick="$('#projectnav').toggle();"><span class="glyphicon glyphicon-menu-hamburger"></span></span>
      <a href="https://s3-us-west-1.amazonaws.com/tiles.aecore.com/<?php echo $sheet->sheet_code; ?>/<?php echo $sheet->sheet_code; ?>.pdf" target="_blank" class="btn btn-default btn-sm pull-right" title="Download pdf."><span class="glyphicon glyphicon-cloud-download"></span> Download</a>
      <h1>{!! $sheet->sheet_number . ' ' . $sheet->sheet_name !!}</h1>
    </div>
  </div>
  
  <div class="sheetcontainer">
    <div id="map"></div>
  </div>
</div>

@stop