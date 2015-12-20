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
	// select all packages switch
	$('#allData')
		.switchButton({
			labels_placement: "right",
			on_label: 'Remove',
			off_label: 'Remove',
			checked: false
		})
		.change(function() {  //on change
			$("#tblData tbody").empty(); // empty table
			$.ajax({ 
				type: 'POST',
			   url: "/plugins/speedtest/include/delete_node.php", // delete all nodes
	  	  		dataType: 'json',
	  		  	data: {id: "all"},
		  	  	error: function() {
	 	 	   	alert("Data could not be written to\n/boot/config/plugins/speedtest/speedtest.xml.");
	  	  		},
  			  	success: function () {
  			  		$('#allData') // reset all remove switch
					.switchButton({
						checked: false
					})
		  	  	}
			});
   	});
   $("#btnBegin").bind("click", beginTEST);// bind click to begin test

   //tablesorter
	$('#tblData').tablesorter({headers:{5:{sorter:false}}});

	//load table from xml
	parseDataXML();
	
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

function parseDataXML(){
  	$.ajax({
   	type: "GET",
   	url: "/boot/config/plugins/speedtest/speedtest.xml",
   	dataType: "xml",
   	success: function(xml) {
			$(xml).find("test").each(function(){
				var Name = $(this).attr("name");
				var Ping = $(this).attr("ping");
				var Download = $(this).attr("download");
				var Upload = $(this).attr("upload");
				var Share = $(this).attr("share");
	  			if (typeof(Share) === "undefined"){
		   		Share = "";
		   	}
		   	$("#tblData tbody").append(
				"<tr id="+Name+" >"+
				"<td>"+strftime(DateTimeFormat, new Date(parseInt(Name)))+"</td>"+ //format time based on unRAID display settings
				"<td>"+Ping+"</td>"+ //Ping
				"<td>"+Download+"</td>"+ //Download
				"<td>"+Upload+"</td>"+ //Upload
				"<td><a class='share_image'>"+Share+ //Share
				"</a></td>"+ //Share
				"<td><input id='"+Name+"_switch' class='checkData' type='checkbox'></td>"+ //checkbox
				"</tr>");
				if(Share)
					$(".share_image").unbind("click", clickImage).bind("click", clickImage); //bind click to image url
				addSwitchButton(Name); //add switch to each row
			});
   	},
		complete : function () {
			$("#tblData").trigger("update"); //update table for tablesorter
			var Image = $('#tblData tr:last').children("td:nth-child(5)").find('.share_image').html(); // get last row image 
			if (Image)
			 	$('#shareImage').attr('src', Image); //change image to last image if it exists
			else
				$('#shareImage').attr('src', '/plugins/speedtest/images/blank.png');	// change image to blank if it does not exist
		},
       error : function() {}
	});
};

function beginTEST(){
	$("#btnBegin").disabled = "disabled";
	$("#tblData tbody").append( // create testing row
		"<tr id='loading'>"+
		"<td><img src='/plugins/dynamix/images/loading.gif'></td>"+
		"<td><img src='/plugins/dynamix/images/loading.gif'></td>"+
		"<td><img src='/plugins/dynamix/images/loading.gif'></td>"+
		"<td><img src='/plugins/dynamix/images/loading.gif'></td>"+
		"<td><img src='/plugins/dynamix/images/loading.gif'></td>"+
		"<td></td>"+
		"</tr>");
	$('#countdown').html('<font class="green">Testing Internet Bandwidth...</font>');

  	$.ajax({
      type: "POST",
      dataType: "json",
   	url: "/plugins/speedtest/include/speedtest.php",
   	data: { show : true },
   	success: function(data) {
 			$("#loading").remove();
			$('#countdown').empty();
			$("#tblData tbody").append(
			"<tr id="+data.Name+" >"+
			"<td>"+strftime(DateTimeFormat, new Date(parseInt(data.Name)))+"</td>"+ // format time based on unRAID display settings 
			"<td>"+data.Ping+"</td>"+ //Ping
			"<td>"+data.Download+"</td>"+ //Download
			"<td>"+data.Upload+"</td>"+ //Upload
			"<td><a class='share_image'>"+data.Share+ //Share
			"</a></td>"+ //Share
			"<td><input id='"+data.Name+"_switch' class='checkData' type='checkbox' ></td>"+ //Checkbox
			"</tr>");
			addSwitchButton(data.Name); // add switch
			if (data.Share){
				$(".share_image").unbind("click", clickImage).bind("click", clickImage);// bind click to image url
				$('#shareImage').attr('src', data.Share );
			}
			form.btnBegin.disabled = false; // reset test button
 		},
		complete : function () {
			$("#tblData").trigger("update"); //update table for tablesorter
		},
       error : function() {},
       cache: false
	});
};

function addSwitchButton(Name) {

	// add remove switchbutton
	$("#"+Name+"_switch")
	.switchButton({
		labels_placement: 'right',
		on_label: 'Remove',
  		off_label: 'Remove',
	  	checked: false
  		})
	.change(function() {
		var par = $(this).parent().parent();
		var Name = par.attr("id"); // row id
		par.remove(); //remove table row
		$.ajax({
			type: 'POST',
		   url: "/plugins/speedtest/include/delete_node.php", //delete node by name
	  	  	dataType: 'json',
	  	  	data: {id: Name},
	  	  	error: function() {
 	 	   	alert("Data could not be written to\n/boot/config/plugins/speedtest/speedtest.xml.");
	  	  	},
  		  	success: function () {
	  	  	}
		});
	});
};

function clickImage() {
	$('#shareImage').attr('src', $(this).html());
};
