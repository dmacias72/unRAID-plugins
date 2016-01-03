$(function(){
	//setup tablesorter
	$('#tblData').tablesorter({headers:{0:{sorter:false},2:{sorter:'ipAddress'},3:{sorter:'MAC'},4:{sorter:false}}});
	$('#tblScan').tablesorter({headers:{0:{sorter:false},2:{sorter:'ipAddress'},3:{sorter:'MAC'},4:{sorter:false}}});
	$('.powercfg').hide();
	//Add, Save, Edit and Delete functions
	$(".btnEdit").bind("click", Edit);
	$(".btnDelete").bind("click", Delete);
	$("#btnNew").bind("click", New);
	$(".btnWake").bind("click", Wake);
	$(".btnAdd").bind("click", Add);
	
	//input masks
	$('.ip_address').mask('0ZZ.0ZZ.0ZZ.0ZZ', {translation:  {'Z': {pattern: /[0-9]/, optional: true}}});
	$('.mac_address').mask('AA:AA:AA:AA:AA:AA', {translation:  {'A': {pattern: /[A-Fa-f0-9]/, optional: false}}});

	//load table from xml
	parseXML();
});

function Add(){
	var par = $(this).parent();
	var hostName = par.children("td:nth-child(2)").html();
	var ipAddress = par.children("td:nth-child(3)").html();
	var macAddress = par.children("td:nth-child(4)").html();

	$("#tblData tbody").append(
	"<tr>"+
	"<td style='cursor:pointer' class='btnWake' title='wake'><img src='/plugins/dynamix/images/green-on.png'/></td>"+
	"<td style='cursor:pointer' class='btnEdit' title='edit'>"+hostName+"</td>"+
	"<td style='cursor:pointer' class='btnEdit' title='edit'>"+ipAddress+"</td>"+
	"<td style='cursor:pointer' class='btnEdit' title='edit'>"+macAddress+"</td>"+
	"<td style='cursor:pointer' class='btnEdit powercfg' title='edit'></td>"+
	"<td style='cursor:pointer' class='btnEdit powercfg' title='edit'></td>"+
	"<td style='cursor:pointer' class='btnEdit powercfg' title='edit'></td>"+
	"<td><img src='/plugins/dynamix/images/close.png' width='24' height='24' class='btnDelete' title='delete'/></td>"+
	"</tr>");
	
	$(".btnWake").unbind("click", Wake).bind("click", Wake);
	$(".btnEdit").unbind("click", Edit).bind("click", Edit);
	$(".btnDelete").unbind("click", Delete).bind("click", Delete);
	Store();
};

function Edit(){
	var par       = $(this).parent();
	var tdName    = par.children("td:nth-child(2)");
	var tdIP      = par.children("td:nth-child(3)");
	var tdMAC     = par.children("td:nth-child(4)");
	var tdUser    = par.children("td:nth-child(5)");
	var tdPass    = par.children("td:nth-child(6)");
	var tdSystem  = par.children("td:nth-child(7)");
	var tdButtons = par.children("td:nth-child(8)");

	tdName.html("<input class='edit' title='name' type='text' id='txtName' style='width:100px' value='"+tdName.html()+"'/>");
	tdIP.html("<input class='ip_address edit' title='ip address' type='text' id='txtIP' style='width:85px' value='"+tdIP.html()+"'/>");
	tdMAC.html("<input class='mac_address edit' title='mac address' type='text' id='txtMAC' style='width:95px' value='"+tdMAC.html()+"'/>");
	tdUser.html("<input class='username edit' title='username' type='text' id='txtUser' style='width:95px' value='"+tdUser.html()+"'/>");
	tdPass.html("<input class='password edit' title='password' type='password' id='txtPass' style='width:95px' value='"+tdPass.html()+"'/>");
	tdSystem.html("<select></select><input class='system edit' title='operating system' type='text' id='txtSystem' style='width:95px' value='"+tdSystem.html()+"'/>");
	tdButtons.html("&nbsp;<img src='/plugins/wakeonlan/images/save.png' title='save' class='btnSave' style='cursor:pointer'/>");

	$(".btnSave").bind("click", Save);
	$(".btnEdit").unbind("click", Edit);
	$(".btnDelete").unbind("click", Delete);
	$(".btnWake").unbind("click", Wake);
	$("#btnNew").unbind("click", New);
	$(".btnAdd").unbind("click", Add);
	
};

function Delete(){
	var par = $(this).parent().parent();
	par.remove();
	Store();
}; 

function Cancel(){
	var par = $(this).parent().parent();
	par.remove();
	$(".btnEdit").bind("click", Edit);
	$(".btnDelete").bind("click", Delete);
};

function New(){
	$("#tblData tbody").append(
		"<tr>"+
		"<td style='cursor:pointer' class='btnWake' title='wake'><img src='/plugins/dynamix/images/green-on.png'/></td>"+
		"<td style='cursor:pointer' class='btnEdit' title='edit'><input style='width:100px' type='text'/></td>"+
		"<td style='cursor:pointer' class='btnEdit' title='edit'><input class='ip_address edit' style='width:85px' type='text'/></td>"+
		"<td style='cursor:pointer' class='btnEdit' title='edit'><input class='mac_address edit' style='width:95px' type='text'/></td>"+
		"<td style='cursor:pointer;display:none;' class='btnEdit powercfg' title='edit'><input class='username edit' style='width:85px' type='text'/></td>"+
		"<td style='cursor:pointer;display:none;' class='btnEdit powercfg' title='edit'><input class='password edit' style='width:85px' type='text'/></td>"+
		"<td style='cursor:pointer;display:none;' class='btnEdit powercfg' title='edit'><input class='system edit' style='width:85px' type='text'/></td>"+
		"<td>&nbsp;<img src='/plugins/wakeonlan/images/save.png' class='btnSave' style='cursor:pointer'>"+
		"&nbsp;<img src='/plugins/dynamix/images/close.png' width='24' height='24' class='btnCancel' title='cancel'/></td>"+
		"</tr>");
	
		$(".btnSave").bind("click", Save);		
		$(".btnEdit").unbind("click", Edit);
		$(".btnDelete").unbind("click", Delete);
		$(".btnCancel").bind("click", Cancel);
};

function parseScan(){
	$("#tblScan tbody").empty();
	$.ajax({
   	type: "GET",
   	url: "/plugins/wakeonlan/scan.xml",
   	dataType: "xml",
   	success: function(xml) {
			$(xml).find("host").each(function(){
				var hostName = "";
				var ipAddress = "";
				var macAddress = "";
				hostName = $(this).find("hostnames").find("hostname").attr("name");
				ipAddress = $(this).find("address").attr("addr");
				$(this).find("address").each(function() {
					if ($(this).attr("addrtype") == "mac") {
						macAddress = $(this).attr("addr");
						return macAddress;
					}
				});
				$("#tblScan tbody").append(
				"<tr>"+
				"<td style='cursor:pointer' class='btnAdd'><img src='/plugins/dynamix/images/maximise.png' width='24' height='24'/></td>"+
				"<td>"+hostName+"</td>"+
				"<td>"+ipAddress+"</td>"+
				"<td>"+macAddress+"</td>"+
				"<td></td>"+
				"</tr>");

			$(".btnAdd").unbind("click", Add).bind("click", Add);
			});
   	},
   	 cache: false,
       error : function() {}
	});
$("#tblScan").trigger("update");
};

function parseXML(){
  	$.ajax({
   	type: "GET",
   	url: "/boot/config/plugins/wakeonlan/wakeonlan.xml",
   	dataType: "xml",
   	success: function(xml) {
			$(xml).find("host").each(function(){
				var hostName = "unknown";
				var ipAddress = "";
				var macAddress = "";
				var userName = "";
				var passWord = "";
				var System = "";
				hostName = $(this).find("hostnames").find("hostname").attr("name");
				userName = $(this).find("hostnames").find("hostname").attr("username");
				passWord = $(this).find("hostnames").find("hostname").attr("password");
				System = $(this).find("hostnames").find("hostname").attr("system");
				ipAddress = $(this).find("address").attr("addr");
				$(this).find("address").each(function() {
					if ($(this).attr("addrtype") == "mac") {
						macAddress = $(this).attr("addr");
						return macAddress;
					}
				});
				$("#tblData tbody").append(
				"<tr>"+
				"<td style='cursor:pointer' class='btnWake' title='wake'><img src='/plugins/dynamix/images/green-blink.png'/></td>"+
				"<td style='cursor:pointer' class='btnEdit' title='edit'>"+hostName+"</td>"+
				"<td style='cursor:pointer' class='btnEdit' title='edit'>"+ipAddress+"</td>"+
				"<td style='cursor:pointer;text-transform:uppercase' class='btnEdit' title='edit'>"+macAddress+"</td>"+
				"<td style='cursor:pointer;display:none;' class='btnEdit powercfg' title='edit'>"+userName+"</td>"+
				"<td style='cursor:pointer;display:none;' class='btnEdit powercfg' title='edit'>"+passWord+"</td>"+
				"<td style='cursor:pointer;display:none;' class='btnEdit powercfg' title='edit'>"+System+"</td>"+
				"<td><img src='/plugins/dynamix/images/close.png' width='24' height='24' class='btnDelete' title='delete'/></td>"+
				"</tr>");
			$(".btnWake").unbind("click", Wake).bind("click", Wake);
			$(".btnEdit").unbind("click", Edit).bind("click", Edit);
			$(".btnDelete").unbind("click", Delete).bind("click", Delete);
			});
		ScanIP();
		$("#tblData").trigger("update");
   	},
       error : function() {}
	});
};

function Save(){
	var par = $(this).parent().parent();
	var tdName = par.children("td:nth-child(2)");
	var tdIP = par.children("td:nth-child(3)");
	var tdMAC = par.children("td:nth-child(4)");
	var tdUser = par.children("td:nth-child(5)");
	var tdPass = par.children("td:nth-child(6)");
	var tdSystem = par.children("td:nth-child(7)");
	var tdButtons = par.children("td:nth-child(8)");

	tdName.html(tdName.children("input[type=text]").val());
	tdIP.html(tdIP.children("input[type=text]").val());
	tdMAC.html(tdMAC.children("input[type=text]").val());
	tdUser.html(tdUser.children("input[type=text]").val());
	tdPass.html(btoa(tdPass.children("input[type=password]").val()));
	tdSystem.html(tdSystem.children("input[type=text]").val());
	tdButtons.html("<img src='/plugins/dynamix/images/close.png' width='24' height='24' class='btnDelete'/>");

	$(".btnWake").unbind("click", Wake).bind("click", Wake);
	$(".btnEdit").unbind("click", Edit).bind("click", Edit);
	$(".btnDelete").unbind("click", Delete).bind("click", Delete);
	$("#btnNew").bind("click", New);
	$(".btnAdd").bind("click", Add);

	ScanIP();
	Store();
};

function ScanIP() {
   $('#tblData tbody tr').each(function(row, tr){
		var ipAddress = $(tr).children("td:nth-child(3)").html();
		
	  	$.ajax({
	     	type : "POST",
   	   url : "/plugins/wakeonlan/include/scan_ip.php",
      	data : {ip: ipAddress},
	      success: function(ipStatus) {
				$(tr).children("td:nth-child(1)").html("<img src='/plugins/dynamix/images/green-"+ipStatus+".png'/>");
 	      }
   	});
	});
};

function Store(){
	$("#tblScan").trigger("update");
	var tblXML = '<?xml version="1.0"?>'+
					'<?xml-stylesheet href="file:///usr/bin/../share/nmap/nmap.xsl" type="text/xsl"?>'+
					'<hosts>';
   $('#tblData tbody tr').each(function(row, tr){
		var hostName = $(tr).children("td:nth-child(2)").html();
		var ipAddress = $(tr).children("td:nth-child(3)").html();
		var macAddress = $(tr).children("td:nth-child(4)").html();
		var userName = $(tr).children("td:nth-child(5)").html();
		var passWord = $(tr).children("td:nth-child(6)").html();
		var System = $(tr).children("td:nth-child(7)").html();
		tblXML += '<host>'+
					'<address addr="'+ipAddress+'" addrtype="ipv4"/>'+
					'<address addr="'+macAddress+'" addrtype="mac"/>'+
					'<hostnames>'+
					'<hostname name="'+hostName+'" username="'+userName+'" password="'+passWord+'" system="'+System+'"/>'+
					'</hostnames>'+
					'</host>';
	});
	tblXML += '</hosts>';

	$.ajax({
		type: 'POST',
	   url: "/plugins/wakeonlan/include/save.php",
  	  	dataType: 'xml',
  	  	data: {data: tblXML},
  	  	error: function() {
  	   	alert("Unknown error. Data could not be written to the file.");
  	  	},
  	  	success: function () {
  	  	}
	});
};

function Scan(ipExclude){
	$("#tblScan tbody").html('<td></td><td><img src="/plugins/dynamix/images/loading.gif"></td><td><img src="/plugins/dynamix/images/loading.gif"></td><td><img src="/plugins/dynamix/images/loading.gif"></td>');
	$('#countdown').html('<font class="green">Scanning...</font>');
  	$.ajax({
     	type : "POST",
      url : "/plugins/wakeonlan/include/scan.php",
      data : {ip: ipExclude},
      complete: function() {
   	   parseScan();
   	   $('#countdown').html("");
       },
      cache: false
   });
};

function Wake(){
	var par = $(this).parent()
	var tdStatus = par.children("td:nth-child(1)");
	var macAddress = par.children("td:nth-child(4)").html();
	tdStatus.html("<img src='/plugins/dynamix/images/loading.gif'>");
  	$.ajax({
     	type : "POST",
      url : "/plugins/wakeonlan/include/wake.php",
      data : {mac: macAddress, ifname: ifName},
      success: function() {
          setTimeout(ScanIP, 9999);
       },
       error : function() {
       }
   });
};
