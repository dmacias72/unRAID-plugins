<?PHP
/* Copyright 2015, Bergware International.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * Modified for Speedtest by dmacias, 2016
 */
?>
<?
require_once '/usr/local/emhttp/webGui/include/Wrappers.php';

if ($_POST['mode']>0) {
  $hour = isset($_POST['hour']) ? htmlspecialchars($_POST['hour']) : '*';
  $min  = isset($_POST['min'])  ? intval($_POST['min'])  : '*';
  $dotm = isset($_POST['dotm']) ? intval($_POST['dotm']) : '*';
  $day  = isset($_POST['day'])  ? intval($_POST['day'])  : '*';
  $cron = "# Generated speedtest schedule:\n$min $hour $dotm * $day /usr/sbin/speedtest-xml &> /dev/null\n\n";
} else {
  $cron = "";
}
parse_cron_cfg('dynamix', 'speedtest', $cron);
?>