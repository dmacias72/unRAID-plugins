<?
require_once '/usr/local/emhttp/webGui/include/Helpers.php';
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';
//ipmi-sensors-config --filename=/boot/config/plugins/ipmi/ipmi.config --checkout
//ipmi-sensors-config --filename=/boot/config/plugins/ipmi/ipmi.config --commit
$config_file = "$plg_path/ipmi.config";
$config      = '';
if(($ipmi_mod) || ($netsvc == 'enable')) {
	if(!is_file($config_file) || (filesize($config_file) == 0 ) || (filemtime($config_file) < (time() - 3600))) {
		$cmd = "ipmi-sensors-config --filename=$config_file --checkout $netopts";
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
	It sensor configuration does not survive then check Load Config @ unRAID Start to load config when system reboots.</p>
</blockquote>

<form id="cfgform" method="POST">

<textarea id="editcfg" name="ipmicfg" placeholder="Copy &amp; Paste IPMI Configuration Here." autofocus><?= htmlspecialchars($config); ?></textarea>
<input type="hidden" name="commit" value="1" />
<dl>
	<dt>&nbsp;</dt>
	<dd><input type="button" value="Save" id="btnSubmit" />
	<input type="button" value="Cancel" id="btnCancel" />
	<span><i class="fa fa-warning icon warning"></i> Edit Carefully!  If edits do not survive reboot toggle Load Config Switch</span>
	</dd>
</form>
</dl>
<script src="/webGui/javascript/jquery.switchbutton.js"></script>
<script src="/plugins/ipmi/js/CodeMirror/lib/codemirror.js"></script>
<script src="/plugins/ipmi/js/CodeMirror/addon/display/placeholder.js"></script>
<script src="/plugins/ipmi/js/CodeMirror/addon/hint/show-hint.js"></script>
<script src="/plugins/ipmi/js/CodeMirror/mode/properties/properties.js"></script>
<script src="/plugins/ipmi/js/CodeMirror/addon/hint/anyword-hint.js"></script>
<script>
$(function() {
	$('.tabs')
		.append("<span id='adv-switch' class='status' style='margin-top:28px;'><input type='checkbox' id='autoload'></span>");

	$('#btnCancel').click(function() {
		location = '/Tools/IPMITools';
	});

	//advanced view switch set cookie and toggle advanced columns
	$('#autoload').switchButton({
		labels_placement: 'left',
		on_label: 'Load Config @ unRAID Start',
		off_label: 'Load Config @ unRAID Start',
		checked: ($.cookie('ipmi_config') == 'yes')
	})
	.change(function () {
		$('.advanced').toggle('slow');
		$.cookie('ipmi_config', $('#autoload')[0].checked ? 'yes' : 'no', { expires: 3650 });
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

	$('#btnSubmit').click(function () {
		editor.save();
		$.post('/plugins/ipmi/include/ipmi_config_save.php', $('#cfgform').serializeArray(), function (data) {
			if(data.success){
				location = '/Tools/IPMITools';
			}
			if(data.error){
				swal({title:"IPMI configuration error",text:data.error,type:"error"});
			}
		}, "json");
	});
});
</script>