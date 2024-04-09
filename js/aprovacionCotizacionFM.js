$(function () {
    /*
    * aprobadosIds
    * noAprobadosIds
    * */

    $(document).on("click", ".remove-cotizacion-cab", function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let cliente = $(this).closest('tr').find('td:eq(2)').text()
        Swal.fire({
            html: '<h4>Anular cotizacion # ' + id + ' , Cliente : ' + cliente + ' ?</h4>',
            // text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Anular!'
        }).then((result) => {
            if (result.isConfirmed) {
                anulaCabeceraCotizacion(id)
            }
        })
    })


    $(".chk-padre").click(function () {
        if ($(this).is(':checked')) {
            // checkAll()
            validaChckbox(1)
        }else{
            validaChckbox(0)
            // $("#aprobadosIds").val('')
            // $("#noAprobadosIds").val('')
            // uncheckAll()
        }

    })

    $("#cerrarModal").click(function () {
        // $("#aprobadosIds").val('')
        // $("#noAprobadosIds").val('')
    })

    function uncheckAll() {
        document.querySelectorAll('.chk-selec').forEach(function(checkElement) {
            checkElement.checked = false;
        });
    }
    function checkAll() {
        document.querySelectorAll('.chk-selec').forEach(function(checkElement) {
            checkElement.checked = true;
        });
    }

    function validaChckbox(estado) {
        // $(".chk-padre").prop('checked',false)
        // document.querySelectorAll('.chk-selec').forEach(function(checkElement) {
        //     if(checkElement.checked == true){
        //         $(".chk-padre").prop('checked',true)
        //     }
        // });
        $(".chk-selec").each(function () {
            // $(this).prop('checked', true)
            if (estado == 1) {
                $(this).prop('checked', true)
            }else{
                $(this).prop('checked', false)
            }
            let id = $(this).parents("tr").find("td").eq(0).text()
            if ($(this).is(":checked")) {
                let idsnotAprob = $("#noAprobadosIds").val()
                let stringAprob = $("#noAprobadosIds").val()
                let array = stringAprob.split(',');
                let nuevoArray = array.filter(element => element != id);
                $("#noAprobadosIds").val(nuevoArray.toString())
                let idsAprob = $("#aprobadosIds").val()
                if (idsAprob == '') {
                    $("#aprobadosIds").val(id)
                } else {
                    $("#aprobadosIds").val(idsAprob + ',' + id)
                }
            } else {
                let stringAprob = $("#aprobadosIds").val()
                let array = stringAprob.split(',');
                let nuevoArray = array.filter(element => element != id);
                $("#aprobadosIds").val(nuevoArray.toString())
                let noAprob = $("#noAprobadosIds").val()
                if (noAprob == '') {
                    $("#noAprobadosIds").val(id)
                } else {
                    $("#noAprobadosIds").val(noAprob + ',' + id)
                }
            }
        })
    }

    function anulaCabeceraCotizacion(id) {
        $.ajax({
            type: "POST",
            url: "./?action=in_cotizacion_anula_cabecera",
            data: {"id": id},
            success: function (respon) {
                console.log(respon)
                let res = JSON.parse(respon)
                if (res.substr(0, 1) == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: res.substr(2),
                    })
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: res.substr(2),
                    })
                    clickCabeceras()
                }
            }
        })
    }

    $("#btn-autrizar").click(function () {
        let noaprobados = $("#noAprobadosIds").val()
        let aprobados = $("#aprobadosIds").val()
        aprobarDetalle(aprobados, noaprobados)
    })

    function aprobarDetalle(aprobados, noaprobados) {
        $.ajax({
            type: "POST",
            url: "./?action=in_cotizacion_aprobar_detalle",
            data: {"aprobados": aprobados, "noaprobados": noaprobados},
            success: function (respon) {
                console.log(respon)
                let res = JSON.parse(respon)
                if (res.substr(0, 1) == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: res.substr(2),
                    })
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: res.substr(2),
                    })
                    $("#modelDetalleCotizaciones").modal('toggle')
                    clickCabeceras()
                }
            }
        })
    }

    function clickCabeceras() {
        $("#buscar-cotizaciones").click()
    }


    $(document).on('click', '.remove-cotizacion', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        let codigo = $(this).closest('tr').find('td:eq(1)').text()
        let producto = $(this).closest('tr').find('td:eq(2)').text()
        Swal.fire({
            html: '<h4>Anular ' + codigo + '-' + producto + ' ?</h4>',
            // text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Anular!'
        }).then((result) => {
            if (result.isConfirmed) {
                AnularRowDetalle(id, $(this).closest('tr'))
            }
        })
    })

    function AnularRowDetalle(id, row) {
        $.ajax({
            type: "POST",
            url: "./?action=in_cotizacion_udate_detalle",
            data: {"id": id},
            success: function (respon) {
                let res = JSON.parse(respon)
                if (res.substr(0, 1) == 0) {
                    Swal.fire({
                        icon: 'error',
                        title: res.substr(2),
                    })
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: res.substr(2),
                    })
                    row.remove()
                }
            }
        })
    }


    // funcion que valida el estado de los checkbox para actualizar el estado de los productos del detalle de cada cotizacion

    $(document).on('change', '.chk-selec', function () {
        let id = $(this).closest('tr').find('td:eq(0)').text()
        if ($(this).is(":checked")) {
            let idsnotAprob = $("#noAprobadosIds").val()
            let stringAprob = $("#noAprobadosIds").val()
            let array = stringAprob.split(',');
            let nuevoArray = array.filter(element => element != id);
            $("#noAprobadosIds").val(nuevoArray.toString())
            let idsAprob = $("#aprobadosIds").val()
            if (idsAprob == '') {
                $("#aprobadosIds").val(id)
            } else {
                $("#aprobadosIds").val(idsAprob + ',' + id)
            }
        } else {
            let stringAprob = $("#aprobadosIds").val()
            let array = stringAprob.split(',');
            let nuevoArray = array.filter(element => element != id);
            $("#aprobadosIds").val(nuevoArray.toString())
            let noAprob = $("#noAprobadosIds").val()
            if (noAprob == '') {
                $("#noAprobadosIds").val(id)
            } else {
                $("#noAprobadosIds").val(noAprob + ',' + id)
            }
        }
    })


    $(document).on("click", ".detallecotizacion", function () {
        $(".chk-padre").prop('checked',false)
        $("#aprobadosIds").val('')
        $("#noAprobadosIds").val('')
        let attributo = $(this).attr('id')
        // loadC(attributo)
        let tableDetalle = $("#table-detalle-cotizaciones").DataTable({
            "destroy": true,
            "ajax": {
                "method": "POST",
                "url": "./?action=in_cotizaciones_getDetalle",
                "data": {"id": attributo}
            },
            "columns": [
                {"data": "id", "width": "10%"},
                {"data": "codigo"},
                {"data": "producto"},
                {"data": "unidad", className: "text-center"},
                {"data": "cantidad", className: "text-right"},
                {"data": "precio", className: "text-right"},
                {"data": "estado", className: "text-center"},
                {"data": "selector", className: "text-center"},
                {
                    "defaultContent": "<button class='btn btn-xs btn-danger remove-cotizacion '><i class=\"fa fa-remove\" aria-hidden=\"true\"></i></button>",
                    className: "text-center"
                },
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })
        $("#modelDetalleCotizaciones").modal('toggle')
    })


    const desde = $("#desde").val()
    const hasta = $("#hasta").val()
    loadCotizacionesFM(desde, hasta)

    $("#buscar-cotizaciones").click(function () {
        const desde = $("#desde").val()
        const hasta = $("#hasta").val()
        loadCotizacionesFM(desde, hasta)
    })

    function loadCotizacionesFM(desde, hasta) {
        var dtTable12 = $("#table-cotizaciones").DataTable({
            "destroy": true,
            "ajax": {
                "method": "POST",
                "url": "./?action=in_cotizaciones_getFM",
                "data": {"desde": desde, "hasta": hasta}
            },

            "columns": [
                {"data": "cotizacion", "width": "10%"},
                {"data": "fecha"},
                {"data": "cliente"},
                {"data": "total", className: "text-right"},
                {"data": "estado", className: "text-center"},
                {"data": "creado", className: "text-center"},
                {
                    "defaultContent": "<button class='btn btn-xs btn-danger remove-cotizacion-cab '><i class=\"fa fa-remove\" aria-hidden=\"true\"></i></button>",
                    className: "text-center"
                },
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            },
        })
    }

    function loadDataTable() {
        $("#table-cotizaciones tbody tr").iterator(function () {
            console.log($(this).find("td").eq(0).text())
        })
    }

    // loadC(desde,hasta)
    function loadC(atributo) {
        $.ajax({
            type: "POST",
            url: "./?action=in_cotizaciones_getDetalle",
            data: {"id": atributo},
            success: function (respon) {
                console.log(respon)
            }
        })
    }
})