<?php
if (isset($_POST)) {
  switch ($_POST['tipodato']) {
    case 1:
      if (!empty($_POST['modelo']) && !empty($_POST['funcion'])) {
        $respuesta = ConfigurationData::update("cgdatoi", $_POST['valor'], $_POST['id']);
      } else if (empty($_POST['modelo']) && empty($_POST['funcion'])) {
        $respuesta = ConfigurationData::update("cgdatoi", $_POST['valor'], $_POST['id']);
      }else{
        $respuesta = ConfigurationData::update("cgdatov", '"' . implode(",", $_POST['valor']) . '"', $_POST['id']);
      }
      break;
    case 2:
      $respuesta = ConfigurationData::update("cgdatoc", '"'.$_POST['valor'].'"', $_POST['id']);
      break;
    case 3:
      $respuesta = ConfigurationData::update("cgdatod", $_POST['valor'], $_POST['id']);
      break;
    case 4:
      $respuesta = ConfigurationData::update("cgdatof", '"' . implode(",", $_POST['valor']) . '"', $_POST['id']);
      break;
    case 5:
      $respuesta = ConfigurationData::update("cgdatov", '"' . implode(",", $_POST['valor']) . '"', $_POST['id']);
      break;
    case 6:
      $respuesta = ConfigurationData::update("cgdatoi", $_POST['valor'], $_POST['id']);
      break;
    case 7:
      $respuesta = ConfigurationData::update("cgdatov", '"' . $_POST['valor'] . '"', $_POST['id']);
      break;
    case 8:
      $respuesta = ConfigurationData::update("cgdatoi", $_POST['valor'], $_POST['id']);
      break;
    case 9:
      $respuesta = ConfigurationData::update("cgdatoi", $_POST['valor'], $_POST['id']);
      break;
    case 10:
      $respuesta = ConfigurationData::update("cgdatoi", $_POST['valor'], $_POST['id']);
      break;
    case 11:
      $respuesta = ConfigurationData::update("cgdatov", '"' . $_POST['valor'] . '"', $_POST['id']);
      break;
    case 12:
      $respuesta = ConfigurationData::update("cgdatov", '"' . $_POST['valor'] . '"', $_POST['id']);
      break;
    case 13:
      $respuesta = ConfigurationData::update("cgdatov", '"' . $_POST['valor'] . '"', $_POST['id']);
      break;
    case 14:
      $respuesta = ConfigurationData::update("cgdatov", '"' . $_POST['valor'] . '"', $_POST['id']);
      break;
    case 15:
      $respuesta = ConfigurationData::update("cgdatov", '"' . $_POST['valor'] . '"', $_POST['id']);
      break;
      case 17:
          $idC = 0;
          if ($_POST['clienteRuc'] != 0) {
              $idCliente = PersonData::getByRucProveeR($_POST['clienteRuc']);
              $idC = $idCliente->ceid;
          }
          $respuesta = ConfigurationData::update("cgdatov", '"' . $idC . '"', $_POST['id']);
          break;
  }
  if ($respuesta[0] == false) {
    $msj = "0-Fallo proceso de actualización";
  } else {
    $msj = "1-Actualización realizada con exito";
  }
  echo json_encode($msj);
}