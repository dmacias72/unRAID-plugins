$(function(){
   $('#LIST').change(function () {
       if ($('#LIST option:selected').text() == "Manual"){
       		$('.serverlist').css('visibility','visible')
           getServerList(Selected);
       } else {
           $('.serverlist').css('visibility','hidden')
       } 
   });
   
   if ($('#LIST option:selected').text() == "Manual"){
   	$('.serverlist').css('visibility','visible')
		getServerList(Selected);
   } else {
   	$('.serverlist').css('visibility','hidden')
	}
});

function getServerList(Selected){
  	$.ajax({
      type: "GET",
      dataType: "json",
   	url: "/plugins/speedtest/include/speedtest-list.php", // list all available servers
      data: "{}",
   	success: function(data) {
	   	var serverList= "<option ";
	   	if (Selected === ""){
	   		serverList+= "selected='' ";
	   	}
   		for (var i = 0; i < data.length; i++){
	   		serverList+= "<option "; 
	   		if (data[i][0] === Selected){
	   			serverList+= "selected='' ";
	   		}
	   		serverList+= "value='" + data[i][0] + "'>" + data[i][1] + "</option>";
			}
		   $("#SERVER").html(serverList);
		},
      error : function() {},
      cache: true
	});
};

function resetDATA(form) {
	form.SECURE.value = "";
	form.SHARE.value = "--share";
	form.UNITS.value = "";
	form.LIST.value = "auto";
	form.SERVER.value = "";
};
