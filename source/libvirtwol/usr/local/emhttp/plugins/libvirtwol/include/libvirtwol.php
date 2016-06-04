<?
$config_file = "/boot/config/domain.cfg";
$libvirtwol_cfg = parse_ini_file($config_file);
$libvirtwol_service = isset($libvirtwol_cfg['WOL']) ? $libvirtwol_cfg['WOL'] 	: "disable";
$libvirtwol_running = trim(shell_exec( "ps ax | grep 'libvirtwol.py' | grep -v grep &>/dev/null && echo 1 || echo 0 2> /dev/null" ));
$status_running = "<span class='green'>Running</span>";
$status_stopped = "<span class='orange'>Stopped</span>";
$libvirtwol_status = ($libvirtwol_running) ? $status_running : $status_stopped;
?>
<div class="advanced">
<div id="title" style="white-space:normal;"><span><img src="/plugins/libvirtwol/icons/wakeonlan.png" class="icon">Libvirt wake on lan</span>
<span class="status"> Status: <?=$libvirtwol_status;?></span></div>
<form name="libvirtwol_settings" method="POST" action="/update.php" target="progressFrame">
<input type="hidden" name="#file" value="/boot/config/domain.cfg" />
<input type="hidden" id="command" name="#command" value="" />
<dl>
	<dt>Enable Wake On Lan:</dt>
	<dd>
		<select id="WOL" name="WOL" size="1" onChange="checkRUNNING(this.form);">
	  		<?=mk_option($libvirtwol_service, "disable", "No");?>
  			<?=mk_option($libvirtwol_service, "enable", "Yes");?>
		</select>
	</dd>
</dl>
<blockquote class="inline_help">
	<p>Enable wake on lan for virtual machines.  Allows you to start a virtual machine by sending a WOL packet with the MAC address of the virtual machine to the unRAID server</p>
	<p> or broadcast the packet on the same network. It listens on your VM bridge for UDP 7 or 9 and ether proto 0x0842.</p>
</blockquote>
<dl>
	<dt>&nbsp;</dt>
	<dd><input id="btnApply" type="submit" value="Apply" onClick="verifyDATA(this.form)"><input type="button" value="Done" onClick="done()"></dd>
</dl>
</form>
</div>
<script type="text/javascript">
$(function(){
	<?if (function_exists('plugin_update_available') && $version = plugin_update_available('libvirtwol')):?>
		showNotice('Wake On Lan <?=$version?> is available. <a>Download Now</a>','libvirtwol');
	<?endif;?>
	checkRUNNING(document.libvirtwol_settings);
});

function checkRUNNING(form) {
	if ("<?=$libvirtwol_running;?>" == "yes")
		form.btnApply.disabled = "disabled";	
	if (form.WOL.value == "enable")
		form.command.value = "/usr/local/emhttp/plugins/libvirtwol/scripts/start";
	else {
		form.command.value = "/usr/local/emhttp/plugins/libvirtwol/scripts/stop";
	 	form.btnApply.disabled = (form.WOL.value == "enable");	
	}
};

function verifyDATA(form) {
		form.WOL.value = form.WOL.value.replace(/ /g,"_");
};
</script>