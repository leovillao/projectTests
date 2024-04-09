if (document.getElementById('configuracion')) {
    $(document).ready(function () {
        $('.select-multipled').select2();
        $(document).on("click", ".btn-actualizar", function () {
            let id = $(this).attr("num")
            let modelo = $(this).attr("modelo")
            let funcion = $(this).attr("funcion")
            // let valor = $(this).closest('tr').find('td:eq(2)').text()
            let fila = $(this).parents("tr") // se captura la columna donde se encuentra el boton al cual se da click
            let valor = fila.find(".dato").val(); // se captura los valores de los datos para realizar la actualizacion de acuerdo al ID
            let tipodato = fila.find(".dato").attr('tipodato'); // se captura los valores de los datos para realizar la actualizacion de acuerdo al ID
            let clienteRuc = ""
            if (fila.find(".dato").attr('idcliente')) {
                clienteRuc = fila.find(".dato").attr('idcliente')
            }
            if (valor == "") {
                clienteRuc = 0
            }
            $.ajax({
                url: './?action=configuration',
                type: 'POST',
                data: {
                    id: id,
                    valor: valor,
                    tipodato: tipodato,
                    modelo: modelo,
                    funcion: funcion,
                    clienteRuc: clienteRuc
                },
                success: function (respon) {
                    console.log(respon)
                    let res = JSON.parse(respon)
                    if (res.substr(0, 1) == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error...',
                            text: res.substr(2),
                        })
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Actualización',
                            text: res.substr(2),
                        })
                    }
                }
            })
        })

        $(document).on('blur', '.control-decimales-costos-unit', function () {
            // e.preventDefault()
            let valor = $(this).val()
            if (valor >= 2 && valor <= 10) {

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Valor no valido',
                })
                $(this).val(2)
            }
        })

        $(document).on('click', '#procesos', function () {
            $("#modalProcesos").modal('toggle')
        })

        $(document).on('click', '#btnStock', function () {
            $.ajax({
                url: './?action=processConfig',
                type: 'POST',
                beforeSend: function () {
                    $("#modalProcesos").modal('hide')
                    $("#myModalLoading").modal("toggle")
                }
                /*success: function (respuesta) {
                    console.log(respuesta)
                }*/
            }).done(function (data) {
                $("#myModalLoading").modal("hide")
                Swal.fire({
                    icon: 'success',
                    // title: '...',
                    text: 'Proceso terminado!',
                })
            });
        })


    }) // document ready
}