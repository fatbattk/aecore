// Update a task
function processSets() {
  $.ajax({
    xhr: function() {
      var xhr = new XMLHttpRequest();
      // progress
      xhr.addEventListener("progress", function(e){
        $('#progressbar').html(e.loaded + '%');
        $('#progressbar').css('width', e.loaded + '%');
      }, false);
      return xhr;
    },
    type: 'POST',
    url: '/planroom/process',
    success: function(data){
      $('#progressbar').html('100%');
      $('#progressbar').css('width', '100%');
        
      $('#processtext').hide();
      $('#navtext').hide();
      $('#ready').show();
    }
  });
}

function define_discipline(sheet_name) {
  
  // Define disciplines
  var discipline = {
    A:"Architectural",
    C:"Civil",
    E:"Electrical",
    F:"Fire Protection",
    G:"General",
    H:"Hazardous Materials",
    I:"Interior",
    K:"Kitchen",
    L:"Landscape",
    M:"Mechanical",
    P:"Plumbing",
    Q:"Equipment",
    R:"Resource",
    S:"Structural",
    T:"Telecommunications",
    Z:"Contractor / Shop Drawings"
  };
  
  // First character of sheet name
  var sheet_name = sheet_name.charAt(0);
  
  // Update discipline value
  $('#sheet_discipline').val(discipline[sheet_name]);
}

// Update a task
function update_revision(sheet_number) {  
  $.ajax({
    type: "POST",
    url: '/planroom/sheets/checkrevision',
    data: {sheet_number:sheet_number}
  }).done(function(result) {
      if(result.sheet_name != null) {
        $('#sheet_name').val(result.sheet_name);
      }
      if(result.sheet_revision != null) {
        $('#sheet_revision').val(result.sheet_revision);
      }
    });
}