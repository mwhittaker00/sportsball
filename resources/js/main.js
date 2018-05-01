$(document).ready(function () {

$('#close-status').on('click',function(){
  $('#status-alert').hide();
  return false;
});

// get current URL
var url = window.location.pathname;
// filename without extension
var filename = url.match(/([^\/]+)(?=\.\w+$)/)[0];
// add the 'active' link class to the nav item for the current page
$('#'+filename).addClass('active');
});
