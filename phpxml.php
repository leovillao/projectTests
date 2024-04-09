<?php
$xml = simplexml_load_file('xml/sinfirma/'.$_GET['file']);
echo $xml->infoTributaria->claveAcceso;