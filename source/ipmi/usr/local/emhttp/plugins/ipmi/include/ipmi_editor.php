<?
require_once '/usr/local/emhttp/webGui/include/Helpers.php';
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';

$config_file = "$plg_path/ipmi.config";
$config      = '';
if(($ipmi_mod == 1) || ($netsvc == 'enable')) {
	// Only get config if the current one is 1 hour old or more
	if(!is_file($config_file) || (filesize($config_file) == 0 )) {
		$cmd = "ipmi-config --filename=$config_file --checkout $netopts";
		shell_exec($cmd);
	}
	$config = file_get_contents($config_file);
}
?>
<link rel="stylesheet" href="/plugins/ipmi/js/CodeMirror/lib/codemirror.css">
<link rel="stylesheet" href="/plugins/ipmi/js/CodeMirror/addon/hint/show-hint.css">
<link type="text/css" rel="stylesheet" href="/webGui/styles/jquery.switchbutton.css">
<style type="text/css">
	.CodeMirror { border: 1px solid #eee; cursor: text; margin-top: 15px; margin-bottom: 10px; }
	.CodeMirror pre.CodeMirror-placeholder { color: #999; }
</style>

<blockquote class="inline_help">
	<p>IPMI Config Editor is used to get and set sensor configuration parameters, such as thresholds and sensor events.
	This configuration tool is for advanced IPMI users and generally not-required for IPMI to function.  
	Since many fields involve decimal numbers, precision/floating point inaccuracies may occur when configuring new thresholds. 
	The inaccuracies may not be apparent immediately.  It is recommend to verify their changes after configuring new thresholds. 
	Some sensor configuration may be stored in volatile memory, so you may wish to veryify that new configurations exist after system reboots.  
	If sensor configuration does not survive then check Load Config @ unRAID Start to load config when system reboots.</p>
</blockquote>

<form id="cfgform" method="POST">

<textarea id="editcfg" name="ipmicfg" placeholder="Copy &amp; Paste IPMI Configuration Here." autofocus><?= htmlspecialchars($config); ?></textarea>
<input type="hidden" name="commit" value="1" />
<dl>
	<dt><input type="button" value="Revert" id="btnRevert" /></dt>
	<dd><input type="button" value="Save" id="btnSubmit" />
	<input type="button" value="Cancel" id="btnCancel" />
	<span><i class="fa fa-warning icon warning"></i> Edit Carefully!  If edits do not survive reboot toggle Load Config Switch</span>
	</dd>
</form>
</dl>

<blockquote class="inline_help">
	<p>The Revert button resets the stored IPMI config file to the config loaded on the BMC.  The Save button save IPMI configuration changes to the BMC.  
	</p>
</blockquote>

<script src="/webGui/javascript/jquery.switchbutton.js"></script>
<script src="/plugins/ipmi/js/CodeMirror/lib/codemirror.js"></script>
<script src="/plugins/ipmi/js/CodeMirror/addon/display/placeholder.js"></script>
<script src="/plugins/ipmi/js/CodeMirror/addon/hint/show-hint.js"></script>
<script src="/plugins/ipmi/js/CodeMirror/mode/properties/properties.js"></script>
<script src="/plugins/ipmi/js/CodeMirror/addon/hint/anyword-hint.js"></script>
<script>
$(function() {
	$('#btnCancel').click(function() {
		location = '/Settings/IPMI';
	});

	var editor = CodeMirror.fromTextArea(document.getElementById("editcfg"), {
		mode: "properties",
		lineNumbers: true,
		gutters: ["CodeMirror-linenumbers"],
		extraKeys: {
			"Ctrl-Space": "autocomplete"
		},
		hintOptions: {}
	});

	setTimeout(function() {
		editor.refresh();
	}, 1);

	// save config and commit to bmc
	$('#btnSubmit').click(function () {
		editor.save();
		$.post('/plugins/ipmi/include/ipmi_config.php', $('#cfgform').serializeArray(),function (data) {
			var Title = 'IPMI Configuration';

			if(data.success)
				swal({title:Title,text:'saved',type:'success',closeOnConfirm: true,},function() {
					location = '/Settings/IPMI';
				});

			if(data.error)
				swal({title:Title,text:data.error,type:'error'});

		}, 'json');
	});

	// revert saved config file to bmc config
	$('#btnRevert').click(function () {
		$.post('/plugins/ipmi/include/ipmi_config.php', {ipmicfg:null}, function (data) {
			var Title = 'IPMI Configuration';
	
			if(data.success)
				swal({title:Title,text:'reloaded from bmc',type:'success',closeOnConfirm: true,},function() {
					location.reload(true);
				});

			if(data.error)
				swal({title:Title,text:data.error,type:'error'});

		}, 'json');
	});

});
</script>