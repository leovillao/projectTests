<?php
if (Session::getUID() != "") {
    print "<script>window.location='./?view=home';</script>";
}
?>
<style>
    body {
        overflow-x: hidden;
    }

    .titulo {
        font-family: 'Work Sans', sans-serif;
        color: #266525;
        font-size: 1.5rem;
        text-align: center;
        font-weight: bold;
    }

    .fuenteTitulo {
        font-family: 'Work Sans', sans-serif;
        color: #266525;
        font-weight: bold;
    }

    .sectionLogin {
        background-color: white;
    }

    .s {
        font-size: 2rem;
        text-align: center;
    }

    .fondoImagenLogo {
        padding: 0;
        margin: 0;
        background-image: url("storage/logoConfig/fondoesignatura.jpg");
        background-size: 100% 100%;
        background-repeat: no-repeat;
        /*background-size: cover;*/
        width: 60vw;
        height: 92vh;
    }

    .sectionLogin {
        padding: 0;
        margin: 0;
        width: 40vw;
        height: 85vh;
    }

    .container {
        display: grid;
        grid-auto-flow: column dense;
        grid-template-columns: 1.1fr 0.9fr;
        grid-template-rows: 1fr;
        gap: 2px 2px;
        grid-template-areas:
    "imagen formulario";
    }

    .imagen {
        grid-area: imagen;
    }

    .formulario {
        grid-area: formulario;
    }

    .containerFormulario {
        display: grid;
        grid-auto-flow: column dense;
        grid-template-columns: 0.8fr 1.4fr 0.8fr;
        grid-template-rows: 1fr;
        gap: 2px 2px;
        grid-template-areas:
    ". formularioIngreso .";
    }

    .formularioIngreso {
        grid-area: formularioIngreso;
    }

    .enlaceRecuperar {
        text-decoration: none;
        /*font-weight: bold;*/
        color: #0a53be;
    }

    .groupEnlace {
        text-align: center;
        width: 100%;
    }
</style>
<section class="container" style="padding: 0px ;margin: 0px">
    <div class="fondoImagenLogo imagen"></div>
    <div class="sectionLogin formulario containerFormulario"
         style="display: flex;justify-content: center;align-items: center">
<!--        --><?php //unset($_SESSION['error'])?>
        <form style="width:80% ;display: flex;flex-direction: column;justify-content: space-around;align-items: center"
              class="formularioIngreso" id="formIngreso" accept-charset="UTF-8" role="form" method="post"
              action="./?view=processLoginLP">
            <div class="logo"
                 style="width: 70%;display: flex;flex-direction: column;align-items: center;justify-content: center">
                <img src="storage/logoConfig/logo01.jpg" alt="fondoEsignatura" style="width: 40%">
                <div class="titulo">E-SIGNATURA</div>
            </div>
            <div class="formulario" style="padding: 2.5rem;width: 100%">
                <div class="fuenteTitulo s"></div>
                <div class="form-group">
                    <input type="" placeholder="Usuario" class="form-control" name="usuario" id="usuario">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Password" class="form-control" name="password" id="password"
                           autocomplete="on">
                    <input type="hidden" name="formulariolp" value="formulariolp">
                </div>
                <div>
                    <input type="submit" class="btn btn-success btn-block" id="btnIngresar" value="INGRESAR">
                </div>
                <div class="form-group groupEnlace" style="text-align: right">
                    <a href="" class="enlaceRecuperar">Recuperar contraseña</a>
                </div>
                <?php
                if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                    echo '<div class="alert alert-danger" role="alert" id="contentError">' . $_SESSION['error'] . '</div>';
                }
                ?>
            </div>
        </form>
    </div>
</section>
<div class="modal fade" id="modalRecuperarContrasena" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="exampleModalLabel">Recuperación contraseña</h4>
            </div>
            <div class="modal-body">
                <form id="form-recuperarContrasena">
                    <div class="form-group">
                        <label for="ruc" class="control-label">Usuario</label>
                        <input type="text" class="form-control" id="rucRecuperacion" name="rucRecuperacion"
                               placeholder="Usuario">
                    </div>
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <strong>Recuperacion de Contraseña !</strong> Ingrese su <b>Usuario</b> , se enviara una clave
                        temporal al correo registrado en su ficha.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-block" id="btnSolicitarContrasena">Solicitar
                    Contraseña
                </button>
                <button type="button" class="btn btn-default btn-block" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        function validaError() {
            $("#contentError").delay(2000).fadeOut('fast');
        }

        if (document.getElementById('contentError')) {
            validaError()
        }

        // $("#rucRecuperacion").mask('0000000000000')
        // $("#usuario").mask('0000000000000')

        $(".enlaceRecuperar").click(function (e) {
            e.preventDefault()
            $("#modalRecuperarContrasena").modal('toggle')
        })

        $(document).on("click", "#btnSolicitarContrasena", function (e) {
            if ($("#rucRecuperacion").val() != '') {
                e.preventDefault()
                $.get("processExt/recuperarContrasena.php", {usuario: $("#rucRecuperacion").val()})
                    .done(function (data) {
                        let r = JSON.parse(data)
                        if (r.substr(0, 1) == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: r.substr(2),
                            }).then((result) => {
                                if (result.isConfirmed) {
                                }
                            })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: r.substr(2),
                            }).then((result) => {
                                if (result.isConfirmed) {
                                }
                            })
                        }
                    });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: "Debe ingrese usuario",
                }).then((result) => {
                    if (result.isConfirmed) {
                    }
                })
            }
        })

    })
</script>