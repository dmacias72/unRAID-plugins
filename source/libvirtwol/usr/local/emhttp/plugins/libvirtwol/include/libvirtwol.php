<?
$config_file        = '/boot/config/domain.cfg';
$libvirtwol_cfg     = parse_ini_file($config_file);
$libvirtwol_service = isset($libvirtwol_cfg['WOL']) ? htmlspecialchars($libvirtwol_cfg['WOL']) : 'disable';
$libvirtwol_running = (intval(trim(shell_exec( "ps ax | grep 'libvirtwol.py' | grep -v grep &>/dev/null && echo 1 || echo 0 2> /dev/null" ))) === 1);
$status_running     = '<span class="green">Running</span>';
$status_stopped     = '<span class="orange">Stopped</span>';
$libvirtwol_status  = ($libvirtwol_running) ? $status_running : $status_stopped;
?>
<div class="advanced">
<div id="title" style="white-space:normal;"><span><img src="/plugins/libvirtwol/icons/wakeonlan.png" class="icon">Libvirt wake on lan</span>
<span class="status"> Status: <?=$libvirtwol_status;?></span></div>
<div id="wolform"></div>
<div id="wolinput">
<input type="hidden" name="#file" value="/boot/config/domain.cfg" />
<input type="hidden" id="wolcommand" name="#command" value="" />
<dl>
    <dt>Enable Wake On Lan:</dt>
    <dd>
        <select id="WOL" name="WOL" size="1">
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
    <dd><input id="btnApply" type="button" value="Apply"><input type="button" value="Done" onClick="done()"></dd>
</dl>
</div>
</div>
<script>
$(function(){
    // dynamix plugin update api
    <?if (function_exists('plugin_update_available') && $version = plugin_update_available('libvirtwol')):?>
        showNotice('Wake on Lan <?=htmlspecialchars($version);?> available. <a>Update</a>','libvirtwol');
        $('#user-notice a').on('click', function () {
            $('#user-notice').empty();
        });
    <?endif;?>

    checkRUNNING();
    $('#WOL').on('change', checkRUNNING);
    $('#btnApply').on('click', verifyDATA);
    $('#wolform').html('<form id="wolsettings" name="wolsettings" method="POST" action="/update.php" target="progressFrame"></form>');
    $('#wolinput').appendTo('#wolsettings');
});

function checkRUNNING() {
    if ($('#WOL').val() === 'enable')
        $('#wolcommand').val('/usr/local/emhttp/plugins/libvirtwol/scripts/start');
    else {
        $('#wolcommand').val('/usr/local/emhttp/plugins/libvirtwol/scripts/stop');
        $('#btnApply').prop('disabled', false);
    }

    if ("<?=$libvirtwol_running;?>" == true)
        $('#btnApply').disabled = 'disabled';
    else
        $('#btnApply').prop('disabled', false);
}

function verifyDATA() {
        $('#WOL').val( $('#WOL').val().replace(/ /g,"_") );
        $('#wolsettings').submit();
}
</script>