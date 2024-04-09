<?php

class FunicionesPVP {

  public function exportDataArrayPrimer($arrayPOST) {
    /*$arrayProducto = array(
            '1' => "Todos",
            '2' => "Categoria",
            '3' => "SubCategoria",
            '4' => "Etiqueta",
            '5' => "SubEtiqueta",
            '6' => "Marca",
            '7' => "Etiqueta Inv",
            '8' => "Categoria + Subcategoria",
            '9' => "Categoria + Etiqueta",
            '10' => "Categoria + SubEtiqueta",
            '11' => "Categoria + Marca",
            '12' => "Categoria + Etiqueta Inv",
            '13' => "Subcategoria + Etiqueta",
            '14' => "Subcategoria + SubEtiqueta",
            '15' => "Subcategoria + Marca",
            '16' => "Subcategoria + Etiqueta Inv",
            '17' => "Etiqueta + Marca",
            '18' => "Marca + Etiqueta Inv",
            '19' => "Categoria + Subcategoria + Etiqueta",
            '20' => "Categoria + Subcategoria + SubEtiqueta",
            '21' => "Categoria + Subcategoria + Marca",
            '22' => "Categoria + Subcategoria + Etiqueta Inv",
            '23' => "Etiqueta + SubEtiqueta + Marca",
            '24' => "Etiqueta + SubEtiqueta + Etiqueta Inv",
            '25' => "Producto",
    );*/
    $pro = '';
    if ($arrayPOST['grp-productos'] >= 2 && $arrayPOST['grp-productos'] <= 7) {
      $pro = self::loadNameCampos($arrayPOST);
      $productos = ProductData::getSearchOneIdProduct($pro[0], $pro[1]);
      foreach ($productos as $producto) {
        $ids[] = $producto->itid;
      }
    } elseif ($arrayPOST['grp-productos'] >= 8 && $arrayPOST['grp-productos'] <= 18) {
      $pro = self::loadNameCampos($arrayPOST);
      $productos = ProductData::getSearchTwo($pro[0], $pro[1], $pro[2], $pro[3]);
      foreach ($productos as $producto) {
        $ids[] = $producto->itid;
      }
    } elseif ($arrayPOST['grp-productos'] >= 19 && $arrayPOST['grp-productos'] <= 24) {
      $pro = self::loadNameCampos($arrayPOST);
      $productos = ProductData::getSearchThree($pro[0], $pro[1], $pro[2], $pro[3], $pro[4], $pro[5]);
      foreach ($productos as $producto) {
        $ids[] = $producto->itid;
      }
    }
    return implode(',', array_unique($ids));
  }

  public function exportDataArraySgdo($arrayPOST) {
    /*$arrayProducto = array(
            '1' => "Todos",
            '2' => "Categoria",
            '3' => "SubCategoria",
            '4' => "Etiqueta",
            '5' => "SubEtiqueta",
            '6' => "Marca",
            '7' => "Etiqueta Inv",
            '8' => "Categoria + Subcategoria",
            '9' => "Categoria + Etiqueta",
            '10' => "Categoria + SubEtiqueta",
            '11' => "Categoria + Marca",
            '12' => "Categoria + Etiqueta Inv",
            '13' => "Subcategoria + Etiqueta",
            '14' => "Subcategoria + SubEtiqueta",
            '15' => "Subcategoria + Marca",
            '16' => "Subcategoria + Etiqueta Inv",
            '17' => "Etiqueta + Marca",
            '18' => "Marca + Etiqueta Inv",
            '19' => "Categoria + Subcategoria + Etiqueta",
            '20' => "Categoria + Subcategoria + SubEtiqueta",
            '21' => "Categoria + Subcategoria + Marca",
            '22' => "Categoria + Subcategoria + Etiqueta Inv",
            '23' => "Etiqueta + SubEtiqueta + Marca",
            '24' => "Etiqueta + SubEtiqueta + Etiqueta Inv",
            '25' => "Producto",
    );*/
    $pro = '';
    if ($arrayPOST['grp-productos'] >= 2 && $arrayPOST['grp-productos'] <= 7) {
      $pro = self::loadNameCampos($arrayPOST);
      $precio = "itpromo" . substr(ConfigurationData::getById($arrayPOST['listapreciobase'])->cgnombre, 7);
      $productos = ProductData::getDataForOne($pro[0], $pro[1], $precio);
      foreach ($productos as $producto) {
        $ids[] = array(
                "id" => $producto->itid,
                "precio" => $producto->$precio,
        );
      }
    } elseif ($arrayPOST['grp-productos'] >= 8 && $arrayPOST['grp-productos'] <= 18) {
      $pro = self::loadNameCampos($arrayPOST);
      $precio = "itpromo" . substr(ConfigurationData::getById($arrayPOST['listapreciobase'])->cgnombre, 7);
      $productos = ProductData::getDataForTwo($pro[0], $pro[1], $pro[2], $pro[3], $precio);
      foreach ($productos as $producto) {
        $ids[] = array(
                "id" => $producto->itid,
                "precio" => $producto->$precio,
        );
      }
    } elseif ($arrayPOST['grp-productos'] >= 19 && $arrayPOST['grp-productos'] <= 24) {
      $pro = self::loadNameCampos($arrayPOST);
      $precio = "itpromo" . substr($arrayPOST['listapreciobase'], 7);
      $productos = ProductData::getDataForThree($pro[0], $pro[1], $pro[2], $pro[3], $pro[4], $pro[5], $precio);
      foreach ($productos as $producto) {
        $ids[] = array(
                "id" => $producto->itid,
                "precio" => $producto->$precio,
        );
      }
    }
//  return implode(',', array_unique($ids));
    return $ids;
  }

  public function exportDataArrayTcr($arrayPOST) {
    $pro = '';
    if ($arrayPOST['grp-productos'] >= 2 && $arrayPOST['grp-productos'] <= 7) {
      $pro = self::loadNameCampos($arrayPOST);
      /*LISTA DE PRECIOS QUE VA A SER AFECTADA */
      $precio = "itpromo" . substr(ConfigurationData::getById($arrayPOST['listaprecio'])->cgnombre, 7);
      $productos = ProductData::getAllProductWithCoMax($pro[0], $pro[1]);
      foreach ($productos as $producto) {
        $ids[] = array(
                "id" => $producto->idproduct,
                "costomaximo" => $producto->costomaximo,
        );
      }
    } elseif ($arrayPOST['grp-productos'] >= 8 && $arrayPOST['grp-productos'] <= 18) {
      $pro = self::loadNameCampos($arrayPOST);
      /*LISTA DE PRECIOS QUE VA A SER AFECTADA */
      $precio = "itpromo" . substr(ConfigurationData::getById($arrayPOST['listaprecio'])->cgnombre, 7);
      $productos = ProductData::getAllProductWithCoMaxTwo($pro[0], $pro[1], $pro[2], $pro[3]);
      foreach ($productos as $producto) {
        $ids[] = array(
                "id" => $producto->idproduct,
                "costomaximo" => $producto->costomaximo,
        );
      }
    } elseif ($arrayPOST['grp-productos'] >= 19 && $arrayPOST['grp-productos'] <= 24) {
      $pro = self::loadNameCampos($arrayPOST);
      /*LISTA DE PRECIOS QUE VA A SER AFECTADA */
      $precio = "itpromo" . substr($arrayPOST['listaprecio'], 7);
      $productos = ProductData::getAllProductWithCoMaxThr($pro[0], $pro[1], $pro[2], $pro[3], $pro[4], $pro[5]);
      foreach ($productos as $producto) {
        $ids[] = array(
                "id" => $producto->idproduct,
                "costomaximo" => $producto->costomaximo,
        );
      }
    }
//  return implode(',', array_unique($ids));
    return $ids;
  }

  public function loadNameCampos($arrayPOST) {
    /*`itid`,`ctid`, `ct2id`, `etqid`, `subetqid`, `maid`, `itcodigo`, `paid`, `itetiqueta`
    '2' => "Categoria",
      '3' => "SubCategoria",
      '4' => "Etiqueta",
      '5' => "SubEtiqueta",
      '6' => "Marca",
      '7' => "Etiqueta Inv",*/
    /*/*$_POST['grp-productos']
  $_POST['etiqueta']
  $_POST['subetiqueta']
  $_POST['categorias']
  $_POST['subcategoria']
  $_POST['productosGrp']
  $_POST['marcas']
  $_POST['etiqTags']
   * */
    if ($arrayPOST['grp-productos'] == 2) {
      $indice[] = "ctid";
      $indice[] = $arrayPOST['categorias'];
    } elseif ($arrayPOST['grp-productos'] == 3) {
      $indice[] = "ct2id";
      $indice[] = $arrayPOST['subcategoria'];
    } elseif ($arrayPOST['grp-productos'] == 4) {
      $indice[] = "etqid";
      $indice[] = $arrayPOST['etiqueta'];
    } elseif ($arrayPOST['grp-productos'] == 5) {
      $indice[] = "subetqid";
      $indice[] = $arrayPOST['subetiqueta'];
    } elseif ($arrayPOST['grp-productos'] == 6) {
      $indice[] = "maid";
      $indice[] = $arrayPOST['marcas'];
    } elseif ($arrayPOST['grp-productos'] == 7) {
      $indice[] = "itetiqueta";
      $indice[] = $arrayPOST['etiqTags'];
    }
    /*'8' => "Categoria + Subcategoria",
            '9' => "Categoria + Etiqueta",
            '10' => "Categoria + SubEtiqueta",
            '11' => "Categoria + Marca", ------
            '12' => "Categoria + Etiqueta Inv",
            '13' => "Subcategoria + Etiqueta",
            '14' => "Subcategoria + SubEtiqueta",
            '15' => "Subcategoria + Marca",
            '16' => "Subcategoria + Etiqueta Inv",
            '17' => "Etiqueta + Marca",
            '18' => "Marca + Etiqueta Inv",*/
    /*`itid`,`ctid`, `ct2id`, `etqid`, `subetqid`, `maid`, `itcodigo`, `paid`, `itetiqueta`*/
    if ($arrayPOST['grp-productos'] == 8) {
      $indice[] = "ctid";
      $indice[] = "ct2id";
      $indice[] = $arrayPOST['categorias'];
      $indice[] = $arrayPOST['subcategoria'];

    } elseif ($arrayPOST['grp-productos'] == 9) {
      $indice[] = "ctid";
      $indice[] = "etqid";
      $indice[] = $arrayPOST['categorias'];
      $indice[] = $arrayPOST['etiqueta'];

    } elseif ($arrayPOST['grp-productos'] == 10) {
      $indice[] = "ctid";
      $indice[] = "maid";
      $indice[] = $arrayPOST['categorias'];
      $indice[] = $arrayPOST['subetiqueta'];

    } elseif ($arrayPOST['grp-productos'] == 11) {
      $indice[] = "ctid";
      $indice[] = "maid";
      $indice[] = $arrayPOST['categorias'];
      $indice[] = $arrayPOST['marcas'];

    } elseif ($arrayPOST['grp-productos'] == 12) {
      $indice[] = "ctid";
      $indice[] = "etiqTags";
      $indice[] = $arrayPOST['categorias'];
      $indice[] = $arrayPOST['etiqTags'];
    } elseif ($arrayPOST['grp-productos'] == 13) {
      $indice[] = "ct2id";
      $indice[] = "etqid";
      $indice[] = $arrayPOST['subcategoria'];
      $indice[] = $arrayPOST['etiqueta'];

    } elseif ($arrayPOST['grp-productos'] == 14) {
      $indice[] = "ct2id";
      $indice[] = "subetqid";
      $indice[] = $arrayPOST['subcategoria'];
      $indice[] = $arrayPOST['subetiqueta'];

    } elseif ($arrayPOST['grp-productos'] == 15) {
      $indice[] = "ct2id";
      $indice[] = "maid";
      $indice[] = $arrayPOST['subcategoria'];
      $indice[] = $arrayPOST['marcas'];

    } elseif ($arrayPOST['grp-productos'] == 16) {
      $indice[] = "ct2id";
      $indice[] = "itetiqueta";
      $indice[] = $arrayPOST['subcategoria'];
      $indice[] = $arrayPOST['etiqTags'];

    } elseif ($arrayPOST['grp-productos'] == 17) {
      $indice[] = "etqid";
      $indice[] = "maid";
      $indice[] = $arrayPOST['etiqueta'];
      $indice[] = $arrayPOST['marcas'];

    } elseif ($arrayPOST['grp-productos'] == 18) {
      $indice[] = "maid";
      $indice[] = "itetiqueta";
      $indice[] = $arrayPOST['marcas'];
      $indice[] = $arrayPOST['etiqTags'];

    }
    /*'19' => "Categoria + Subcategoria + Etiqueta",
            '20' => "Categoria + Subcategoria + SubEtiqueta",
            '21' => "Categoria + Subcategoria + Marca",
            '22' => "Categoria + Subcategoria + Etiqueta Inv",
            '23' => "Etiqueta + SubEtiqueta + Marca",
            '24' => "Etiqueta + SubEtiqueta + Etiqueta Inv",*/
    /*`itid`,`ctid`, `ct2id`, `etqid`, `subetqid`, `maid`, `itcodigo`, `paid`, `itetiqueta`*/
    if ($arrayPOST['grp-productos'] == 19) {
      $indice[] = "ctid";
      $indice[] = "ct2id";
      $indice[] = "etqid";
      $indice[] = $arrayPOST['categorias'];
      $indice[] = $arrayPOST['subcategoria'];
      $indice[] = $arrayPOST['etiqueta'];

    } elseif ($arrayPOST['grp-productos'] == 20) {
      $indice[] = "ctid";
      $indice[] = "ct2id";
      $indice[] = "subetqid";
      $indice[] = $arrayPOST['categorias'];
      $indice[] = $arrayPOST['subcategoria'];
      $indice[] = $arrayPOST['subetiqueta'];

    } elseif ($arrayPOST['grp-productos'] == 21) {
      $indice[] = "ctid";
      $indice[] = "ct2id";
      $indice[] = "maid";
      $indice[] = $arrayPOST['categorias'];
      $indice[] = $arrayPOST['subcategoria'];
      $indice[] = $arrayPOST['marcas'];

    } elseif ($arrayPOST['grp-productos'] == 22) {
      $indice[] = "ctid";
      $indice[] = "ct2id";
      $indice[] = "itetiqueta";
      $indice[] = $arrayPOST['categorias'];
      $indice[] = $arrayPOST['subcategoria'];
      $indice[] = $arrayPOST['etiqTags'];

    } elseif ($arrayPOST['grp-productos'] == 23) {
      $indice[] = "etqid";
      $indice[] = "subetqid";
      $indice[] = "maid";
      $indice[] = $arrayPOST['etiqueta'];
      $indice[] = $arrayPOST['subetiqueta'];
      $indice[] = $arrayPOST['marcas'];

    } elseif ($arrayPOST['grp-productos'] == 24) {
      $indice[] = "etqid";
      $indice[] = "subetqid";
      $indice[] = "itetiqueta";
      $indice[] = $arrayPOST['etiqueta'];
      $indice[] = $arrayPOST['subetiqueta'];
      $indice[] = $arrayPOST['etiqTags'];

    }
    return $indice;
  }

}