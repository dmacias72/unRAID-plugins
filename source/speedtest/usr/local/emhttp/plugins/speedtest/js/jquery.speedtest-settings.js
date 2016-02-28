$(function(){
   $('#LIST').change(function () {
       if ($('#LIST')[0].selectedIndex){
       		$('.serverlist').css('visibility','visible')
           getServerList(Selected);
       } else {
           $('.serverlist').css('visibility','hidden')
       } 
   });
   
   if ($('#LIST')[0].selectedIndex){
   	$('.serverlist').css('visibility','visible')
		getServerList(Selected);
   } else {
   	$('.serverlist').css('visibility','hidden')
	}
});

// list all available servers
function getServerList(Selected){
  	$.getJSON('/plugins/speedtest/include/speedtest-list.php', {}, function(data) {
	   var serverList= '<option ';
	   if (Selected === ''){
	   	serverList+= "selected='' ";
	   }
   	for (var i = 0; i < data.length; i++){
	   	serverList+= '<option '; 
	   	if (data[i][0] === Selected){
	   		serverList+= "selected='' ";
	   	}
	   	serverList+= "value='" + data[i][0] + "'>" + data[i][1] + "</option>";
		}
	   $('#SERVER').html(serverList);
	});
};

function resetDATA(form) {
	form.SECURE.value = '';
	form.SHARE.value = '--share';
	form.UNITS.value = '';
	form.LIST.value = 'auto';
	form.SERVER.value = '';
};
