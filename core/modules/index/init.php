<?php
if (!isset($_GET["action"])) {
    Module::loadLayout("index");
} else {
    if (View::validaSesion() == false) {
        View::login('login');
    } else {
        Action::load($_GET["action"]);
    }
}

?>