$(function () {
    $("#eliminarProducto").click(function () {
        if (!$(this).attr('idproducto')) {
            Swal.fire({
                "icon": 'error',
                "title": "Debe seleccionar el producto a eliminar"
            })
        } else {
            let id = $(this).attr('idproducto')
            let name = $(this).attr('nameProducto')
            Swal.fire({
                title: name,
                text: "Dese eliminar este producto ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Borrar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                }
            })
        }
    })
    $('#nuevaEtiqueta').click(function (e) {
        e.preventDefault();
        $('#modalNewTags').modal('toggle')
        $('#newTags-form').trigger('reset')
        $('#modalNewTags .modal-title').text("Nueva Etiqueta")
        let id = $("#modalTags #idproveedorTags").val()
        $('#modalNewTags #idprovNewTags').val(id)
        $('#modalNewTags #tipo').val(1)
        $('#modalNewTags #actualizarTags').css('display', 'none')
        $('#modalNewTags #save-newTags').css('display', 'block')
    })

    $(document).on('click', '#save-newTags', function (e) {
        let tagName = $("#tags-name").val()
        if (tagName.length != '') {
            let data = $("#newTags-form").serialize()
            $.ajax({
                url: './?action=addTags',
                type: 'POST',
                data: $("#newTags-form").serialize(),
                success: function (res) {
                    let cod = res.substr(0, 1);
                    let msj = res.substr(2)

                    if (cod == 1) {
                        Swal.fire({
                            "icon": 'success',
                            "title": msj
                        })
                        $("#modalNewTags").modal('hide')
                        $("#modalTags").modal('hide')
                        $("#newTags-form").trigger('reset')
                        $('#contentEtiquetasMaster').jstree("destroy")

                        getEtiquetas()
                    } else if (cod == 0) {
                        Swal.fire({
                            "icon": 'error',
                            "title": msj
                        })
                    }
                }
            })
        } else {
            Swal.fire({
                "icon": 'error',
                "title": "Debe Ingresar nombre de etiqueta"
            })
        }
    })
    /* =============== MOSTRAR VENTANA MODAL PARA VISUALIZAR LOS COLORES DISPONIBLES
    * */
    $("#btn-Colores").click(function (e) {
        e.preventDefault()
        $("#modalColores").modal('toggle')
    })
    /* =============== MOSTRAR VENTANA MODAL PARA VISUALIZAR LAS TALLAS DISPONIBLES
    * */
    $("#btn-Tallas").click(function (e) {
        e.preventDefault()
        $("#modalTallas").modal('toggle')
    })

    $("#actualizarProducto").click(function () {
        let datos = new FormData(document.getElementById('form-producto'))

        $(".colores").each(function () {
            if ($(this).is(':checked')) {
                datos.append('colores[]', $(this).val())
            }
        });
        $(".tallas").each(function () {
            if ($(this).is(':checked')) {
                datos.append('tallas[]', $(this).val())
            }
        });
        // datos.append('tallas',document.getElementsByName("tallas"))
        $.ajax({
            url: "./?action=products_actualizar",
            type: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respond) {
                let t = JSON.parse(respond)
                if (t.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: t.substr(2),
                        // text: 'Something went wrong!',
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: t.substr(2),
                        // text: 'Something went wrong!',
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                }
                // console.log(respond);
            }
        })
    })
    $("#nuevoProducto").click(function () {
        $('#form-producto')[0].reset()
        $("#itbarcode").prop('disabled', false)

        $("#grabarProducto").removeClass('novisible')
        $("#grabarProducto").addClass('visible')
        $("#actualizarProducto").removeClass('visible')
        $("#actualizarProducto").addClass('novisible')

        $("#modalProducto").modal('toggle')
    })
    // grabar poroducto nuevo
    $("#grabarProducto").click(function () {
        let datos = new FormData(document.getElementById('form-producto'))

        $(".colores").each(function () {
            if ($(this).is(':checked')) {
                datos.append('colores[]', $(this).val())
            }
        });
        $(".tallas").each(function () {
            if ($(this).is(':checked')) {
                datos.append('tallas[]', $(this).val())
            }
        });
        // datos.append('tallas',document.getElementsByName("tallas"))
        $.ajax({
            url: "./?action=products_nuevo",
            type: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respond) {
                let t = JSON.parse(respond)
                if (t.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: t.substr(2),
                        // text: 'Something went wrong!',
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: t.substr(2),
                        // text: 'Something went wrong!',
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                }
                // console.log(respond);
            }
        })
    })

// Editar producto carga datos en la ventana modal
    $("#editarProveedor").click(function () {
        $("#actualizarProducto").removeClass('novisible')
        $("#actualizarProducto").addClass('visible')
        $("#grabarProducto").removeClass('visible')
        $("#grabarProducto").addClass('novisible')
        let id = $(this).attr('idProducto')
        $.ajax({
            url: "./?action=products_getDetalle",
            type: "POST",
            data: {"id": id},
            success: function (respond) {
                let t = JSON.parse(respond)
                $("#modalProducto .modal-title").text(t.product.itname)
                $("#itbarcode").val(t.product.itcodigo).prop('disabled', true)
                $("#idproducto").val(t.product.itid)
                $("#itname").val(t.product.itname)
                $("#itname").val(t.product.itname)
                $("#itdescription").val(t.product.itdescription)
                $("#itname_short").val(t.product.itname_short)
                $("#itreemplazo").val(t.product.itreemplazo).trigger('change')
                $("#ittime_abs").val(t.product.ittime_abs).trigger('change')
                $("#itranking").val(t.product.itranking).trigger('change')
                $("#paid").val(t.product.paid).trigger('change')
                $("#maidModal").val(t.product.maid).trigger('change')

                // $("#categoriaModal").val(t.product.ctid).trigger('change')
                $("#categoriaModal").html('')
                $("#subcategoriaModal").html('')

                // $("#categoriaModal").val(t.product.ctid).trigger('change')
                let optionScat = ""
                $.each(t.scategoria, function (i, r) {
                    if (t.product.ct2id == r.id) {
                        select = "selected"
                    } else {
                        select = ""
                    }
                    optionScat += "<option value='" + r.id + "' " + select + " >" + r.descrip + "</option>"
                })
                $("#subcategoriaModal").html(optionScat)

                let optionCat = ""
                $.each(t.categoria, function (i, r) {
                    if (t.product.ctid == r.id) {
                        select = "selected"
                    } else {
                        select = ""
                    }
                    optionCat += "<option value='" + r.id + "' " + select + " >" + r.name + "</option>"
                })
                $("#categoriaModal").html(optionCat)

                $("#subetiquetaModal").html('')
                let optionSetq = ""
                $.each(t.setiqueta, function (i, r) {
                    if (t.product.subetqid == r.id) {
                        select = "selected"
                    } else {
                        select = ""
                    }
                    optionSetq += "<option value='" + r.id + "' " + select + " >" + r.name + "</option>"
                })
                $("#subetiquetaModal").html(optionSetq)


                $("#unitBase").html('')
                let optionUnidad = ""
                $.each(t.unidad, function (i, r) {
                    if (t.product.unid == r.id) {
                        select = "selected"
                    } else {
                        select = ""
                    }
                    optionUnidad += "<option value='" + r.id + "' " + select + " >" + r.descrip + "</option>"
                })

                $("#unid_c").html('')
                let optionSunidad = ""
                let select1 = ""
                $.each(t.sunidad, function (i, r) {
                    if (t.product.unid_c == r.id) {
                        select1 = "selected"
                    } else {
                        select1 = ""
                    }
                    optionSunidad += "<option value='" + r.id + "' " + select1 + " >" + r.descrip + "</option>"
                })
                $("#unitBase").html(optionUnidad)

                $("#unid_c").html(optionSunidad)


                $("#etiquetaModal").html('')
                let optionEtq = ""
                if (t.product.etqid != "") {
                    /*$.each(t.etiqueta, function (i, r) {
                        if (t.product.etqid == r.id) {
                            select = "selected"
                        }else{
                            select = ""
                        }
                        optionEtq += "<option value='" + r.idCat + "' " + select + " >" + r.name + "</option>"
                    })*/
                    optionEtq = "<option value='" + t.etiqueta.idCat + "' selected>" + t.etiqueta.name + "</option>";
                }

                $("#etiquetaModal").html(optionEtq);

                if (t.product.ituse_inventory == 1) {
                    $("#itprice_in").prop("disabled", false)
                } else {
                    $("#itprice_in").prop("disabled", true)
                }


                $("#itxc_vwreport").val(t.product.itxc_vwreport)
                $("#itvidautil").val(t.product.itvidautil)
                $("#itprice_in").val(t.product.itprice_in)
                $("#itinventary_min").val(t.product.itinventary_min)
                $("#itinventary_max").val(t.product.itinventary_max)
                $("#itinventary_seg").val(t.product.itinventary_seg)
                $("#itduration").val(t.product.itduration)
                $("#itpeso").val(t.product.itpeso)
                $("#itm3").val(t.product.itm3)
                $("#itparte").val(t.product.itparte)
                $("#itnivelpel").val(t.product.itnivelpel)
                $("#itregsan").val(t.product.itregsan)
                $("#itregsan").val(t.product.itregsan)
                $("#itarancel").val(t.product.itarancel)
                $("#itesptecres").val(t.product.itesptecres)
                $("#itesptecdet").val(t.product.itesptecdet)
                // $("#itvidautil").val(t.product.itis_active)
                if (t.product.itis_active == 1) {
                    $("#itis_active").prop('checked', true)
                } else {
                    $("#itis_active").prop('checked', false)
                }
                if (t.product.itd_venta == 1) {
                    $("#itd_venta").prop('checked', true)
                } else {
                    $("#itd_venta").prop('checked', false)
                }
                if (t.product.itusalotes == 1) {
                    $("#itusalotes").prop('checked', true)
                } else {
                    $("#itusalotes").prop('checked', false)
                }
                if (t.product.ituse_inventory == 1) {
                    $("#ituse_inventory").prop('checked', true)
                } else {
                    $("#ituse_inventory").prop('checked', false)
                }
                if (t.product.ituse_ingredients == 1) {
                    $("#ituse_ingredients").prop('checked', true)
                } else {
                    $("#ituse_ingredients").prop('checked', false)
                }
                if (t.product.itusatalla == 1) {
                    $("#itusatalla").prop('checked', true)
                    $("#btn-Tallas").prop('disabled', false)
                } else {
                    $("#itusatalla").prop('checked', false)
                    $("#btn-Tallas").prop('disabled', true)
                }

                if (t.product.itin_ice == 1) {
                    $("#itin_ice").prop('checked', true)
                } else {
                    $("#itin_ice").prop('checked', false)
                }
                if (t.product.itenvio_dom == 1) {
                    $("#itenvio_dom").prop('checked', true)
                } else {
                    $("#itenvio_dom").prop('checked', false)
                }
                if (t.product.itenvio_dom == 1) {
                    $("#itenvio_dom").prop('checked', true)
                } else {
                    $("#itenvio_dom").prop('checked', false)
                }
                if (t.product.itis_favorite == 1) {
                    $("#itis_favorite").prop('checked', true)
                } else {
                    $("#itis_favorite").prop('checked', false)
                }
                if (t.product.itusacolores == 0) {
                    $("#btn-Colores").prop('disabled', true)
                    $("#itusacolores").prop('checked', false)
                } else {
                    $("#btn-Colores").prop('disabled', false)
                    $("#itusacolores").prop('checked', true)
                }
                if (t.product.itin_iva == 1) {
                    $("#itin_iva").prop('checked', true)
                } else {
                    $("#itin_iva").prop('checked', false)
                }
                if (t.product.itis_ingredient == 1) {
                    $("#itis_ingredient").prop('checked', true)
                } else {
                    $("#itis_ingredient").prop('checked', false)
                }
                if (t.product.ituse_inventory == 1) {
                    $("#itprice_in").prop('disabled', false)
                } else {
                    $("#itprice_in").prop('disabled', true)
                }

                // valida los colores asociados al producto
                $("#contentColores").html('')
                let coloresContent = ""
                $.each(t.colores, function (i, r) {
                    let checked = ""
                    let colorID = t.product.coloresid.filter(colores => colores == r.id);
                    if (colorID != "") {
                        checked = "checked"
                    }
                    coloresContent += '<div class="checkbox list-group-item " style="display: flex; justify-content: space-between">' +
                        '<label><input type="checkbox" ' + checked + ' name="colores[]" class="colores" value="' + r.id + '" >' + r.coname + '</label>' +
                        '<input type="color" class="input-sm" value="' + r.cocolor + '" >' +
                        '</div>';
                })

                $("#contentColores").html(coloresContent)

                // valida las tallas asociadas al producto
                $("#contentTallas").html('')
                let tallasContent = ""
                $.each(t.tallas, function (i, rt) {
                    let checked = ""
                    let tallaID = t.product.tallasid.filter(tallas => tallas == rt.id);
                    if (tallaID != "") {
                        checked = "checked"
                    }
                    tallasContent += '<div class="checkbox list-group-item " style="display: flex; justify-content: space-between">' +
                        '<label><input type="checkbox" name="tallas[]" class="tallas" value="' + rt.id + '" ' + checked + ' >' + rt.taname + '</label>' +
                        '<label>' + rt.tdescrip + '</label>' +
                        '</div>';
                })
                $("#contentTallas").html(tallasContent)

            }
        })
        $("#modalProducto").modal('toggle')
    })
    // valida si el producto maneja inventario o no
    $("#ituse_inventory").click(function () {
        if ($(this).is(':checked')) {
            $("#itprice_in").prop("disabled", false)
        } else {
            $("#itprice_in").prop("disabled", true)
        }
    })
    //valida si el producto maneja tallas
    $("#itusatalla").click(function () {
        if ($(this).is(':checked')) {
            $("#btn-Tallas").prop("disabled", false)
        } else {
            $("#btn-Tallas").prop("disabled", true)
        }
    })
    //valida si el producto maneja colores
    $("#itusacolores").click(function () {
        if ($(this).is(':checked')) {
            $("#btn-Colores").prop("disabled", false)
        } else {
            $("#btn-Colores").prop("disabled", true)
        }
    })

    $("#unitBase").change(function () {
        let valor = $(this).val()
        let sub = ''
        loadUnitC(valor, sub)
    })

    function loadUnitC(base, compra) {
        let option = 8
        let respuesta = 0
        $.ajax({
            url: './?action=loadDataProds',
            type: 'POST',
            data: {option: option, UndBase: base},
            success: function (dato) {
                let res = JSON.parse(dato)
                let viewHtml = '<option value="">Seleccione unidad...</option>'
                let selected = ""
                $.each(res, function (i, item) {
                    if (compra != '') {
                        if (compra == item.id) {
                            selected = "selected"
                        } else {
                            selected = ""
                        }
                    }
                    viewHtml += '<option value="' + item.id + '" ' + selected + ' >' + item.name + '</option>'
                });
                $("#unid_c").html(viewHtml)
            }
        })
    }

    loadUnit()

    // console.log("productos")
    function loadUnit() {
        let unit_val = 0
        if (document.getElementById('unitBaseVal')) {
            unit_val = $("#unitBaseVal").val()
        }
        let option = 1
        let respuesta = 0
        $.ajax({
            url: './?action=loadDataProds',
            type: 'POST',
            data: {option: option},
            success: function (dato) {
                let res = JSON.parse(dato)
                let viewHtml = '<option value="">Seleccione unidad...</option>'
                $.each(res, function (i, item) {
                    let selected = ""
                    if (item.base == 0) {
                        if (unit_val != '') {
                            if (unit_val == item.id) {
                                selected = "selected"
                            }
                        }
                        viewHtml += '<option value="' + item.id + '" ' + selected + '>' + item.name + ' </option>'
                    }
                });
                $("#unitBase").html(viewHtml)
            }
        })
    }

    $("#etiquetaModal").change(function (event) {
        let idetiqueta = $(this).val()
        let stq = 0
        loadSubEti(stq, idetiqueta)
    })

    function loadSubEti(idSetiq, etq) {
        let respuesta = 0
        $.ajax({
            url: './?action=loadDataProds',
            type: 'POST',
            data: {option: 7, idetq: etq},
            success: function (dato) {

                let res = JSON.parse(dato)
                let selected = ''
                let viewHtml = '<option value="">Seleccione subetiqueta...</option>'
                $.each(res, function (i, item) {

                    if (idSetiq == item.id) {
                        selected = "selected"
                    } else {
                        selected = ''
                    }

                    viewHtml += '<option value="' + item.id + '" ' + selected + ' >' + item.name + '</option>'
                });
                $("#subetiquetaModal").html(viewHtml)
            }
        })
    }

    loadEtiquetas()

    function loadEtiquetas() {
        let varConfig = $("#configInv").val()
        if (varConfig == "") {
            Swal.fire({
                icon: 'error',
                title: "Se debe configurar etiqueta de inventario",
            })
        } else {
            let etqVal = ''
            if (document.getElementById('cateti')) {
                etqVal = $("#cateti").val()
            }

            $.ajax({
                url: './?action=loadDataProds',
                type: 'POST',
                data: {option: 5, varConfig: varConfig},
                success: function (resultado) {
                    console.log(resultado)
                    let res = JSON.parse(resultado)
                    let viewHtml = '<option value="">Seleccione etiqueta...</option>'
                    let selected = ""
                    $.each(res, function (i, item) {

                        if (etqVal.length != 0) {
                            if (etqVal == item.id) {
                                selected = "selected"
                            } else {
                                selected = ''
                            }
                        }

                        viewHtml += '<option value="' + item.id + '" ' + selected + ' >' + item.name + '</option>'
                    });
                    $("#etiquetaModal").html(viewHtml)
                }
            })
        }
    }

    loadMarcas()

    function loadMarcas() {
        let idmaid = 0
        if (document.getElementById('idmaid')) {
            idmaid = $("#idmaid").val()
        }

        $.ajax({
            url: './?action=loadDataProds',
            type: 'POST',
            data: {option: 6},
            success: function (resultado) {
                let res = JSON.parse(resultado)
                let viewHtml = '<option value="">Seleccione marca...</option>'
                $.each(res, function (i, item) {
                    let select = ''
                    if (idmaid == item.id) {
                        select = "selected"
                    } else {
                        select = ""
                    }
                    viewHtml += '<option value="' + item.id + '" ' + select + ' >' + item.name + '</option>'
                });
                $("#maidModal").html(viewHtml)
            }
        })
    }

    let obj_cat = function loadCategoria() {
        let catVal = ''
        if (document.getElementById('catEdit')) {
            catVal = $("#catEdit").val()
        }
        $.ajax({
            url: './?action=loadDataProds',
            type: 'POST',
            data: {option: 3},
            success: function (resultado) {
                let res = JSON.parse(resultado)
                let viewHtml = '<option value="">Seleccione categoria...</option>'
                let selected = ''
                $.each(res, function (i, item) {
                    if (catVal.length != 0) {
                        if (catVal == item.id) {
                            selected = "selected"
                        } else {
                            selected = ''
                        }
                    }
                    viewHtml += '<option value="' + item.id + '" ' + selected + ' >' + item.name + '</option>'
                });
                $("#categoriaModal").html(viewHtml)
            }
        })
    }

    obj_cat() /* FUNCION QUE EJECUTA LA CARGA YY VISUALIZACION DE LAS CATEGORAS */

    $("#categoriaModal").change(function (event) { /* PROCESO QUE VISUALIZA LAS SUBCATEGORIAS DE ACUERDO A LA SELECCION DE LA CATEGORIA*/
        event.preventDefault()
        let valor = $(this).val()
        let sub = ''
        loadSubCate(valor, sub)
    })

    if (document.getElementById('subcatEdit')) {
        let sub = $("#subcatEdit").val()
        let valor = $("#catEdit").val()
        loadSubCate(valor, sub)
    }

    function loadSubCate(valor, sub) {
        let respuesta = 0
        $.ajax({
            url: './?action=loadDataProds',
            type: 'POST',
            data: {option: 4},
            success: function (dato) {
                let res = JSON.parse(dato)
                let viewHtml = '<option value="">Seleccione subcategoria...</option>'
                $.each(res, function (i, item) {
                    let selected = ''
                    if (item.cat == valor) {
                        //     if (sub != '') {
                        if (item.id == sub) {
                            selected = "selected"
                        }
                        // }
                        viewHtml += '<option value="' + item.id + '" ' + selected + ' >' + item.name + '</option>'
                    }
                });
                $("#subcategoriaModal").html(viewHtml)
            }
        })
    }

    // obtiene las etiquetas de configuracion de los productos
    function getEtiquetasConfig() {
        let etqConfig = $("#configInv").val()
        $.ajax({
            url: './?action=clientes_etiquetas',
            type: 'POST',
            data: {
                "option": 3, "id": etqConfig
            },
            success: function (respond) {
                let res = JSON.parse(respond)
                let viewHTML = ''
                $.each(res, function (i, item) {
                    viewHTML += '<div><i class="fa fa-tag" aria-hidden="true"></i><label>&nbsp;<input ' +
                        'type="checkbox" class="chkEtiqueta checkSize" value="' + item.id + '" data-id="chk-' + item.id + '"> ' + item.name + '</label></div>'
                })
                $("#contentCheckBox").html(viewHTML)
            }
        })
    }

    // obtiene las marcas de los productos
    function getMarcas() {
        // let etqConfig = $("#configInv").val()
        $.ajax({
            url: './?action=listaprecios_marcas',
            type: 'POST',
            success: function (respond) {
                let res = JSON.parse(respond)
                let viewHTMLmarcas = '<ul>'
                viewHTMLmarcas += '<li class="chkEtiqueta" id="todosm"><b>Todos</b><ul>'
                $.each(res, function (i, item) {
                    viewHTMLmarcas += '<li id="' + item.id + '" class="chkEtiqueta chkMarcas" style="font-size: 10px!important;">' + item.name + '</li>'
                })
                viewHTMLmarcas += '</ul>'
                viewHTMLmarcas += '</li>'
                viewHTMLmarcas += '</ul>'

                $("#contentCheckBoxMarcas").html(viewHTMLmarcas)
                $('#contentCheckBoxMarcas').jstree({
                    "plugins": ["checkbox", "dnd", "massload", "search", "sort", "state", "types", "unique", "wholerow", "changed", "conditionalselect"]
                });
            }
        })
    }

    $("#contentCheckBoxMarcas").click('changed.jstree', function (e, data) {
        validaDatosCheckbox()
    })
    $("#contentCheckBoxCategorias").click('changed.jstree', function (e, data) {
        validaDatosCheckbox()
    })
    $("#contentEtiquetasMaster").click('changed.jstree', function (e, data) {
        validaDatosCheckbox()
    })

    function validaDatosCheckbox() {
        if (getValCheckEtiquetas() != '' || getValCheckEtqConfig() != "" || getValCheckMarcas() != "" || getValCheckCategorias()) {
            $('#tbody-productos').empty();
            pagina = 1
            option = '';
            // console.log(validaRadioSeccion())
            loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), validaRadioSeccion(), $("#buscarProducto").val())
        } else {
            $('#tbody-productos').empty();
            pagina = 1
            option = '';
            /** FUNCION PARA QUE RECIBE PRIMERO LAS ETIQUETAS DE CLASIFICACION Y LAS ETIQUETAS DE CONFIGURACION */
            loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), validaRadioSeccion(), $("#buscarProducto").val())
        }
        getFirstProducto()

    }

    $('#medio').blur(function () {
        $('#tbody-productos').empty();
        pagina = 1
        option = '';
        loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), validaRadioSeccion(), $("#buscarProducto").val())
    })
    $('#normal').blur(function () {
        $('#tbody-productos').empty();
        pagina = 1
        option = '';
        loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), validaRadioSeccion(), $("#buscarProducto").val())
    })
    $('#critico').blur(function () {
        $('#tbody-productos').empty();
        pagina = 1
        option = '';
        loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), validaRadioSeccion(), $("#buscarProducto").val())
    })

    // obtiene las categorias de los productos
    function getCategorias() {
        // let etqConfig = $("#configInv").val()
        $.ajax({
            url: './?action=listaprecios_categorias',
            type: 'POST',
            success: function (respond) {
                let res = JSON.parse(respond)
                let viewHTMLmarcas = '<ul>'
                viewHTMLmarcas += '<li class="chkEtiqueta" id="todosm"><b>Todos</b><ul>'
                $.each(res.categorias, function (i, item) {
                    viewHTMLmarcas += '<li class="chkEtiqueta" id="c-' + item.idCat + '" style="font-size: 10px!important;">' + item.categoria + ''
                    if (parseInt(item.hijos) >= 1) {
                        viewHTMLmarcas += '<ul>'
                        $.each(res.scategorias, function (t, tar) {
                            if (tar.ctid == item.idCat) {
                                viewHTMLmarcas += '<li data-chk="etiqueta" class="chkEtiqueta " id="s-' + tar.idCat2 + '">' + tar.scategoria + '</li>';
                            }
                        })
                        viewHTMLmarcas += '</ul>'
                    }
                    viewHTMLmarcas += '</li>'
                })
                viewHTMLmarcas += '</ul>'
                viewHTMLmarcas += '</li>'
                viewHTMLmarcas += '</ul>'
                $("#contentCheckBoxCategorias").html(viewHTMLmarcas)
                $('#contentCheckBoxCategorias').jstree({
                    "plugins": ["checkbox", "dnd", "massload", "search", "sort", "state", "types", "unique", "wholerow", "changed", "conditionalselect"]
                });
            }
        })
    }

    getCategorias()
    getMarcas()
    getEtiquetasConfig()

    // obtiene las etiquetas de clasificacion de los productos
    function getEtiquetas() {
        let option = 1
        $.ajax({
            url: './?action=listaprecios_etiquetas',
            type: 'POST',
            data: {'option': option},
            success: function (e) {
                let r = JSON.parse(e)
                let etiqButton = '<ul>'
                // etiqButton += ''
                etiqButton += '<li id="todos"><b>Todos</b><ul>'
                r.forEach(function (data, index) {
                    etiqButton += '<li  class="chkEtiqueta " id="' + data.id + '">' + data.text + '';
                    if (!(data.hijos === null)) {
                        etiqButton += '<ul>'
                        $.each(data.hijos, function (t, tar) {
                            etiqButton += '<li data-chk="etiqueta" class="chkEtiqueta " id="' + tar.id + '">' + tar.text + '</li>'
                        })
                        etiqButton += '</ul>'
                    }
                    etiqButton += '</li>';
                })
                etiqButton += '</ul>'
                $("#contentEtiquetasMaster").html(etiqButton)
                $('#contentEtiquetasMaster').jstree({
                    "plugins": ["checkbox", "dnd", "massload", "search", "sort", "state", "types", "unique", "wholerow", "changed", "conditionalselect"]
                });
            }
        })
    }

    getEtiquetas()

    var pagina = 1

    let option = '';

    $(document).on('click', '.radioSeccion', function () {
        if ($(this).is(':checked')) {
            $('#tbody-productos').empty();
            pagina = 1
            option = '';
            loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), $(this).val(), $("#buscarProducto").val())
        }
    })

    // muestras los productos en la tabla principal
    $(document).on("keyup", '#buscarProducto', function (event) {
        var codigo = event.key;
        if (codigo === 8 || codigo === 46) {
            loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), validaRadioSeccion(), $(this).val())
        }
        if ($(this).val() != "") {
            $('#tbody-productos').empty();
            pagina = 1
            option = '';
            loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), validaRadioSeccion(), $(this).val())
        } else {
            $('#tbody-productos').empty();
            pagina = 1
            option = '';
            loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), validaRadioSeccion(), $(this).val())
        }
        getFirstProducto()

    })

    function validaRadioSeccion() {
        let r = ''
        $(".radioSeccion").each(function () {
            if ($(this).is(':checked')) {
                r = $(this).val()
            }
        })
        return r
    }

    let ultimoProducto;
    let observador = new IntersectionObserver((entradas, observador) => {
        entradas.forEach(entrada => {
            if (entrada.isIntersecting) {
                pagina++
                loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), validaRadioSeccion(), $("#buscarProducto").val())
            }
        })

    }, {
        rootMargin: '0px 0px 0px 0px',
        threshold: 1.0
    })

    loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), validaRadioSeccion(), $("#buscarProducto").val())

    function getColorStatus(valor) {
        let style = ""
        let m = $("#medio").val()
        let n = $("#normal").val()
        let c = $("#critico").val()
        if (parseFloat(valor) <= parseFloat(c)) {
            style = "background : rgba(252, 7, 7,.3)"
        } else if (parseFloat(valor) > parseFloat(c) && parseFloat(valor) <= parseFloat(m)) {
            style = "background : rgba(216, 254, 0,.3)"
        } else if (parseFloat(valor) > parseFloat(n)) {
            style = "background : rgba(2, 150, 58 ,.3)"
        }
        return style
    }

    function loadProductosData(etqClasificacion, etqConfiguracion, marcas, categorias, opciones, busqueda) {  // Funcion para cargar la lista de Proveedores

        $.ajax({
            type: 'POST',
            url: './?action=products_listar',
            data: {
                "option": 1,
                "pagina": pagina,
                "productoPorPagina": 15,
                "etiquetas": etqClasificacion,
                "etqConfig": etqConfiguracion,
                "marcas": marcas,
                "categorias": categorias,
                "opciones": opciones,
                "busqueda": busqueda
            },
            success: function (e) {
                let r = JSON.parse(e);
                console.log(r)
                if (r.data != "") {
                    const numPrecios = $(".valoresNumerico")
                    let totalPreciosInput = numPrecios.length
                    for (let o = 1; o <= totalPreciosInput; o++) {
                        $(".namePrice" + o).val(r.precios['precio' + o])
                    }

                    r.data.forEach(function (item, dato) {
                        let etq = ""
                        if (item.etiquetas != "") {
                            $.each(item.etiquetas, function (i, y) {
                                etq += '<span class="badge">' + y.etqName + '</span>'
                            })
                        }
                        option += '<button type="button" data-etiquetas="' + item.idsEtq + '" data-id="' + item.itid + '" data-name="' + item.name + '" class="list-group-item lista-proveedores-item productoData"><div style="display:flex;flex-direction: column"><span>' + item.name + '</span> <span>' + item.codigo + '</span><span><b>' + item.unidad + '</b></span></div> ' + etq + '  </button>';
                    })
                    let totProductos = r.tp
                    $("#totalProductos").text("Total de productos : " + r.tp)
                    $('#mensajes').html(option);

                    let totalProvee = document.querySelectorAll('.productoData')

                    if (parseInt(totProductos) > parseInt(totalProvee.length)) {
                        if (ultimoProducto) {
                            observador.unobserve(ultimoProducto);
                        }
                        const productosVisibles = document.querySelectorAll('.productoData')
                        ultimoProducto = productosVisibles[productosVisibles.length - 1]
                        observador.observe(ultimoProducto)
                    }
                    getFirstProducto()

                } else {
                    option += '<tr style="background-color: rgba(117, 117, 117,.4)"><td colspan="14"><b>SIN PRODUCTOS ASOCIADOS</b></td></tr>';
                    $('#tbody-productos').html(option);
                    $("#totalProductos").text("Total de productos : 0 ")
                }
            }
        })
    }

    $('#etiquetas').select2();

    $(document).on('click', '.lista-proveedores-item', function () {
        $('.lista-proveedores-item').removeClass('active')
        if ($(this).hasClass('active')) {
            $(this).removeClass('active')
        } else {
            $(this).addClass('active')
        }
    })

    // doble clic para etiquetar los productos
    $(document).on('dblclick', '.productoData', function () {
        $("#modalEtiquetar").modal('toggle')
        let etiqs = $(this).data('etiquetas')
        let str = etiqs.toString()
        if (str.indexOf(",") > -1) {
            let r = etiqs.split(',');
            $('#etiquetas').val(r).trigger('change');
        } else {
            $('#etiquetas').val(etiqs).trigger('change');
        }
        console.log($(this).attr('data-id'))
        $("#modalEtiquetar #idProveedore").val($(this).attr('data-id'))
        $("#modalEtiquetar .modal-title").text($(this).children('div').find('span:first-child').text())
    })

    $(document).on('click', "#asignarEtiquetas", function () {
        let dataNewTags = $("#formAsignacion").serialize()
        $.ajax({
            url: './?action=addTags',
            type: 'POST',
            data: $("#formAsignacion").serialize(),
            success: function (respond) {
                // console.log(res)
                let res = JSON.parse(respond)
                let cod = res.substr(0, 1);
                let msj = res.substr(2)
                if (cod == 1) {
                    Swal.fire({
                        "icon": 'success',
                        "title": msj
                    })
                    $('#mensajes').html('');
                    loadProductosData(getValCheckEtiquetas(), getValCheckEtqConfig(), getValCheckMarcas(), getValCheckCategorias(), validaRadioSeccion(), $("#buscarProducto").val())

                    $("#modalEtiquetar").modal('toggle')
                    // $("#newTags-form").trigger('reset')
                } else if (cod == 0) {
                    Swal.fire({
                        "icon": 'error',
                        "title": msj
                    })
                }
            }
        })
    })

    /** VALIDA LA SELECCION DE ETIQUETAS DE CLASIFICACION */
    function getValCheckEtiquetas() {
        let checked_ids = [];
        let selectedNodes = $('#contentEtiquetasMaster').jstree("get_checked", true);
        $.each(selectedNodes, function () {
            checked_ids.push(this.id);
        });
        return checked_ids.toString()
    }

    /** VALIDA LA SELECCION DE MARCAS */

    function getValCheckMarcas() {
        let checked_ids = [];
        let selectedNodes = $('#contentCheckBoxMarcas').jstree("get_checked", true);
        $.each(selectedNodes, function () {
            checked_ids.push(this.id);
        });
        return checked_ids.toString()
    }

    /** VALIDA LA SELECCION DE CATEGORIAS */

    function getValCheckCategorias() {
        let checked_ids = [];
        let selectedNodes = $('#contentCheckBoxCategorias').jstree("get_checked", true);
        $.each(selectedNodes, function () {
            checked_ids.push(this.id);
        });
        return checked_ids.toString()
    }

    /** VALIDA LA SELECCION DE ETIQUETAS DE CONFIGURACION */
    function getValCheckEtqConfig() {
        let chketconf = [];
        let arr = $('#contentCheckBox .chkEtiqueta:checked').map(function () {
            return this.value;
        }).get();
        return arr
    }

    /** SE CARGAN LOS PRECIOS EN EL CONTENERO DERECHO PARA PODER EDITAR */
    function creaProductoQR(nameProducto) {
        $("#logoProductos").text('')
        let qrcode = new QRCode(document.getElementById("logoProductos"), {
            width: 100,
            height: 100
        });
        qrcode.makeCode(nameProducto);
    }

    // seleccion de producto para visualizar sus descripcion del lado derecho

    function getFirstProducto() {
        $('.productoData:first').addClass('active')
        let id = $('.lista-proveedores-item:first').data('id')
        getDataProductoDetalle(id)
    }

    $(document).on("click", ".productoData", function () {
        let id = $(this).attr('data-id')
        $("#eliminarProducto").attr('nameProducto', $(this).attr('data-name'))
        $("#eliminarProducto").attr('idproducto', id)
        getDataProductoDetalle(id)
    })

    function getDataProductoDetalle(id) {
        $.ajax({
            url: "./?action=products_getDetalle",
            type: "POST",
            data: {"id": id},
            success: function (respond) {
                // console.log(respond)
                let t = JSON.parse(respond)
                if (t.product.itimage === null || t.product.itimage == "") {
                    $("#imagenProducto").attr("src", "storage/logo/producto.png");
                } else {
                    $("#imagenProducto").attr("src", "storage/productos/" + t.product.itimage);
                }

                $("#tituloProductoData").text(t.product.itname)

                $("#editarProveedor").attr('idProducto', t.product.itid)
                creaProductoQR(t.product.itname) // muestra codigo qr del nombre del producto
                $("#razon").text(t.product.itcodigo + " - " + t.product.itname)
                switch (t.product.ittime_abs) {
                    case "A":
                        $("#rankingRotacion").text("Alta rotación")
                        break;
                    case "B":
                        $("#rankingRotacion").text("Moderada rotación")
                        break;
                    case "C":
                        $("#rankingRotacion").text("Baja rotación")
                        break;
                    default:
                        $("#rankingRotacion").text("")
                        break;
                }
                switch (t.product.itranking) {
                    case "A":
                        $("#rankingCosto").text("Alto costo")
                        break;
                    case "B":
                        $("#rankingCosto").text("Moderado costo")
                        break;
                    case "C":
                        $("#rankingCosto").text("Bajo costo")
                        break;
                    default:
                        $("#rankingCosto").text("")
                        break;
                }
                /**<select name="itranking" id="itranking" class="form-control input-sm " nombre="Ranking costos ABC">
                 <option value="">Seleccione ranking..</option>
                 <option value="A">(A) Alto costo</option>
                 <option value="B">(B) Moderado costo</option>
                 <option value="C">(C) Bajo costo</option>
                 </select>
                 */
                /**<select name="ittime_abs" id="ittime_abs" class="form-control input-sm " nombre="Ranking rotación ABC">
                 <option value="">Seleccione ranking..</option>
                 <option value="A">(A) Alta rotación</option>
                 <option value="B">(B) Moderada rotación</option>
                 <option value="C">(C) Baja rotación</option>
                 </select>
                 * */
                $("#Und_Compra").text(t.product.nameUnidad + " / " + t.product.nameUC)

                $("#marca").text(t.product.maname)
                $("#etiqueta").text(t.product.etiqueta)
                $("#setiqueta").text(t.product.setiqueta)
                $("#numeroStock").text(t.product.stock)
                $("#catSubCat").text(t.product.ctname + " / " + t.product.ct2name)
                /*$("#categoria").text(t.product.ctname)
                $("#scategoria").text(t.product.ct2name)*/

                $("#nombreCorto").text(t.product.itname_short)
                $("#descripcion").text(t.product.itdescription)
                if (t.product.itis_active == 1) {
                    $("#estado").text("Activo")
                } else {
                    $("#estado").text("Inactivo")
                }
                if (t.product.itd_venta == 1) {
                    $("#disponibleventa").text("SI")
                } else {
                    $("#disponibleventa").text("NO")
                }
                if (t.product.ituse_ingredients == 1) {
                    $("#usaIngrediente").text("SI")
                } else {
                    $("#usaIngrediente").text("NO")
                }
                if (t.product.ituse_inventory == 1) {
                    $("#usaInventario").text("SI")
                } else {
                    $("#usaInventario").text("NO")
                }
                if (t.product.itin_iva == 1) {
                    $("#grabaIva").text("SI")
                } else {
                    $("#grabaIva").text("NO")
                }
                /*usaLote
receta
ingrediente*/
                if (t.product.itusalotes == 1) {
                    $("#usaLote").text("SI")
                } else {
                    $("#usaLote").text("NO")
                }
                if (t.product.ituse_ingredients == 1) {
                    $("#receta").text("SI")
                } else {
                    $("#receta").text("NO")
                }
                if (t.product.itis_ingredient == 1) {
                    $("#ingrediente").text("SI")
                } else {
                    $("#ingrediente").text("NO")
                }
            }
        })
    }

    $(document).on('change', '.inputValor', function () {
        let valordb = $(this).attr('valordb')
        let valor = $(this).val()
        let numLista = $(this).attr('num')
        let idproducto = $("#idProducto").val()
        if (parseFloat(valor) !== parseFloat(valordb)) {
            updatePrecio(valor, numLista, idproducto)
        }
    })

    /** FUNCION PARA ACTUALIZAR EL PRECIO MODIFCADO */

    function updatePrecio(precio, lista, idproducto) {
        $.ajax({
            url: './?action=listaprecios_editaprecio',
            type: 'POST',
            data: {"precio": precio, "lista": lista, "idproducto": idproducto},
            success: function (e) {
                let r = JSON.parse(e)
                if (r.substr(0, 1) == 1) {
                    Swal.fire({
                        icon: 'success',
                        // title: 'Oops...',
                        text: r.substr(2),
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                    $(".namePrice" + lista).val(parseFloat(precio).toFixed(2) + "%")
                } else {
                    Swal.fire({
                        icon: 'error',
                        // title: 'Oops...',
                        text: r.substr(2),
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                }
                // console.log(e);
            }
        })
    }

    $(".valoresNumericos").numeric({decimalPlaces: 4});
    $(".valoresNumerico").numeric({decimalPlaces: 2});
// target a los inputs con la clase observacion
    // usando spread operator para hacerlo iterable(ES6 feature)
    const $inputs = [...document.getElementsByClassName('inputValor')]

    // listener al evento click de cada input
    // para recobrar el borde y remover readonly (mientras se edita)
    $inputs.forEach(i => {
        i.addEventListener('click', function () {
            this.style.border = 'inherit'
            this.removeAttribute('readonly')
        })
    })

    // listener al evento blur de cada input
    // para volver a quitar el border cuando se sale del focus del input
    // y volver a darle el attributo readonly
    $inputs.forEach(i => {
        i.addEventListener('blur', function () {
            this.style.border = 'none'
            this.setAttribute("readonly", true);
            /*if (  this.value != ' '){
                this.onfocus
            }else*/
            if (this.getAttribute('precio') != this.value) {
                this.classList.add("valor-editado");
            }
        })
    })
})
