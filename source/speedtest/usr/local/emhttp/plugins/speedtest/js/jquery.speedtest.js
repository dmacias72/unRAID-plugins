google.charts.load('current', {'packages':['bar','line','corechart']});

$(function(){
	//load table from xml
	parseDataXML();
	
   $("#btnBegin").click(beginTEST);// bind click to begin test
   
	//tablesorter
	$('#tblTests').tablesorter({
		textExtraction : function(node, table, cellIndex){
			n = $(node);
			return n.attr('data-sortValue') || n.text();
		},
		sortList: [[0,1]],
		widgets: ["saveSort", "filter", "stickyHeaders", "Chart"],
		widgetOptions: {
			chart_ignoreColumns: [0,1,2,5,6],
			chart_sort: [[0,1]],
			stickyHeaders_filteredToTop: true,
			filter_hideEmpty: true,
			filter_liveSearch: true,
			filter_saveFilters: true,
			filter_reset : 'a.reset',
			filter_functions: {
				'.filter-date' : {
      	    	"3 days"   : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 259200000);   }, //3*24*60*60
					"1 week"   : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 604800000);   }, //7*24*60*60
  	      		"2 weeks"  : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 1209600000);  }, //14*24*60*60
					"1 month"  : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 2592000000);  }, //30*24*60*60
					"6 months" : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 15724800000); }, //26*7*24*60*60
					"1 year"   : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 31449600000); } //52*7*24*60*60
				},
				'.filter-host'  : true,
				'.filter-ping'  : {
					"< 10 ms"    : function(e, n, f, i, $r, c, data) { return parseInt(n) < 10; },
					"10 - 20 ms" : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 10 && parseInt(n) <= 20; },
					"> 20 ms"    : function(e, n, f, i, $r, c, data) { return parseInt(n) > 20; }
				},
				'.filter-download'    : {
					"< 50 Mbit/s"      : function(e, n, f, i, $r, c, data) { return parseInt(n) < 50; },
					"50 - 100 Mbit/s"  : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 50 && parseInt(n) <=  100; },
					"100 - 150 Mbit/s" : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 100 && parseInt(n) <= 150; },
					"> 150 Mbit/s"     : function(e, n, f, i, $r, c, data) { return parseInt(n) > 150; }
				},
				'.filter-upload'   : {
					"< 1 Mbit/s"    : function(e, n, f, i, $r, c, data) { return parseInt(n) < 1; },
					"1 - 5 Mbit/s"  : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 1 && parseInt(n) <= 5; },
					"5 - 10 Mbit/s" : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 5 && parseInt(n) <= 10; },
					"> 10 Mbit/s"   : function(e, n, f, i, $r, c, data) { return parseInt(n) > 10; }
				}
			}
		}
	})
	.tablesorterPager({
		container: $(".pager"),
		fixedHeight: false,
		size: 5
	});
		//chart switch and cookie
	$("#chartSelect")
		.switchButton({
			labels_placement: "left",
			on_label: "On",
			off_label: "Off",
			checked: false 	//$.cookie('speedtest_chart') == "enable"
		})
		.change(function () {
			//$.cookie('speedtest_chart', $(this).prop("checked") ? "enable" : "disable", { expires: 3650 });
	   	$('#chart-container').slideToggle( $(this).is(':checked') );
			if ($('#chartSelect').is(':checked')) {
   			drawChart();
	   		$('.speedtest').hide();
  			}else
				$('.speedtest').show();
 		});

  /* Initial settings */
  var $table = $('#tblTests'),
    $chart = $('#chart'),
    $bar = $('#chartbar'),
    $rowType = $('[name=getrows]'),
    $icons = $('#chart-container i'),
    initType = 'area', // graph types ('pie', 'pie3D', 'line', 'area', 'vbar', 'vstack', 'hbar' or 'hstack')
    chartTitle = 'Speedtest Results',
    hAxisTitle = 'Date',
    vAxisTitle = Units,
    width = 1080,
    height = 400,
    // extra data processing
    processor = function(data) {
      // console.log(data);
      return data;
    },

  // don't change anything below, unless you want to remove some types; modify styles and/or font-awesome icons
  types = {
    line   : { in3D: false, maxCol: 99,stack: false, type: 'line', titleStyle: { color: '#808080' }, icon: 'fa-line-chart' },
    area   : { in3D: false, maxCol: 5, stack: false, type: 'area', titleStyle: { color: '#808080' }, icon: 'fa-area-chart' },
    vbar   : { in3D: false, maxCol: 5, stack: false, type: 'vbar', titleStyle: { color: '#808080' }, icon: 'fa-bar-chart' },
    vstack : { in3D: false, maxCol: 5, stack: true,  type: 'vbar', titleStyle: { color: '#808080' }, icon: 'fa-tasks fa-rotate-90' },
    hbar   : { in3D: false, maxCol: 5, stack: false, type: 'hbar', titleStyle: { color: '#808080' }, icon: 'fa-align-left' },
    hstack : { in3D: false, maxCol: 5, stack: true,  type: 'hbar', titleStyle: { color: '#808080' }, icon: 'fa-tasks fa-rotate-180' }
  },
  /* internal variables */
  settings = {
    table : $table,
    chart : $chart[0],
    chartTitle : chartTitle,
    hAxisTitle : hAxisTitle,
    vAxisTitle : vAxisTitle,
    type : initType,
    processor : processor
  },
  drawChart = function() {
    if (!$table[0].config) { return; }
    var options, chart, numofcols, tabledata,
      s = settings,
      t = types[s.type],
      obj = s.chart,
      rawdata = $table[0].config.chart.data;
    if ( $.isFunction( s.processor ) ) {
      rawdata = s.processor( rawdata );
    }
    if ( rawdata.length < 2 ) {
      return;
    }
    tabledata = google.visualization.arrayToDataTable( rawdata );

    numofcols = rawdata[1].length;
    if (numofcols > t.maxCol) {
      // default to line chart if too many columns selected
      t = types['line'];
    }

    options = {
      title: s.chartTitle,
      chart: {
        title: s.chartTitle,
        titleTextStyle: t.titleStyle
      },
      hAxis: {
      	title: s.hAxisTitle,
      	titleTextStyle: t.titleStyle,
      	textStyle: t.titleStyle
      },
      vAxis: {
      	title: s.vAxisTitle,
      	titleTextStyle: t.titleStyle,
      	textStyle: t.titleStyle
      },
      is3D: t.in3D,
      isStacked: t.stack,
      width: width,
      height: height,
		backgroundColor: 'transparent',
		legendTextStyle: t.titleStyle,
		titleTextStyle: t.titleStyle
    };

    if (t.type == 'vbar' && !t.stack) {
      chart = new google.visualization.ColumnChart(obj);
    } else if (t.type == 'vbar') {
      chart = new google.visualization.ColumnChart(obj);
    } else if (t.type == 'hbar') {
      options.hAxis = {
      	title: s.vAxisTitle,
			titleTextStyle: t.titleStyle,
			textStyle: t.titleStyle
			};
      options.vAxis = {
        title: s.hAxisTitle,
        titleTextStyle: t.titleStyle,
        textStyle: t.titleStyle,
        minValue: 0
      };
      chart = new google.visualization.BarChart(obj);
    } else if (t.type == 'area') {
      chart = new google.visualization.AreaChart(obj);
    } else if (t.type == 'line') {
      chart = new google.visualization.LineChart(obj);
    } else {
      chart = new google.visualization.PieChart(obj);
    }
    chart.draw(tabledata, options);
  };

  $icons.click(function(e) {
    if ( $(e.target).hasClass('disabled') ) {
      return true;
    }
    $icons.removeClass('active');
    var $t = $(this).addClass('active');
    $.each(types, function(i, v){
      if ($t.hasClass(v.icon)) {
        settings.type = i;
      }
    });
    drawChart();
  });

  $rowType.on('change', function(){
    $table[0].config.widgetOptions.chart_incRows = $rowType.filter(':checked').attr('data-type');
    // update data, then draw new chart
    $table.trigger('chartData');
    drawChart();
  });

  $table.on('columnUpdate pagerComplete', function(e) {
    var table = this,
      c = table.config,
      t = types['pie'],
      max = t && t.maxCol || 2;
    setTimeout(function() {
      if (table.hasInitialized) {
        $table.trigger('chartData');
        drawChart();
        // update chart icons
        if (typeof c.chart !== 'undefined') {
          var cols =  c.chart.data[0].length;
          if (cols > max) {
            $bar.find('.fa-cube, .fa-pie-chart').addClass('disabled');
            if ($bar.find('.fa-cube, .fa-pie-chart').hasClass('active')) {
              $bar.find('.fa-cube, .fa-pie-chart').removeClass('active');
              $bar.find('.fa-line-chart').addClass('active');
            }
          } else {
            $bar.find('.fa-cube, .fa-pie-chart').removeClass('disabled');
            if (settings.type == 'pie') {
              $bar.find('.active').removeClass('active');
              $bar.find( settings.in3D ? '.fa-cube' : '.fa-pie-chart' ).addClass('active');
            }
          }
        }
      }
    }, 10);
  });

console.log($table[0]);
})

// parse speedtest xml data
function parseDataXML(){
	$.get("/boot/config/plugins/speedtest/speedtest.xml", function(xml) {
		$(xml).find("test").each(function(){
			var Name = $(this).attr("name");
			var Host = ($(this).attr("host")) ? $(this).attr("host") : "--";
			var Ping = ($(this).attr("ping")) ? $(this).attr("ping") : "--";
			var Download = ($(this).attr("download")) ? $(this).attr("download") : "--";
			var Upload = ($(this).attr("upload")) ? $(this).attr("upload") : "--";
			var Share = ($(this).attr("share")) ? $(this).attr("share") : "";

	   	$("#tblTests tbody").append(
			"<tr id='"+Name+"' class='shareRow' title='click to show image'>"+
			"<td data-sortValue='"+Name+"' >"+strftime('%Y-%m-%d %H:%M %a', new Date(parseInt(Name))).trim()+"</td>"+ //DateTimeFormat for format time based on unRAID display settings
			"<td>"+Host+"</td>"+ //Host
			"<td>"+Ping+"</td>"+ //Ping
			"<td>"+Download+"</td>"+ //Download
			"<td>"+Upload+"</td>"+ //Upload
			"<td>"+Share+"</td>"+ //Share
			"<td><a class='delete' title='delete'><i class='fa fa-trash'></i></a>"+ //delete icon
			"</tr>");

		});

   	$("#allTests").click(function() { // bind click to delete all
  			Delete('all');
		});

		$('.shareRow').click(function () { //bind click to row for url image
     		shareImage($(this))});

		$('.delete').click(function () { //bind delete to each delete icon
     		Delete($(this).parent().parent().attr("id"));
  		});

		$("#tblTests tr:last").addClass("lastRow"); // add class to last test

		$("#tblTests").trigger("update");
		shareImage($('#tblTests .lastRow')); // show image for last test
	});
}

// open shadowbox and begin speedtest
function beginTEST() {
  // open shadowbox window (run in foreground)
  var run = '/logging.htm?cmd=/plugins/speedtest/scripts/speedtest-xml';
  var options = {modal:true,onClose:function(){document.location.reload(true);}};
  Shadowbox.open({content:run, player:'iframe', title:'Speedtest', height:400, width:600, options:options});
}

// show image or display blank
function shareImage(Image) {
	Image = Image.children("td:nth-child(6)").text(); // get last row image
	if (Image)
	 	$('#shareImage').attr('src', Image); //change image if it exists
	else
		$('#shareImage').attr('src', '/plugins/speedtest/images/blank.png');	// change image to blank if it does not exist
}

// animate row deletion
function slideRow(par) {
	par
	.children('td')
	.animate({ padding: 0 })
	.wrapInner('<div />')
	.children()
	.slideUp(function() { par.remove(); });
	$("#tblTests").trigger("update")
}

// delete table row or entire table
function Delete(Row) {
	if (Row == "all"){
		swal({
			title: "Are you sure?", 
			text: "Are your sure you want to remove all speedtests!?", 
			type: "warning",
			showCancelButton: true,
			closeOnConfirm: true,
		}, function() {
		$.get("/plugins/speedtest/include/delete_node.php", {id: Row}, function() {
			$("#tblTests tbody").empty(); // empty table
			}
		);
    });
	} else {
		$.get("/plugins/speedtest/include/delete_node.php", {id: Row}, function() {
			if ($('#'+Row).hasClass("lastRow")){
				if ($('.filter-date').hasClass('tablesorter-headerDesc'))
					$('#'+Row).next('tr').addClass('lastRow');
				else
					$('#'+Row).prev('tr').addClass('lastRow');

				slideRow($('#'+Row)); //remove table row
				shareImage($('#tblTests .lastRow'));
			} else{
				slideRow($('#'+Row)); //remove table row
			}
		});
	}
}