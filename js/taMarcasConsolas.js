$(function () {
    // getMarcas()
    //     $('#ciruc').
    // $(".km").mask('00000000');
    $(".telefono").mask('0000000000');
    $(".codigoCliente").mask('0000000000');
    // $(".valocobro").mask('00000000.00');

    $(".estabemision").mask('000-000');
    // $(".emision").mask('000');
    $(".numerofactura").mask('000000000');

    $("#btn-facturar").click(function () {
        if ($("#numeroCaja").val() != "") {
            let n = new FormData(document.getElementById('formfactura'));
            n.append("idSucursal", $("#sucursal").attr('idSucursal'))
            n.append("idEmision", $("#estabemision").attr('idEmision'))
            n.append("numCaja", $("#numeroCaja").val())
            n.append("valorEfectivo", $("#valorcobro").val())
            if (localStorage.getItem($("#agendaID").val()) !== null) {
                n.append('formaPago', localStorage.getItem($("#agendaID").val()));
            }
            $.ajax({
                url: "./?action=TaConsolas_facturacion",
                type: "POST",
                data: n,
                processData: false,
                contentType: false,
                success: function (respond) {
                    let r = JSON.parse(respond)
                    console.log(r)
                    if (r.msj.substr(0, 1) == 1) {
                        // {"documento":"001003000006227","id":3940,"tipo":"01","reporte":"0-Punto de emision no tiene configurado formato de impresion","tipoEmision":"1","msj":"1-Documento #001003000006227, grabado con exito","confImpresion":"3","caja":"","depura":{"sucursal":"General","estabemision":"001-003","numerofactura":"","agendaID":"55","idcliente":"61","direccion":"km 13.5 av Leon febres cordero","telefono":"0995329945","correo":"wramondev@gmail.com","idformacobro":"2","formacobro":"Tarjeta de Cr\u00e9dito","valorcobro":"17","idSucursal":"1","idEmision":"3","formaPago":"{\"idAgenda\":\"55\",\"cuentachque\":\"\",\"fechadocumento\":\"2022-11-09\",\"cuentatarjeta\":\"\",\"entidad\":\"3\",\"valorpago\":\"17.00000\"}"}}
                        Swal.fire({
                            icon: 'success',
                            title: r.msj.substr(2),
                        })
                        $("#numerofactura").val(r.documento.substr(6))
                        localStorage.clear();
                        processDocuments(r.documento, r.id, r.tipo, r.reporte)
                        // window.open('reportes/carwaiiReport/factura.php?id=' + r.id, '_blank');
                        window.open('reportes/carwaiiReport/' + r.reporte + '.php?id=' + r.id, '_blank');
                        $("#btn-facturar").prop('disabled',true)
                        $("#btn-facturar").attr('disabled')
                        $("#modalFacturacion").modal('hide')
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: r.msj.substr(2),
                        })
                    }
                    // console.log(respond);
                }
            })
        } else {
            Swal.fire({
                icon: 'error',
                title: "Debe realizar proceso de apertura de caja para poder facturar",
            })
        }
    })

    function processDocuments(documento, id, tipo, reporte) {
        $.ajax({
            type: "POST",
            url: './?action=TaConsolas_procesarDocs',
            data: {option: 1, "documento": documento, "id": id, "tipo": tipo, "reporte": reporte},
            beforeSend: function () {
            },
            error: function (data) {
                console.log(data)
            },
            success: function (data) {
                console.log(data)
                let dat = JSON.parse(data)
                // console.log(dat)
                let msjMail = ''
                console.log(data)
                if (dat[1]['msjMail'][0].substr(0, 1) == 1) {
                    msjMail = '<h4>' + dat[1]['msjMail'][0].substr(2) + '</h4>'
                }
                if (dat[0]['msjAuto'].substr(0, 1) == 1) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        html: '<h4>' + dat[0]['msjAuto'].substr(2) + ' </h4>' + msjMail,
                        showConfirmButton: false,
                        timer: 1500
                    })
                    $("#myModalLoadingRennvio").modal('hide')
                } else if (dat[0]['msjAuto'].substr(0, 1) == 0) {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        html: '<h4>' + dat[0]['msjAuto'].substr(2) + ' </h4>' + msjMail,
                        showConfirmButton: false,
                        timer: 1500
                    })
                    $("#myModalLoadingRennvio").modal('hide')
                }
            }
        });
    }

    /**
     * stu = JSON.stringify(stu);  // Convierta JSON en una cadena y guárdelo en una variable
     * localStorage.setItem("stu",stu);// Guarde la variable en localStorage
     * */
    $(document).on('click', '#btnCliente', function (e) {
        viewModalClientes()
    })

    $("#clienteDefault").click(function (e) {
        let r = $(this)
        $("#idcliente").val(r.attr('cliente-id'))
        $(".codigoCliente").val(r.attr('cliente-cerut'))
        $(".textoCliente").val(r.attr('cliente-cename'))
        $(".direccion").val(r.attr('cliente-direccion'))
        $(".correo").val(r.attr('cliente-email'))
        $(".telefono").val(r.attr('cliente-cephone'))
    })


    loadClientesData()

    function loadClientesData() {
        let opcion = 1
        $("#table-clientes").DataTable({
            "destroy": true,
            "keys": true,
            "ajax": {
                "method": "POST",
                "url": "./?action=processFacturacion",
                "data": {"option": opcion}
            },

            "columns": [
                {"data": "ruc"},
                {"data": "name"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })
    }

    function viewModalClientes() {
        $("#modalCliente").modal('toggle').on("shown.bs.modal", function () {
            $('#modalCliente .dataTables_filter input').focus();
        })
        $("#modalCliente .modal-header").css("background-color", "#84AC3B")
    }/* ================ SE EJECUTA LA VISUALIZACION DE LA VENTANA MODAL CLIENTES ===============*/


    $(document).on('click', '#tbody-cliente tr td', function () {
        let name = $(this).parents("tr").find("td").eq(1).text()
        let ruc = $(this).parents("tr").find("td").eq(0).text()
        let id = $(this).parents("tr").find('td:eq(1)').find('input[type="date"]').val()
        $(".codigoCliente").val(ruc)
        $(".textoCliente").val(name)
        $(".idcliente").val(id)

        $("#modalCliente").modal('toggle')
    }) /* ======= SELECCIONA EL CLIENTE Y LO CARGA EN LA SECCION DE CLIENTES DE LA VENTANA DE FACTURACION  ======= */

    $("#asignarPago").click(function () {
        localStorage.clear()
        const registro = {
            'idAgenda': $("#idAgenda").val(),
            'cuentachque': $("#cuentachque").val(),
            'fechadocumento': $("#fechadocumento").val(),
            'cuentatarjeta': $("#cuentatarjeta").val(),
            'entidad': $("#entidad").val(),
            'valorpago': $("#valorpago").val(),
        };

        let idAg = $("#idAgenda").val()
        if (localStorage.hasOwnProperty(idAg) == false) {
            localStorage.setItem(idAg, JSON.stringify(registro));
        } else {
            localStorage.removeItem(idAg)
            localStorage.setItem(idAg, JSON.stringify(registro));
        }
        $("#modalFormaPago").modal('toggle')
    })

    $(document).on("click", ".btn-facturar", function () {
        let nbr = $(this).closest("tr").find('input[type="number"]').val();
        console.log(nbr)
        let cliente = $(this).attr('cliente')
        let agenda = $(this).attr('id')
        let select = $(this).closest('tr').find('.formpago')
        let valorCobro = $(this).closest('tr').find('td:eq(16)').text()
        let textSelect = $("option:selected", select).attr("data-inbanco");
        let idFormaCobro = $(this).closest('tr').find('.formpago').val();
        let formatexto = $("option:selected", select).attr("data-forma");
        // let textSelect = $(this).closest('tr').find('.formpago').attr('data-namePago')
        // = $(this).closest('tr').find('.formpago').attr('namePago')
        $("#modalFacturacion").modal('toggle')
        $("#modalFacturacion .modal-title").text('Numero de servicio # ' + agenda.substr(3))
        $.ajax({
            url: "./?action=TaConsolas_getForFacturacion",
            type: "POST",
            data: {"cliente": cliente, "agenda": agenda.substr(3)},
            success: function (respond) {
                let cliente = JSON.parse(respond)
                $(".codigoCliente").val(cliente.ruc)
                $(".textoCliente").val(cliente.nombre)
                $(".direccion").val(cliente.direccion)
                $(".telefono").val(cliente.telefono)
                $(".correo").val(cliente.correo)
                $(".idcliente").val(cliente.id)
                $("#idformacobro").val(idFormaCobro)
                $("#agendaID").val(agenda.substr(3))
                // console.log(textSelect)
                if (textSelect > 0) {
                    if (localStorage.hasOwnProperty(agenda.substr(3))) {
                        let l = JSON.parse(localStorage.getItem(agenda.substr(3)));
                        $(".valorcobro").val(l.valorpago)
                        $(".formacobro").val(formatexto)
                    } else {
                        $(".valocobro").val('')
                        $(".formacobro").val('')
                        Swal.fire({
                            text: "Debe asignar tipo de cobro y valor para realizar proceso de facturación",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Swal.fire(
                                //     'Deleted!',
                                //     'Your file has been deleted.',
                                //     'success'
                                // )
                            }
                        })
                    }
                } else {
                    localStorage.removeItem(agenda.substr(3))
                    $(".formacobro").val(formatexto)
                    $(".formacobro").val(formatexto)
                    $(".valorcobro").val(parseFloat(nbr))
                }
            }
        })

    })


    const fecha = $("#fecha").val()

    $("#fecha").change(function () {
        getAgenda($(this).val())
    })

    function getAgenda(fecha) {
        $("#table-agendas").DataTable({
            "responsive": true,
            "destroy": true,
            "sort": false,
            "ajax": {
                "method": "POST",
                "url": "./?action=TaConsolas_gets",
                "data": {"fecha": fecha}
            },
            "columns": [
                {"data": "idagenda"},
                {"data": "cliente"},
                {"data": "fecha"},
                {"data": "desde"},
                {"data": "hasta"},
                {"data": "fechaupdate"},
                {"data": "servicio"},
                {"data": "direccion"},
                {"data": "telefono"},
                {"data": "comentario"},
                {"data": "placa"},
                {"data": "modelo"},
                {"data": "marca"},
                {"data": "anio"},
                {"data": "km"},
                // {"data": "atendido", "className": "text-center"},
                {"data": "accion"},
                {"data": "precio"},
                {"data": "formapago"},
                {"data": "factura", "className": "text-center"},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })
    }

    let table = $("#table-agendas").DataTable({
        "responsive": true,
        "destroy": true,
        "sort": false,
        "ajax": {
            "method": "POST",
            "url": "./?action=TaConsolas_gets",
            "data": {"fecha": fecha}
        },
        "columns": [
            {"data": "idagenda"},
            {"data": "cliente"},
            {"data": "fecha"},
            {"data": "desde"},
            {"data": "hasta"},
            {"data": "fechaupdate"},
            {"data": "servicio"},
            {"data": "direccion"},
            {"data": "telefono"},
            {"data": "comentario"},
            {"data": "placa"},
            {"data": "modelo"},
            {"data": "marca"},
            {"data": "anio"},
            {"data": "km"},
            {"data": "accion"},
            // {"data": "atendido", "className": "text-center"},
            {"data": "precio"},
            {"data": "formapago"},
            {"data": "factura"},
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
        },
    })

    new $.fn.dataTable.FixedHeader(table);

    $(document).on('click', ".btn-update-horafecha", function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let fecha = $(this).closest('tr').find('td:eq(2)').find('input[type="date"]').val()
        let desde = $(this).closest('tr').find('td:eq(3)').find('input[type="time"]').val()
        let hasta = $(this).closest('tr').find('td:eq(4)').find('input[type="time"]').val()
        Swal.fire({
            title: 'ACTUALIZACION FECHAS / HORARIO',
            text: "Desea realizar actualizacion de fechas / horarios de este servicios.?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Actualizar'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(id)
                actualizarFechaHoras(desde, hasta, fecha, id)
            }
        })
    })

    function actualizarFechaHoras(desde, hasta, fecha, id) {
        let fechaD = $("#fecha").val()
        $.ajax({
            url: "./?action=TaConsolas_updateHorario",
            type: "POST",
            data: {"id": id, "desde": desde, "hasta": hasta, "fecha": fecha},
            success: function (respond) {
                let o = JSON.parse(respond)
                console.log(o)
                if (o.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: o.substr(2),
                    })
                    getAgenda(fechaD)
                } else {
                    Swal.fire({
                        icon: "error",
                        title: o.substr(2),
                    })
                }
            }
        })
    }

    $(document).on('click', ".btn-actualizar", function () {
        // alert('aki')
        let id = $(this).attr('id')
        let rkm = $(this).attr('rkm')
        // let banco = $(this).closest('tr').find('td:eq(1)').text()
        let estado = $(this).closest('tr').find('input[type="checkbox"]').val()
        let kms = $(this).closest("tr").find("input[name='atendido']").val();
        // if (rkm == 0) {
        Swal.fire({
            title: 'SERVICIO ATENDIDO',
            text: "Seguro desea marcar este evento como atendido",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Actualizar'
        }).then((result) => {
            if (result.isConfirmed) {
                actualizarEstado(id, kms)
            }
        })
        // } else {
        //     Swal.fire({
        //         // position: 'top-end',
        //         icon: 'error',
        //         title: 'Debe ingresar Kilometraje',
        //         showConfirmButton: false,
        //         timer: 1500
        //     })
        // }
    })

    function actualizarEstado(id, km) {
        $.ajax({
            url: "./?action=TaConsolas_updateState",
            type: "POST",
            data: {"id": id, "km": km},
            success: function (respond) {
                let o = JSON.parse(respond)
                if (o.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: o.substr(2),
                    })
                    getAgenda(fecha)
                } else {
                    Swal.fire({
                        icon: "error",
                        title: o.substr(2),
                    })
                }
            }
        })
    }

    $(document).on("change", ".formpago", function () {
        let id = $(this).val()
        let valor = $(this).closest('tr').find('td:eq(16)').text()
        let idagenda = $(this).attr('id-agenda')
        $.ajax({
            url: "./?action=TaConsolas_formaspago",
            type: "POST",
            data: {"id": id},
            success: function (resp) {
                let datos = JSON.parse(resp)
                let opcion = ""
                // $(this).closest('tr').find('td:eq(17)').find('button').prop('disabled',false)
                if (datos.data.in_banco != 0) {
                    $(this).closest('tr').find('td:eq(17)').find('button').prop('disabled', true)
                    $(this).closest('tr').find('td:eq(17)').find('button').attr('disabled')
                    $("#modalFormaPago").modal('toggle')
                    if (datos.bancos != null) {
                        $.each(datos.bancos, function (i, item) {
                            opcion += '<option value="' + item.id + '">' + item.bname + '</option>'
                        })
                    } else if (datos.tarjetas != null) {
                        $.each(datos.tarjetas, function (i, item) {
                            opcion += '<option value="' + item.id + '">' + item.bname + '</option>'
                        })
                    }
                    if (datos.data.cffecha == "S") {
                        $("#fechadocumento").prop("disabled", false)
                    } else {
                        $("#fechadocumento").prop("disabled", true)
                    }
                    if (datos.data.cfctatar == "S") {
                        $("#cuentatarjeta").prop('disabled', false)
                    } else {
                        $("#cuentatarjeta").prop('disabled', true)
                    }
                    if (datos.data.cfpapeleta == "S") {
                        $("#cuentachque").prop('disabled', false)
                    } else {
                        $("#cuentachque").prop('disabled', true)
                    }

                    $("#idAgenda").val(idagenda)
                    $("#cfcodSri").val(datos.data.cfcodSri)
                    $("#fpid").val(id)
                    $("#valor").val(valor)
                    $("#valorpago").val(valor)
                    $("#entidad").html(opcion)
                }
            }
        })
    }) /* FUNCION QUE MUESTRA LA VENTANA MODAL DE PAGOS DE ACUERDO A LA FORMA DE PAGO */

    $("#valorpago").change(function () {
        let v = $("#valor").val()
        if (parseFloat($(this).val()) > parseFloat(v)) {
            $(this).val('')
        }
    })

    $("#procesaFormaPago").click(function () {

        $("#cuentachque").prop("disabled", false)
        $("#fechadocumento").prop("disabled", false)
        $("#cuentatarjeta").prop("disabled", false)
        $("#entidad").prop("disabled", false)
        // console.log("procesarPago")
        $.ajax({
            url: "./?action=TaGrabarPago",
            type: "POST",
            data: $("#pagos-news").serialize(),
            success: function (respond) {
                let o = JSON.parse(respond)
                if (o.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: o.substr(2),
                    })
                    getAgenda(fecha)
                } else {
                    Swal.fire({
                        icon: "error",
                        title: o.substr(2),
                    })
                }
            }
        })

        $("#cuentachque").prop("disabled", true)
        $("#fechadocumento").prop("disabled", true)
        $("#cuentatarjeta").prop("disabled", true)
        $("#entidad").prop("disabled", true)
    })

})
