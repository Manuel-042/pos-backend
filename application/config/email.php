<?php 
defined('BASEPATH') OR exit('No direct script access allowed');


$config['protocol'] = 'sendmail';
$config['mailpath'] = '/usr/sbin/sendmail';
$config['charset'] = 'iso-8859-1';
$config['wordwrap'] = TRUE;
$config['useragent'] = 'CodeIgniter';
$config['wrapchars'] = 76;
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['validate'] = FALSE;
$config['priority'] = 3;
$config['crlf'] = "\r\n";
$config['newline'] = "\r\n";
$config['bcc_batch_mode'] = FALSE;
$config['bcc_batch_size'] = 200;


// $config['protocol']    = 'smtp';
// $config['smtp_host']    = 'smtp.gmail.com';
// $config['smtp_port']    = 25;
// $config['smtp_user']    = '***********';
// $config['smtp_pass']    = '***********';
// $config['charset']    = 'utf-8';
// $config['priority'] = 3;
// $config['newline']    = "\r\n";
// $config['newline'] = "\r\n";
// $config['mailtype'] =  'html';
// $config['validation'] = FALSE;
// $config['wordwrap'] = TRUE;