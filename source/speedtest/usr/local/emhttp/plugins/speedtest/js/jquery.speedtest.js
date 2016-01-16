$(function(){
   $("#btnBegin").click(beginTEST);// bind click to begin test

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
				var Host = ($(this).attr("host")) ? $(this).attr("host") : "--";
				var Ping = ($(this).attr("ping")) ? $(this).attr("ping") : "--";
				var Download = ($(this).attr("download")) ? $(this).attr("download") : "--";
				var Upload = ($(this).attr("upload")) ? $(this).attr("upload") : "--";
				var Share = ($(this).attr("share")) ? $(this).attr("share") : "";

		   	$("#tblData tbody").append(
				"<tr id="+Name+" class='shareRow' title='click to show image'>"+
				"<td data-sortValue='"+Name+"' >"+strftime(DateTimeFormat, new Date(parseInt(Name))).trim()+"</td>"+ //format time based on unRAID display settings
				"<td>"+Host+"</td>"+ //Host
				"<td>"+Ping+"</td>"+ //Ping
				"<td>"+Download+"</td>"+ //Download
				"<td>"+Upload+"</td>"+ //Upload
				"<td>"+Share+ //Share
				"</td>"+ //Share
				"<td><a class='delete' title='delete'><i class='fa fa-trash'></i></a>"+ //delete icon
				"</tr>");

				if(Share)
					$('.shareRow').unbind('click',clickRow).bind('click',clickRow); //bind click to row for url image

				$('.delete').unbind('click').click(function () {
        			Delete($(this).parent().parent().attr("id"));
    			});

			});

			$("#tblData").trigger("update");
			$("#tblData tr:last").addClass("lastRow"); // add class to last test

  			//tablesorter
			$('#tblData').tablesorter({
				textExtraction : function(node, table, cellIndex){
					n = $(node);
					return n.attr('data-sortValue') || n.text();
    			},
    			sortList: [[0,1]],
				widgets: ['saveSort', 'filter', 'stickyHeaders'],
				widgetOptions: {
					stickyHeaders_filteredToTop: true,
					filter_hideEmpty : true,
					filter_liveSearch : true,
					filter_saveFilters : true,
					filter_reset : 'a.reset',
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
			})
			.tablesorterPager({
				container: $(".pager"),
				fixedHeight: false,
				size: 5
			});

   		$("#allData").click(function() {
  				Delete('all');
			});
   	},
   	complete: function () {
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

function clickRow() {
	$('#shareImage').attr('src', $(this).children("td:nth-child(6)").html());
};

function shareImage() {
	var Image = $('#tblData .lastRow').children("td:nth-child(6)").html(); // get last row image 
	if (Image)
	 	$('#shareImage').attr('src', Image); //change image to last image if it exists
	else
		$('#shareImage').attr('src', '/plugins/speedtest/images/blank.png');	// change image to blank if it does not exist
};

function Delete(Row) {
	var Confirm = (Row == "all") ? confirm("Are your sure you want to remove all speedtests!?"): true;
	if (Confirm){
		$.ajax({ 
			type: 'POST',
		   url: "/plugins/speedtest/include/delete_node.php", // delete all nodes
	  		dataType: 'json',
		  	data: {id: Row},
			success: function (data) {
				if (Row == "all")
					$("#tblData tbody").empty(); // empty table
				else{
					if ($('#'+Row).hasClass("lastRow")){

						if ($('.filter-date').hasClass('tablesorter-headerDesc'))
							$('#'+Row).next('tr').addClass('lastRow');
						else
							$('#'+Row).prev('tr').addClass('lastRow');

					$('#'+Row).remove(); //remove table row
					shareImage();
					} else{
						$('#'+Row).remove(); //remove table row
					}
				}
			},
		  	error: function() {
	 	   	alert("Data could not be written to\n/boot/config/plugins/speedtest/speedtest.xml.");
	  		}
	  	});
	}
};
