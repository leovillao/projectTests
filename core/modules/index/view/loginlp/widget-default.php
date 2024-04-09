<section class="sectionLogin" style="display: flex;justify-content: center;align-items: center">
    <form style="width: 40vw ;height: 300px ;margin: auto;background-color: #F1AF35;display: flex;border-radius: 15px" id="formIngreso" accept-charset="UTF-8" role="form" method="post" action="index.php?view=processLoginLP">
        <div class="logo" style="width: 50%;display: flex;flex-direction: column;align-items: center;justify-content: center">
            <img src="storage/logoConfig/LOGOHALCON1.png" alt="logoHalcomStore" style="width: 40%">
            <div class="titulo">HALCOMSTOCK</div>
        </div>
        <div class="formulario" style="padding: 2.5rem;width: 50%">
            <div class="fuenteTitulo s">SISTEMA DE INVENTARIO FISICO</div>
            <div class="form-group">
                <input type="" placeholder="Usuario" class="form-control" name="usuario" id="usuario">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Password" class="form-control" name="password" id="password" autocomplete="on">
                <input type="hidden" name="formulariolp" value="formulariolp">
            </div>
            <div>
                <input type="submit" class="btn btn-success btn-block" id="btnIngresar" value="INGRESAR">
                <button class="btn btn-danger btn-block">CANCELAR</button>
            </div>
        </div>
    </form>
</section>