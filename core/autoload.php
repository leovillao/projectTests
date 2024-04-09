<?php
include "controller/Core.php";
include "controller/View.php";
include "controller/Module.php";
include "controller/Database.php";
include "controller/Executor.php";
//# include "controller/Session.php"; [remplazada]

include "controller/forms/lbForm.php";
include "controller/forms/lbInputText.php";
include "controller/forms/lbInputPassword.php";
include "controller/forms/lbValidator.php";

// 10 octubre 2014
include "controller/Model.php";
include "controller/Bootload.php";
include "controller/Action.php";

// 13 octubre 2014
include "controller/Request.php";


// 14 octubre 2014
include "controller/Get.php";
include "controller/Post.php";
include "controller/Cookie.php";
include "controller/Session.php";
include "controller/Lb.php";

// 26 diciembre 2014
include "controller/Form.php";
include 'controller/class.upload.php';

// 01 Julio 2020
require 'controller/Fpdf/fpdf.php';
require 'controller/Fpdf/code128.php';

//require '../../core/controller/Fpdf/fpdf.php';
//require '../../core/controller/Fpdf/code128.php';

require_once 'res/mailer/class.phpmailer.php';
require_once 'res/mailer/class.pop3.php';
require_once 'res/mailer/class.smtp.php';

// 27 Julio del 2020


// 27 Julio 2020
/*include "controller/Esquemas.php";
include "controller/RET_MakeXML.php";
include "controller/SendFileXml.php";*/

include "controller/Esquemas.php";
include "controller/RET_MakeXML.php";
include "controller/FACT_MakeXML.php";
include "controller/NCR_MakeXML.php";
include "controller/GUIA_MakeXML.php";
include "controller/SendFileXml.php";
include "controller/FactSendFileXml.php";
include "controller/NcrSendFileXml.php";
include "controller/RetSendFileXml.php";
include "controller/GuiaSendFileXml.php";

// 14 Agosto 2020
include "controller/NumerosEnLetras.php";

// 16 Septiembre 2020
include "controller/GeneraPdf.php";


// 2 Septiembre 2021
include 'controller/PHPMailer.php';
include 'controller/POP3.php';
include 'controller/SMTP.php';
include 'controller/Exception.php';
include 'controller/OAuth.php';
include 'controller/vendor/autoload.php';

include "controller/Encryption.php";

//require __DIR__ . "/vendor/autoload.php";

