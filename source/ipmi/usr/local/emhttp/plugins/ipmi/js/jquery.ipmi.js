$(function(){
	$('.tabs')
		.append("<span class='status'><label id='settings' title='go to settings page'><i class='fa fa-gear'></i>Settings</label></span>")
		.append("<span id='adv-switch' class='status'><input type='checkbox' id='advancedview'></span>")
		.append("<span id='backup-switch' class='status'><input type='checkbox' id='event-backup'></span>");

	$('#settings').click(function() {
		location = '/Settings/IPMI';
	});

	$("#tab2").click(function () {
		$('#adv-switch').hide();
		$('#backup-switch').show();
	});
	$("#tab1").click(function () {
		$('#adv-switch').show();
		$('#backup-switch').hide();
	});

	if ($("#tab2")[0].checked){
		$('#adv-switch').hide();
		$('#backup-switch').show();
	}else{
		$('#adv-switch').show();
		$('#backup-switch').hide();
	}
	
	//advanced view switch set cookie and toggle advanced columns
	$('#advancedview').switchButton({
		labels_placement: 'left',
		on_label: 'Advanced View',
  		off_label: 'Basic View',
  		checked: ($.cookie('ipmi_sensor_mode') == 'advanced')
	})
	.change(function () {
		$('.advanced').toggle('slow');
		$.cookie('ipmi_sensor_mode', $('#advancedview')[0].checked ? 'advanced' : 'basic', { expires: 3650 });
	});

	//event backup switch set cookie and toggle backup setting
	$('#event-backup').switchButton({
		labels_placement: 'left',
		on_label: 'Save on Clear',
  		off_label: 'Save on Clear',
  		checked: ($.cookie('ipmi_event_backup') == 'post-clear')
	})
	.change(function () {
		$.cookie('ipmi_event_backup', $('#event-backup')[0].checked ? 'post-clear' : 'clear', { expires: 3650 });
	});

	$('#tblSensor').tablesorter({
		sortList: [[2,0]],
		widgets: ['saveSort', 'filter', 'stickyHeaders'],
		widgetOptions: {
			stickyHeaders_filteredToTop: true,
			filter_hideEmpty : true,
			filter_liveSearch : true,
			filter_saveFilters : true,
			filter_reset : '.reset-sensors',
			filter_functions: {
				'.filter-ip' : true,
				'.filter-type' : true,
				'.filter-reading' : {
					'nominal'		: function(e, n, f, i, $r, c, data) {
						return (data.$row.find('td.reading font').prop('color') == 'green'); },
					'warning'		: function(e, n, f, i, $r, c, data) {
						return (data.$row.find('td.reading font').prop('color') == 'orange'); },
					'critical'		: function(e, n, f, i, $r, c, data) {
						return (data.$row.find('td.reading font').prop('color') == 'red'); }
				}
			}
		}
	});

	$('#tblEvent').tablesorter({
		sortList: [[2,1]],
		widgets: ['saveSort', 'filter', 'stickyHeaders'],
		widgetOptions: {
			stickyHeaders_filteredToTop: true,
			filter_hideEmpty : true,
			filter_liveSearch : true,
			filter_saveFilters : true,
			filter_reset : '.reset-events',
			filter_functions: {
				'.filter-ip' : true,
				'.filter-type' : true,
				'.filter-time' : {
					'3 days'		: function(e, n, f, i, $r, c, data) {
						return ($.now() - n <= 259200000); }, //3*24*60*60 3 days
					'1 week'		: function(e, n, f, i, $r, c, data) {
						return ($.now() - n <= 604800000); }, //7*24*60*60 1 week
					'2 weeks'	: function(e, n, f, i, $r, c, data) {
						return ($.now() - n <= 1209600000); }, //14*24*60*60 2 weeks
					'1 month'	: function(e, n, f, i, $r, c, data) {
						return ($.now() - n <= 2592000000); }, //30*24*60*60 1 month
					'6 months'	: function(e, n, f, i, $r, c, data) {
						return ($.now() - n <= 15724800000); }, //26*7*24*60*60 6 months
					'1 year'		: function(e, n, f, i, $r, c, data) {
						return ($.now() - n <= 31449600000); } //52*7*24*60*60 1 year
				}
			}
		}
	})
	.tablesorterPager({
		container: $('.pager'),
		fixedHeight: false,
		size: 20
	});

	// add network class to ip address filter row cells
	$('#tblSensor tr.tablesorter-filter-row').children("td:nth-child(2)").addClass('network');
	$('#tblEvent tr.tablesorter-filter-row').children("td:nth-child(2)").addClass('network');

	// add advanced class to select tablesorter filter row cells
	var tdAdv = [3,5, 6, 7, 10, 11, 12];
	$.each(tdAdv , function (index, value) {
		$('#tblSensor tr.tablesorter-filter-row').children("td:nth-child("+value+")").addClass('advanced');
	});

	sensorArray(false);
	eventArray();

	sensorRefresh();

});

//sensor refresh
function sensorRefresh() {
  sensorArray(true);
   setTimeout(sensorRefresh, 20000);
};

//load ipmi sensor table
function sensorArray(Refresh){
	var Host;
  	$.getJSON('/plugins/ipmi/include/ipmi_sensors.php', function(sensors) {
  		$.each(sensors, function (i, sensor) {
  			if (sensor.State != 'N/A') {
  				var Reading = parseFloat(sensor.Reading);
  				var LowerNR = parseFloat(sensor.LowerNR);
  				var LowerC = parseFloat(sensor.LowerC);
  				var LowerNC = parseFloat(sensor.LowerNC);
  				var UpperNC = parseFloat(sensor.UpperNC);
  				var UpperC = parseFloat(sensor.UpperC);
  				var UpperNR = parseFloat(sensor.UpperNR);
  				var Color = 'green';

  				if (sensor.Type == 'Voltage'){

  					// if voltage is less than lower non-critical
  					// or voltage is greater than upper non-critical show critical
  					if (Reading < LowerNC && Reading > UpperNC)
  						Color = 'orange';

  					// if voltage is less than lower critical
  					// or voltage is greater than upper critical show non-recoverable
  					if (Reading < LowerC || Reading > UpperC)
  						Color = 'red';

  				} else if (sensor.Type == 'Fan'){
 
  					// if Fan RPMs are less than lower non-critical
  					if (Reading < LowerNC || Reading < LowerC || Reading < LowerNR)
  						Color = "red";

  				} else if (sensor.Type == 'Temperature'){

  					// if Temperature is greater than upper non-critical
  					if (Reading > UpperNC || Reading > UpperC || Reading > UpperNR)
  						Color = 'red';
  				}
   				
  				if (Refresh) {
					$("#"+i+" td.reading").html("<font color='"+ Color + "'>"+Reading+"</font>");
				} else {
					Host = (typeof sensor.IP == 'undefined') ? '' : sensor.IP;
					$('#tblSensor tbody')
					.append("<tr id='"+i+"'>"+
					"<td title='"+sensor.State+"'><img src='/plugins/ipmi/images/green-on.png'/></td>"+ //state
					"<td class='network'>"+Host+"</td>"+ // sensor host ip address
					"<td class='advanced'>"+sensor.ID+"</td>"+ // sensor id
					"<td>"+sensor.Name+"</td>"+ //sensor name
   				"<td class='advanced'>"+ sensor.LowerNR +"</td>"+
					"<td class='advanced'>"+ sensor.LowerC +"</td>"+
					"<td class='advanced'>"+ sensor.LowerNC +"</td>"+
					"<td class='reading'>"+ "<font color='"+ Color + "'>"+ Reading +"</font></td>"+ //sensor reading
					"<td>"+sensor.Units+"</td>"+ //sensor units
					"<td class='advanced'>"+ sensor.UpperNC +"</td>"+
					"<td class='advanced'>"+ sensor.UpperC +"</td>"+
					"<td class='advanced'>"+ sensor.UpperNR +"</td>"+
					"</tr>");
				}
			}
  		});
		if (!Refresh) {
			if(Host === '')
				$('.network').hide();
			else
				$('.network').show();

			if ($('#advancedview')[0].checked)
				$('.advanced').show();
			else
				$('.advanced').hide();
			
			// restore filters
			var lastSearch = $('#tblSensor')[0].config.lastSearch;
			$('#tblSensor').trigger('update');
			$('#tblSensor').trigger('search', [lastSearch]);
		}
 	});
};

//load ipmi event table
function eventArray(){
	var Host;
	$('#tblEvent tbody').html("<tr><td colspan='6'><br><i class='fa fa-spinner fa-spin icon'></i><em>Please wait, retrieving event information ...</em></td><tr>");
  	$.getJSON('/plugins/ipmi/include/ipmi_events.php', function(events) {
  		$('#tblEvent tbody').empty();
		$.each(events, function (i, event) {
   		var State = (event.State == 'Asserted') ? 'red' : 'green';
   		Host = (typeof event.IP == 'undefined') ? '' : event.IP;
 			$('#tblEvent tbody')
 			.append("<tr id='"+i+"'>"+
			"<td title='"+ event.State +"'><img src='/plugins/ipmi/images/"+ State +"-on.png'/></td>"+ //state
			"<td class='network'>"+ Host +"</td>"+ //event host ip address
			"<td>"+ event.ID +"</td>"+ //event id
			"<td>"+ event.DATE +" "+event.Time+"</td>"+ //time stamp
			"<td>"+ event.Name +"</td>"+ //sensor name
			"<td>"+ event.Type +"</td>"+ //event type
			"<td>"+ event.Event +"</td>"+ //event description
			"<td><a class='delete'><i class='fa fa-trash' title='delete'></i></a></td>"+ //delete icon
			"</tr>");
  		});
 
		if(Host === '')
			$('.network').hide();
		else
			$('.network').show();

		$('.delete').click(function () {
			Delete($(this).parent().parent().attr('id'));
		});

		//if (event.IP )
		var lastSearch = $("#tblEvent")[0].config.lastSearch;
		$('#tblEvent').trigger('update'); //update table for tablesorter
		$("#tblEvent").trigger("search", [lastSearch]);
			
		$('#allEvents').click(function() {
 				Delete($.cookie('ipmi_event_backup'));
		});
 	});
}

function Delete(ID) {
	var EventDelete = '/plugins/ipmi/include/ipmi_event_delete.php';
	if (ID == 'clear'|| ID == 'post-clear'){
		var Message = (ID == 'clear') ? 'permanently' : 'backup then'; 
		swal({
			title: 'Are you sure?', 
			text: 'You want to '+Message+' remove all events!?', 
			type: 'warning',
			showCancelButton: true,
			closeOnConfirm: true,
		}, function() {
		$.get(EventDelete, {event: ID}, function() {
			$('#tblEvent tbody').empty(); // empty table
			}
		);
    });
	} else {
		var trID = $('#'+ID);
		$.get(EventDelete, {event: ID},
			function() {
				//remove table row
				trID
				.children('td')
				.animate({ padding: 0 })
				.wrapInner('<div />')
				.children()
				.slideUp(function() {
					trID.remove();
					$('#tblEvents').trigger('update');
				});
		});
	}
}