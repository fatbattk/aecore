L.Draw.Cloud = L.Draw.SimpleShape.extend({
  statics: {
    TYPE: "cloud"
  },
  
  options: {
    shapeOptions: {
      stroke: !0,
      color: "#FF0000",
      weight: 3,
      opacity: 1,
      fill: null,
      fillColor: null,
      fillOpacity: .2,
      clickable: !0,
      noClip: !0
    }},
  
  initialize: function(map, options) {
    this.type = L.Draw.Cloud.TYPE,
    
    this._initialLabelText = L.drawLocal.draw.handlers.cloud.tooltip.start,
    this._endDragLabelText = L.drawLocal.draw.handlers.cloud.tooltip.endDrag,
    this._endClickLabelText = L.drawLocal.draw.handlers.cloud.tooltip.endClick,
    L.Draw.SimpleShape.prototype.initialize.call(this, map, options)
  },
  
  _drawShape: function(latlng) {
		if (!this._shape) {
			this._shape = new L.Cloud(new L.LatLngBounds(this._startLatLng, latlng), this.options.shapeOptions);
			this._map.addLayer(this._shape);
		} else {
			this._shape.setBounds(new L.LatLngBounds(this._startLatLng, latlng));
		}
  },
  
  _fireCreatedEvent: function() {
    var cloud = new L.Cloud(this._shape.getBounds(), this.options.shapeOptions);
    L.Draw.SimpleShape.prototype._fireCreatedEvent.call(this, cloud);
  }})