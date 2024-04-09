<?php
$table_data = '';
require 'serverside.php';
$table_data->get('vsproveedores','ruc',array('ruc','razon','documentos','total','retencion','abonado','saldo'));
?>