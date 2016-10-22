$(function(){
    //setup tablesorter
    $('#tblData').tablesorter({
        headers:{0:{sorter:false},2:{sorter:'ipv4Address'},3:{sorter:'MAC'},4:{sorter:false}},
        widgets: ['stickyHeaders', 'saveSort'],
        widgetOptions: {
            stickyHeaders_filteredToTop: true
         }
    });
    $('#tblScan').tablesorter({
        headers:{1:{sorter:'ipv4Address'},2:{sorter:'MAC'}},
        widgets: ['stickyHeaders', 'saveSort'],
        widgetOptions: {
            stickyHeaders_filteredToTop: true
         }
        });

    //Add all click functions
    $('.edit').one('click', Edit);
    $('.delete').on('click', Delete);
    $('#btnNew').one('click', New);
    $('#btnScan').on('click', Scan);
    $('.wake').on('click', Wake);
    $('#allMac').on('click', DeleteAll);
    $('#btnClear').on('click',function() {
        $.getJSON('/plugins/wakeonlan/include/delete_scan.php', function() {
                $('#tblScan tbody').empty();
                window.parent.scrollTo(0,0);
          }
        );
    });

    //ESC key reset
/*	$(document).keyup(function(e) {
        if (e.keyCode == 27) { // escape key maps to keycode `27`
            Refresh();
        }
    });
*/

    //input masks
    $('.ip-address').mask('0ZZ.0ZZ.0ZZ.0ZZ', {translation:  {'Z': {pattern: /[0-9]/, optional: true}}});
    $('.mac-address').mask('AA:AA:AA:AA:AA:AA', {translation:  {'A': {pattern: /[A-Fa-f0-9]/, optional: false}}});

    //load table from xml
    parseXML();
    parseScan();
});

function Add(){
    var tr =$(this);
    var hostName   = tr.children('td:nth-child(1)').html();
    var ipAddress  = tr.children('td:nth-child(2)').html();
    var macAddress = tr.children('td:nth-child(3)').html();
    $.post('/plugins/wakeonlan/include/add_node.php',{name:hostName, ip:ipAddress, mac:macAddress},function () {
            slideRow(tr);
            addRow(hostName, ipAddress, macAddress);
            $('#tblData').trigger('update');
        }
    );
}

function addRow(hostName, ipAddress, macAddress) {
    $('#tblData tbody').append('<tr>'+
    '<td class="wake" title="wake"><img src="/plugins/dynamix/images/green-on.png"/></td>'+
    '<td class="edit hostname" title="edit">'+hostName+'</td>'+
    '<td class="edit ip-address" title="edit">'+ipAddress+'</td>'+
    '<td class="edit mac-uppercase mac-address" title="mac address">'+macAddress+'</td>'+
    '<td><a class="delete" title="delete"><i class="fa fa-trash"></i></a></td></tr>');

    $('.wake').off('click', Wake).on('click', Wake);
    $('.edit').off('click', Edit).on('click', Edit);
    $('.delete').off('click', Delete).on('click', Delete);
}

function Cancel(par){
    par = par.parent().parent();
    slideRow(par);
    $('.edit').off('click', Edit).one('click', Edit);
    $('.delete').off('click', Delete).on('click', Delete);;
    $('#btnNew').off('click', New).one('click', New);
}

function Delete() {
    var par = $(this).parent().parent();
    mac = par.children('td:nth-child(4)').html();
    $.post('/plugins/wakeonlan/include/delete_node.php', {mac: mac}, function (data) {
            slideRow(par);
        }
    );
}

function DeleteAll() {
    swal({
        title: 'Are you sure?',
        text: 'You want to remove all!?',
        type: 'warning',
        showCancelButton: true,
        closeOnConfirm: true,
        }, function() {
            $.post('/plugins/wakeonlan/include/delete_node.php', {mac: 'all'}, function (data) {
                $('#tblData tbody').empty(); // empty table
            });
        }
    );
}

function Edit(){
    var par       = $(this).parent();
    var tdName    = par.children('td:nth-child(2)');
    var tdIP      = par.children('td:nth-child(3)');
    var tdMAC     = par.children('td:nth-child(4)');
    var tdButtons = par.children('td:nth-child(5)');

    tdName.html('<input class="edit-name edit-text" title="name" type="text" '+
        'id="txtName" value="'+tdName.html()+'"/>');
    tdIP.html('<input class="ip-address edit-ip edit-text" title="ip address" '+
        'type="text" id="txtIP" value="'+tdIP.html()+'"/>');
    tdMAC.html('<input class="mac-address edit-mac edit-text" title="mac address" '+
        'type="text" id="txtMAC" value="'+tdMAC.html()+'"data-mac="'+tdMAC.html()+
        '"/><i id="edit-error" class="fa fa-exclamation red" style="display:none;"/>');
    tdButtons.html('<a id="save" title="save"> <i class="fa fa-floppy-o"/> </a>&nbsp;'+
        '<a id="cancel" title="cancel"> <i class="fa fa-close"/> </a>');

    //set focus to input in clicked cell
    if ($(this).hasClass('mac-address'))
        $('input.edit-mac').focus();
    if ($(this).hasClass('ip-address'))
        $('input.edit-ip').focus();
    if ($(this).hasClass('hostname'))
        $('input.edit-name').focus();

    $('#cancel').one('click', Refresh);
    $('.edit').off('click', Edit);
    $('.delete').off('click', Delete);
    $('.wake').off('click', Wake);
    $('#btnNew').off('click', New);
    $('#save').one('click', function() {
        Save($(this), true);
        });
    $('.edit-text').keyup(function(e) {
        if (e.keyCode == 13) { //'enter' key = keycode `27`
        Save($(this), true);
        }
        if (e.keyCode == 27) { //'escape' key = keycode `27`
            Refresh();
        }
    });

    //tdName.removeClass('edit').addClass('noedit');
    //tdIp.removeClass('edit').addClass('noedit');

};

function New(){
    $('#tblData tbody').append('<tr>'+
    '<td class="wake" title="wake"><img src="/plugins/dynamix/images/green-on.png"/></td>'+
    '<td class="edit" title="edit"><input class="edit-name edit-new" type="text"/></td>'+
    '<td class="edit" title="edit"><input class="ip-address edit-ip edit-new" type="text"/></td>'+
    '<td class="edit" title="edit"><input class="mac-address edit-mac edit-new" type="text"/>'+
    '<i id="edit-error" class="fa fa-exclamation red" style="display:none;"/></td>'+
    '<td><a id="save" title="save"><i class="fa fa-floppy-o"/> </a>&nbsp;'+
    '<a id="cancel" title="cancel"> <i class="fa fa-trash"/> </a></td></tr>');

    $('#tblData').trigger('update');

    $('input.edit-name').focus();

    $('.edit').off('click', Edit);
    $('.delete').off('click', Delete);
    $('#btnNew').off('click', New)
    $('#cancel').one('click', function() {
        Cancel($(this));
        });
    $('#save').one('click', function() {
        Save($(this), false);
        });
    $('.edit-new').keyup(function(e) {
        if (e.keyCode == 13) { //'enter' key = keycode `27`
            Save($(this), false);
        }
        if (e.keyCode == 27) { //'escape' key = keycode `27`
            Cancel($(this));
        }
    });
};

function parseScan(){
    $.get('/log/wakeonlan/scan.xml', function(xml) {
            $(xml).find('host').each(function(){
                var hostName;
                var ipAddress;
                var macAddress;
                hostName  = $(this).find('hostnames').find('hostname').attr('name');
                ipAddress = $(this).find('address').attr('addr');
                $(this).find('address').each(function() {
                    if ($(this).attr('addrtype') == 'mac') {
                        macAddress = $(this).attr('addr');
                        return macAddress;
                    }
                });

                // check if mac is already saved
                var macCount = 0;
                $('#tblData tbody tr').each(function(row, tr){
                   var tblMac = $(tr).children('td:nth-child(4)').html();
                    if (tblMac == macAddress)
                        macCount += 1;
                });

                // if mac is not saved then add
                if (macCount == 0) {
                    $("#tblScan tbody").append('<tr class="addRow" title="click to add">'+
                    '<td>'+hostName+'</td>'+
                    '<td>'+ipAddress+'</td>'+
                    '<td>'+macAddress+'</td></tr>');

                    $('.addRow').off('click', Add).on('click', Add);
                }
            });

            $('#tblScan').trigger('update');

        }, 'xml'
    );
}

function parseXML(){
    $.get('/boot/config/plugins/wakeonlan/wakeonlan.xml', function(xml) {
            $(xml).find('host').each(function(){
                var hostName = 'unknown';
                var ipAddress
                var macAddress
                hostName = $(this).find('hostnames').find('hostname').attr('name');
                ipAddress = $(this).find('address').attr('addr');
                $(this).find('address').each(function() {
                    if ($(this).attr('addrtype') == 'mac') {
                        macAddress = $(this).attr('addr');
                        return macAddress;
                    }
                });
            addRow(hostName, ipAddress, macAddress);
            });
        $('#tblData').trigger('update');
        ScanIP();
    },'xml');
}

function Refresh() {
    document.location.reload(false);
}

function Save(par, edit){
    par            = par.parent().parent();
    var tdMAC      = par.children('td:nth-child(4)');
    var macAddress = tdMAC.children('input[type=text]').val();

    if (macAddress.length < 17) {
        $('input.edit-mac').focus();
        $('#edit-error').show();
    }else{
        var tdName    = par.children('td:nth-child(2)');
        var tdIP      = par.children('td:nth-child(3)');
        var tdButtons = par.children('td:nth-child(5)');

        var hostName  = tdName.children('input[type=text]').val();
        var ipAddress = tdIP.children('input[type=text]').val();
        var oldMAC    = tdMAC.children('input[type=text]').data('mac');

        tdName.html(tdName.children('input[type=text]').val());
        tdIP.html(tdIP.children('input[type=text]').val());
        tdMAC.html(tdMAC.children('input[type=text]').val());
        tdButtons.html('<a class="delete" title="delete"><i class="fa fa-trash"/></a>');
        $('#tblData').trigger('update');

        $('.wake').off('click', Wake).on('click', Wake);
        $('.edit').off('click', Edit).on('click', Edit);
        $('.delete').off('click', Delete).on('click', Delete);
        $('#btnNew').off('click', New).one('click', New);

        if (edit)
            $.post('/plugins/wakeonlan/include/edit_node.php',{name:hostName, ip:ipAddress, mac:macAddress, oldmac:oldMAC},function () {},'xml');
        else
            $.post('/plugins/wakeonlan/include/add_node.php',{name:hostName, ip:ipAddress, mac:macAddress},function () {},'xml');

    ScanIP();
    }
};

function Scan(){
    $('#tblScan tbody').html('<tr id="scanning"><td><img src="/plugins/dynamix/images/loading.gif"></td>'+
        '<td><img src="/plugins/dynamix/images/loading.gif"></td>'+
        '<td><img src="/plugins/dynamix/images/loading.gif"></td></tr>');
    $('#countdown').html('<font class="green">Scanning...</font>');
    $.getJSON('/plugins/wakeonlan/include/scan.php',{ip: excludeIP}, function(data) {
            parseScan();
            $('#countdown').empty();
            slideRow($('#scanning'));
        }
   );
};

function ScanIP() {
    $('#tblData tbody tr').each(function(row, tr){
        var ipAddress = $(tr).children('td:nth-child(3)').html();
        $.getJSON('/plugins/wakeonlan/include/scan_ip.php', {ip: ipAddress}, function(ipStatus) {
                $(tr).children('td:nth-child(1)').html('<img src="/plugins/dynamix/images/green-'+ipStatus+'.png"/>');
        });
    });
}

function slideRow(par) {
    par
    .children('td')
    .animate({ padding: 0 })
    .wrapInner('<div />')
    .children()
    .slideUp(function() { par.remove(); });
    $('#tblData').trigger('update')
}

function Wake(){
    var par = $(this).parent()
    var tdStatus = par.children('td:nth-child(1)');
    var macAddress = par.children('td:nth-child(4)').html();
    tdStatus.html('<img src="/plugins/dynamix/images/loading.gif">');
    $.post('/plugins/wakeonlan/include/wake.php', {mac: macAddress, ifname: ifName}, function() {
          setTimeout(ScanIP, 9999);
    });
}
