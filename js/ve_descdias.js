if (document.getElementById('descuentodias')) {
    $(document).ready(function () {
        /* ==================================================================
                  CARGA LOS DATOS DE LA LISTA DE DESCUENTO A EDITAR
        * =================================================================*/
        $(".btn-editar-descuento").on('click', function (e) {
            e.preventDefault()
            let row = $(this)
            let producto = row.parents('tr').find('td:eq(1)').text()
            // let cename = row.attr('id')
            let valores = row.attr('valores')
            let id = row.attr('id')
            let descuento = row.attr('descuento')
            let arrayValores = valores.split(',')
            // console.log(valores.split(','))
            $("#modalDescuento").modal('toggle')
            $("#modalDescuento .modal-title").text(producto)
            $("#group-producto").css('display', 'none')
            $.each(arrayValores, function (i, item) {
                if (i == 0) {
                    $("#lunes").val(item).trigger('change')
                } else if (i == 1) {
                    $("#martes").val(item).trigger('change')
                } else if (i == 2) {
                    $("#miercoles").val(item).trigger('change')
                } else if (i == 3) {
                    $("#jueves").val(item).trigger('change')
                } else if (i == 4) {
                    $("#viernes").val(item).trigger('change')
                } else if (i == 5) {
                    $("#sabado").val(item).trigger('change')
                } else if (i == 6) {
                    $("#domingo").val(item).trigger('change')
                }
            });
            $("#btn-process").text("Actualizar")
            $("#id-descuento").val(id)
            $("#descuento").val(descuento)
            $("#option").val(2)
        })
        /* ==================================================================
                VALIDA EL PROCESO DE ELIMINACION DEL DESCUENTO
        * =================================================================*/
        $(".btn-eliminar-descuento").on('click', function (e) {
            e.preventDefault()
            let row = $(this)
            let producto = row.parents('tr').find('td:eq(1)').text()
            let id = row.attr('id')
            Swal.fire({
                title: producto,
                text: "Desea eliminar el descuento para este producto",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, borrarlo!'
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarDescuento(id)
                }
            })
        })
        /* ==================================================================
               FUNCION QUE ELIMINA EL DESCUENTO
        * =================================================================*/
        function eliminarDescuento(id) {
            $.ajax({
                url: './?action=ve_descuentosdia',
                type: 'POST',
                data: {id: id, option: 4},
                success: function (respond) {
                    let res = JSON.parse(respond)
                    if (res.substr(0, 1) == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: res.substr(2),
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                    } else {
                        Swal.fire({
                            icon: 'success',
                            // title: '',
                            text: res.substr(2),
                            // footer: '<a href="">Why do I have this issue?</a>'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                loadPagPrincipal()
                            }
                        })
                    }
                }
            })
        }
        /* ==================================================================
                  MUESTRA LA VENTANA DE CREACION DE NUEVO DESCUENTO
        * =================================================================*/
        $("#btn-nuevo-descuento").on('click', function (e) {
            e.preventDefault()
            $("#modalDescuento").modal('toggle')
            $("#form-descuentos").trigger('reset')
            $("#modalDescuento .modal-title").text('Nuevo descuento')
            $("#group-producto").css('display', 'block')
            $("#btn-process").text("Grabar")
            $("#descuento").val('')
            loadProductos()
        })
        /* ==================================================================
        EJECUTA LA CREACION Y LA EDICION DEL DESCUENTO SEGUN EL PROCESO SELECCIONADO
        * =================================================================*/
        $("#btn-process").on('click', function (e) {
            e.preventDefault()
            let option = $("#option").val()
            let producto = $("#producto").val()
            let datos = new FormData(document.getElementById('form-descuentos'))
            if (option == 1) {
                datos.append("producto", producto)
            }
            $.ajax({
                url: './?action=ve_descuentosdia',
                type: 'POST',
                processData: false,
                contentType: false,
                data: datos,
                success: function (respond) {
                    let res = JSON.parse(respond)
                    if (res.substr(0, 1) == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: res.substr(2),
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                    } else {
                        Swal.fire({
                            icon: 'success',
                            // title: '',
                            text: res.substr(2),
                            // footer: '<a href="">Why do I have this issue?</a>'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                loadPagPrincipal()
                            }
                        })

                    }
                }
            })
        })
        /* ==================================================================
                            CARGA LA LISTA DE PRODUCTOS
        * =================================================================*/
        function loadProductos() {
            $.ajax({
                url: './?action=ve_descuentosdia',
                type: 'POST',
                data: {option: 3},
                success: function (respond) {
                    let htmloption = '<option value ="0">Selecione producto...</option>'
                    let datos = JSON.parse(respond)
                    $.each(datos, function (i, item) {
                        htmloption += '<option value ="' + item.id + '">' + item.producto + '</option>'
                    })
                    $("#producto").html(htmloption)
                    $("#producto").select2();
                }
            })
        }

        function loadPagPrincipal(){
            location.href = "./?view=ve_descuentosdias";
        }


    })
}