<?php
if (isset($_POST)){
	$funciones = new FuncionesData();
	$funciones->name = ucfirst(strtolower(trim($_POST['name'])));
//	$funciones->id_nego = $_POST['unidad'];
	$funciones->ruc = $_SESSION['ruc'];
	$state = 0;
	if (isset($_POST['state'])):
		$state = 1;
	endif;
	$funciones->is_state = $state;
    $fun = $funciones->add();
	if (count($fun) != 0){
	    echo '<span class="alert alert-success">Grabado con exito..!!!</span>';
    }else{
        echo '<span class="alert alert-danger">Intente nuevamente..!!!</span>';
    }
}
?>