$(function(){
	// append tabs for settings and scheduler and chart switch
	$('.tabs')
		.append('<div class="tab"><input type="radio" name="tabs2" id="tab3"><label for="tab3"><img class="icon" src="/plugins/speedtest/icons/settings.png">Settings</label></div>')
		.append('<div class="tab"><input type="radio" name="tabs2" id="tab4"><label for="tab4"><img class="icon" src="/plugins/speedtest/icons/scheduler.png">Scheduler</label></div>')
		.append("<span class='status'><input id='chartSelect' class='hidden' type='checkbox'></span>");

	//load table from xml
	$('#wait').html("<br><i class='fa fa-spinner fa-spin icon'></i><em>Please wait, retrieving speedtest data...</em><p></p>");
	parseDataXML();
	$('#wait').empty();
	
	//get chart type cookie and set button active
	var ActiveChart = ($.cookie('speedtest_charttype')) ? $.cookie('speedtest_charttype') : 'area';
	$('#'+ActiveChart).addClass('active');
	
	//get chart data cookie and set button active
	var DataType = ($.cookie('speedtest_datatype')) ? $.cookie('speedtest_datatype') : 'filter';
	$('.btn-group label#'+DataType).addClass('active');
	
	//chart switch and cookie
	$('#chartSelect')
		.switchButton({
			labels_placement: 'right',
			on_label: "<i class='fa fa-pie-chart'></i> Charts On ",
			off_label: "<i class='fa fa-pie-chart'></i> Charts Off",
			checked: $.cookie('speedtest_charts') == 'enable'
		})
		.change(function () {
			$.cookie('speedtest_charts',$('#chartSelect')[0].checked ? 'enable' : 'disable', { expires: 3650 });
			if ($('#chartSelect')[0].checked){
				//$('#tab2').parent().show();
				$('#tblTests').trigger('chartData');
				drawChart();
			}else {
				//$('#tab2').parent().hide();
				shareImage($('#tblTests .lastRow').children("td:nth-child(7)").html());
			}
			$('#chart-container, #shareImage').slideToggle();
		});
	
	$('.btnBegin').click(beginTEST);// bind click to begin test

	/* Initial settings */
	var $table = $('#tblTests'),
	$chart = $('#chart'),
	$bar = $('#chartbar'),
	$rowType = $('[name=getrows]'),
	$icons = $('#chart-container i'),
	initType = ($.cookie("speedtest_charttype")) ? $.cookie("speedtest_charttype") : 'area', // graph types ('pie', 'pie3D', 'line', 'area', 'vbar', 'vstack', 'hbar' or 'hstack')
	chartTitle = 'Speedtest Results',
	hAxisTitle = 'Date',
	vAxisTitle = Units,
	width = 1080,
	height = 300,
	// extra data processing
	processor = function(data) {
		// console.log(data);
		return data;
	},

	// don't change anything below, unless you want to remove some types; modify styles and/or font-awesome icons
	types = {
	line	  : { in3D: false, maxCol: 99,stack: false, type: 'line', titleStyle: { color: '#808080' }, icon: 'fa-line-chart' },
	area	  : { in3D: false, maxCol: 5, stack: false, type: 'area', titleStyle: { color: '#808080' }, icon: 'fa-area-chart' },
	vbar	  : { in3D: false, maxCol: 5, stack: false, type: 'vbar', titleStyle: { color: '#808080' }, icon: 'fa-bar-chart' },
	vstack  : { in3D: false, maxCol: 5, stack: true,  type: 'vbar', titleStyle: { color: '#808080' }, icon: 'fa-tasks fa-rotate-90' },
	hbar	  : { in3D: false, maxCol: 5, stack: false, type: 'hbar', titleStyle: { color: '#808080' }, icon: 'fa-align-left' },
	hist	  : { in3D: false, maxCol: 5, stack: false, type: 'hist', titleStyle: { color: '#808080' }, icon: 'fa-tasks fa-rotate-180' },
	scatter : { in3D: false, maxCol: 5, stack: false, type: 'scat', titleStyle: { color: '#808080' }, icon: 'fa-ellipsis-h' }
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
		} else if (t.type == 'hist') {
			chart = new google.visualization.Histogram(obj);
		} else if (t.type == 'scat') {
			chart = new google.visualization.ScatterChart(obj);
		}
		chart.draw(tabledata, options);
	};

	// set icon cookie and class
	$icons.click(function(e) {
		if ( $(e.target).hasClass('disabled') ) {
			return true;
		}
		$icons.removeClass('active');
		var $t = $(this).addClass('active');
		$.each(types, function(i, v){
			if ($t.hasClass(v.icon)) {
				settings.type = i;
				$.cookie("speedtest_charttype", i, { expires: 3650 });
			}
		});
		drawChart();
	});

	// change and save chart data type
	$rowType.on('change', function(){
		DataType = $rowType.filter(':checked').attr('data-type');
		$.cookie('speedtest_datatype', DataType, { expires: 3650 });
		$table[0].config.widgetOptions.chart_incRows = DataType;
		// update data, then draw new chart
		$table.trigger('chartData');
		drawChart();
	});

	//tablesorter
	$table.tablesorter({
		textExtraction : function(node, table, cellIndex){
			n = $(node);
			return n.attr('data-sortValue') || n.text();
		},
		sortList: [[0,1]],
		widgets: ['saveSort', 'filter', 'stickyHeaders', 'Chart'],
		widgetOptions: {
			chart_incRows: DataType,
			chart_ignoreColumns: [0,1,2,3,6,7],
			chart_sort: [[0,1]],
			stickyHeaders_filteredToTop: true,
			filter_hideEmpty: true,
			filter_liveSearch: true,
			filter_saveFilters: true,
			filter_reset : 'a.reset',
			filter_functions: {
				'.filter-date' : {
					'3 days'   : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 259200000);   }, //3*24*60*60
					'1 week'   : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 604800000);   }, //7*24*60*60
					'2 weeks'  : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 1209600000);  }, //14*24*60*60
					'1 month'  : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 2592000000);  }, //30*24*60*60
					'6 months' : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 15724800000); }, //26*7*24*60*60
					'1 year'   : function(e, n, f, i, $r, c, data) { return ($.now() - parseInt($r.attr('id')) <= 31449600000); } //52*7*24*60*60
				},
				'.filter-host' 	 : true,
				'.filter-distance'			: {
					'&nbsp;0 - 10 km' : function(e, n, f, i, $r, c, data) { return parseInt(n) <= 10; },
					'10 - 20 km'		: function(e, n, f, i, $r, c, data) { return parseInt(n) >= 10 && parseInt(n) <= 20; },
					'20 - 30 km'		: function(e, n, f, i, $r, c, data) { return parseInt(n) >= 20 && parseInt(n) <= 30; },
					'30 - 40 km'		: function(e, n, f, i, $r, c, data) { return parseInt(n) >= 30 && parseInt(n) <= 40; },
					'40 - 50 km'		: function(e, n, f, i, $r, c, data) { return parseInt(n) >= 40 && parseInt(n) <= 50; },
					'50 - &infin; km' : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 50; }
				},
				'.filter-ping'			: {
					'&nbsp;0 - 10 ms' : function(e, n, f, i, $r, c, data) { return parseInt(n) <= 10; },
					'10 - 20 ms'		: function(e, n, f, i, $r, c, data) { return parseInt(n) >= 10 && parseInt(n) <= 20; },
					'20 - &infin; ms' : function(e, n, f, i, $r, c, data) { return parseInt(n) >= 20; }
				},
				'.filter-download'		: {
					'&nbsp;&nbsp;0 - 50' : function(e, n, f, i, $r, c, data) { return parseInt(n) <= 50; },
					'&nbsp;50 - 100'  	: function(e, n, f, i, $r, c, data) { return parseInt(n) >= 50 && parseInt(n) <=  100; },
					'100 - 150' 			: function(e, n, f, i, $r, c, data) { return parseInt(n) >= 100 && parseInt(n) <= 150; },
					'150 - &infin;'		: function(e, n, f, i, $r, c, data) { return parseInt(n) >= 150; }
				},
				'.filter-upload'	: {
					'&nbsp;0 - 1'	: function(e, n, f, i, $r, c, data) { return parseInt(n) <= 1; },
					'&nbsp;1 - 5'	: function(e, n, f, i, $r, c, data) { return parseInt(n) >= 1 && parseInt(n) <= 5; },
					'&nbsp;5 - 10'	: function(e, n, f, i, $r, c, data) { return parseInt(n) >= 5 && parseInt(n) <= 10; },
					'10 - &infin;'	: function(e, n, f, i, $r, c, data) { return parseInt(n) >= 10; }
				}
			}
		}
	})
	.tablesorterPager({
		container: $('.pager'),
		fixedHeight: false,
		size: 5
	})
	.on('removeRow', function () {
		$table.trigger('chartData');
		drawChart();
	})
	.on('columnUpdate pagerComplete', function(e) {
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
					}else{
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

})

// parse speedtest xml data
function parseDataXML(){
	$('#tblTests tbody').empty();
	$.ajax({
		cache: false,
		url: '/boot/config/plugins/speedtest/speedtest.xml',
		success: function(xml) {
		$(xml).find('test').each(function(){
			var Name = $(this).attr('name');
			var HostArray = ($(this).attr('host')) ? $(this).attr("host").split(' [') : ['--','--'];
			var Host = HostArray[0].trim();
			var Distance = HostArray[1].replace(/]/g,'').trim();
			var Ping = ($(this).attr('ping')) ? $(this).attr("ping") : '--';
			var Download = ($(this).attr('download')) ? $(this).attr('download') : '--';
			var Upload = ($(this).attr('upload')) ? $(this).attr('upload') : '--';
			var Share = ($(this).attr('share')) ? $(this).attr('share') : '';

		   	$('#tblTests tbody').append(
				"<tr id='"+Name+"' title='click to show image'>"+
				"<td class='tdShare' data-sortValue='"+Name+"' >"+strftime('%Y-%m-%d %H:%M %a', new Date(parseInt(Name))).trim()+"</td>"+
				"<td class='tdShare'>"+Host+"</td>"+ //Host
				"<td class='tdShare'>"+Distance+"</td>"+ //Distance
				"<td class='tdShare'>"+Ping+"</td>"+ //Ping
				"<td class='tdShare'>"+Download+"</td>"+ //Download
				"<td class='tdShare'>"+Upload+"</td>"+ //Upload
				"<td class='tdShare'>"+Share+"</td>"+ //Share
				"<td><a class='delete' title='delete'><i class='fa fa-trash'></i></a></td>"+ //delete icon
				"</tr>");

			});

			// bind click to delete all
			$('#allTests').click(function() {
				Delete('all');
			});

			//bind click to row for image url
			$('.tdShare').click(function () {
				shareImage($(this).parent().children('td:nth-child(7)').html())
 	    	});

			//bind delete to each delete icon
			$('.delete').click(function () {
				Delete($(this).parent().parent().attr('id'));
			});

			// add class to last test
			$('#tblTests tr:last').addClass('lastRow');
			$('#tblTests').trigger('update');

			// enable chart or image
			if ($('#chartSelect')[0].checked){
				$('#chart-container').show();
				$('#shareImage').hide();
			}else {
				shareImage($('#tblTests .lastRow').children('td:nth-child(7)').html());
				$('#shareImage').show();
				$('#chart-container').hide();
			}
		}
	});
}

// open shadowbox and begin speedtest
function beginTEST() {
	// open shadowbox window (run in foreground)
	var run = '/logging.htm?cmd=/plugins/speedtest/scripts/speedtest-xml';
	var options = {modal:true,onClose:parseDataXML/*function(){parseDataXML();document.location.reload(true)}*/};
	Shadowbox.open({content:run, player:'iframe', title:'Speedtest', height:400, width:600, options:options});
}

// show image or display blank
function shareImage(Image) {
	if (Image)
	 	$('#image').attr('src', Image); //change image if it exists
	else
		$('#image').attr('src', '/plugins/speedtest/images/blank.png'); // change image to blank
}

// delete table row or entire table
function Delete(Row) {
	var deleteNode = '/plugins/speedtest/include/delete_node.php';
	if (Row == "all"){
		swal({
			title: 'Are you sure?',
			text:  'Are your sure you want to remove all speedtests!?',
			type:  'warning',
			showCancelButton: true,
			closeOnConfirm: true,
		}, function() {
		$.get(deleteNode, {id: Row}, function() {
			$('#tblTests tbody').empty(); // empty table
			shareImage(false); // show blank image
			$('#chart').empty();
			}
		);
		});
	} else {
		$.get(deleteNode, {id: Row}, function() {
			var tRow = $('#'+Row);
			// add class lastrow and show image of next row
			tRow.next('tr').addClass('lastRow');

			shareImage(tRow.next('tr').children("td:nth-child(7)").html());

			// animate row deletion
			tRow
			.children('td')
			.animate({ padding: 0 })
			.wrapInner('<div />')
			.children()
			.slideUp(function() {
				tRow.remove();
				$('#tblTests').trigger('update').trigger('removeRow');
			});
		});
	}
}
