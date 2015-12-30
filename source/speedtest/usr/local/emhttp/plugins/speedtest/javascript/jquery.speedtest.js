$(function(){
	$('#tblData')
   	 .bind('filterInit', function() {
      	  // check that storage ulility is loaded
	        if ($.tablesorter.storage) {
   	         // get saved filters
      	      var f = $.tablesorter.storage(this, 'tablesorter-filters') || [];
         	   $(this).trigger('search', [f]);
	        }
   	 })
	    .bind('filterEnd', function(){
   	     if ($.tablesorter.storage) {
      	      // save current filters
         	   var f = $(this).find('.tablesorter-filter').map(function(){
            	    return $(this).val() || '';
	            }).get();
   	         $.tablesorter.storage(this, 'tablesorter-filters', f);
      	  }
	});

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

	//load table from xml
	parseDataXML();
	
});

function parseDataXML(){
  	$.ajax({
   	type: "GET",
   	url: "/boot/config/plugins/speedtest/speedtest.xml",
   	dataType: "xml",
   	success: function(xml) {
			$(xml).find("test").each(function(){
				var Name = $(this).attr("name");
				var Host = $(this).attr("host");
	  			if (typeof(Host) === "undefined"){
		   		Host = "";
		   	}
				var Ping = $(this).attr("ping");
				var Download = $(this).attr("download");
				var Upload = $(this).attr("upload");
				var Share = $(this).attr("share");
	  			if (typeof(Share) === "undefined"){
		   		Share = "";
		   	}
		   	$("#tblData tbody").append(
				"<tr id="+Name+" >"+
				"<td data-sortValue='"+Name+"' >"+strftime(DateTimeFormat, new Date(parseInt(Name)))+"</td>"+ //format time based on unRAID display settings
				"<td>"+Host+"</td>"+ //Host
				"<td>"+Ping+"</td>"+ //Ping
				"<td>"+Download+"</td>"+ //Download
				"<td>"+Upload+"</td>"+ //Upload
				"<td><a class='share_image'>"+Share+ //Share
				"</a></td>"+ //Share
				"<td><input id='"+Name+"_switch' class='checkData' type='checkbox'></td>"+ //checkbox
				"</tr>");

				if(Share)
					$(".share_image").unbind("click", clickImage).bind("click", clickImage); //bind click to image url

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
  		  					shareImage();
	  	  				}
					});
				});
			});

  			//tablesorter
			$('#tblData').tablesorter({
				headers:{5:{filter:false},6:{sorter:false, filter:false}},
				textExtraction : function(node, table, cellIndex){
					n = $(node);
					return n.attr('data-sortValue') || n.text();
    			},
    			//sortList: [[0,0]],
				widgets: ['saveSort', 'filter'],
				widgetOptions: {
					filter_hideEmpty : true,
					filter_hideFilters : true,
					filter_functions: {
					'.filter-date' : {
		          	"3 days"  : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 259200000); }, //3*24*60*60
     			    	"1 week"  : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 604800000); }, //7*24*60*60
	   	      	"2 weeks" : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 1209600000); }, //14*24*60*60
     			   	"1 month" : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 2592000000); }, //30*24*60*60
     			   	"6 months" : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 15724800000); }, //26*7*24*60*60
     			   	"1 year" : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 31449600000); } //52*7*24*60*60
	        			},
     		  		'.filter-host' : true,
					'.filter-ping' : {
						"< 10 ms"     : function(e, n, f, i, $r, c, data) { return parseInt(n) < 10; },
						"10 - 20 ms" : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 10 && parseInt(n) <= 20; },
						"> 20 ms"    : function(e, n, f, i, $r, c, data) { return parseInt(n) > 20; }
						},
					'.filter-download' : {
						"< 50 Mbit/s"     : function(e, n, f, i, $r, c, data) { return parseInt(n) < 50; },
						"50 - 100 Mbit/s" : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 50 && parseInt(n) <=  100; },
						"100 - 150 Mbit/s" : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 100 && parseInt(n) <= 150; },
						"> 150 Mbit/s"    : function(e, n, f, i, $r, c, data) { return parseInt(n) > 150; }
						},
					'.filter-upload' : {
						"< 1 Mbit/s"     : function(e, n, f, i, $r, c, data) { return parseInt(n) < 1; },
						"1 - 5 Mbit/s" : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 1 && parseInt(n) <= 5; },
						"5 - 10 Mbit/s" : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 5 && parseInt(n) <= 10; },
						"> 10 Mbit/s"    : function(e, n, f, i, $r, c, data) { return parseInt(n) > 10; }
						}

     		  		}
				}
			});

			$("#tblData").trigger("update")
				.trigger("appendCache")
				.trigger("applyWidgets");
			shareImage();
   	},
       error : function() {}
	});
};

function beginTEST() {
  // open shadowbox window (run in foreground)
  var run = '/logging.htm?cmd=/plugins/speedtest/scripts/speedtest-xml';
  var options = {modal:true,onClose:function(){document.location.reload(true);}};
  Shadowbox.open({content:run, player:'iframe', title:'Speedtest', height:400, width:600, options:options});
}

function clickImage() {
	$('#shareImage').attr('src', $(this).html());
};

function shareImage() {
	var Image = $('#tblData tr:last').children("td:nth-child(6)").find('.share_image').html(); // get last row image 
	if (Image)
	 	$('#shareImage').attr('src', Image); //change image to last image if it exists
	else
		$('#shareImage').attr('src', '/plugins/speedtest/images/blank.png');	// change image to blank if it does not exist
};
