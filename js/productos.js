if (document.getElementById('productos')) {
    $(document).ready(function () {

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
        $(document).on("keyup", "#itbarcode", function () {
            this.value = this.value.toUpperCase();
        })
        $(document).on("keyup", "#nombreProducto", function () {
            this.value = this.value.toUpperCase();
        })
        /* ================ FUNCION PARA VISUALZIAR LA SUBUNIDAD (UNIDAD PARA SETEAR LA UNIDAD DE COMPRA ) ======== */
        $("#unitBase").change(function () {
            let valor = $(this).val()
            let sub = ''
            loadUnitC(valor, sub)
        })
        /** PARA PROCESO DE EDICION */
        if (document.getElementById('unitCbaseVal')) {
            let unidHc = $("#unitCbaseVal").val() // unidad de compra habitual
            let unidBase = $("#unitBaseVal").val() // unidad Base
            loadUnitC(unidBase, unidHc)
        }

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

        /* ================ FUNCION PARA VISUALZIAR LAS ETIQUETAS  ======== */
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
                        $("#etiqueta").html(viewHtml)
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
                    $("#maid").html(viewHtml)
                }
            })
        }


        $("#etiqueta").change(function (event) {
            let idetiqueta = $(this).val()
            let stq = 0
            loadSubEti(stq, idetiqueta)
        })

        if (document.getElementById('subcateti')) {
            // cateti
            // subcateti
            let idSetiq = $("#subcateti").val()
            let idEtq = $("#cateti").val()
            loadSubEti(idSetiq, idEtq)
        }

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
                    $("#subetiqueta").html(viewHtml)
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
                    $("#categoria").html(viewHtml)
                }
            })
        }

        obj_cat() /* FUNCION QUE EJECUTA LA CARGA YY VISUALIZACION DE LAS CATEGORAS */

        $("#categoria").change(function (event) { /* PROCESO QUE VISUALIZA LAS SUBCATEGORIAS DE ACUERDO A LA SELECCION DE LA CATEGORIA*/
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
                    $("#subcategoria").html(viewHtml)
                }
            })
        }

        /*==============================================================
                MUESTRA LA VENTANA PARA ETIQUETAR MEDIANTE TAGS
        * =============================================================*/
        $(document).on('click', '#btn-tags-pro', function (e) {
            e.preventDefault()
            $("#modalTags").modal('toggle')
            let table = $("#table-new").val()

            e.preventDefault()
            let id = 0
            let producto = 0
            let tipo = 1
            $.ajax({
                url: '?action=loadTags',
                type: 'POST',
                data: {id: id, table: table, tipo: tipo},
                success: function (responde) {
                    let data = JSON.parse(responde)
                    let i = 0
                    let tags = ''
                    tags += '<ul class="list-group list-group-sm">'
                    $.each(data.etiquetas, function (i, item) {
                        let chk = ''
                        let nodoName = ''
                        let nodo = ''
                        if (item.nodoName != '') {
                            nodo = '<b> / ' + item.nodoName + '</b>'
                        }
                        if (Number(item.checked == 1)) {
                            chk = 'checked'
                        }
                        tags += '<div class="checkbox list-group-item "><label><input type="checkbox" name="tags-list[]" class="tags-list" value="' + item.id + '" ' + chk + '> ' + item.name + nodo + '</label></div>'
                    });
                    tags += '</ul>'
                    $("#tags-id").html(tags)
                }
            })

            $("#modalTags #idproveedorTags").val(id)
            $("#aplicar-btn").css("display", "none")
            $("#nueva-btn").css("display", "block")
            $("#cerrar-btn").css("display", "block")

        })


        $("#aplicar-btn-tags").click(function (event) {
            event.preventDefault()
            let tags = ''
            $('#form-new-tags input[type=checkbox]').each(function () {
                if (this.checked) {
                    tags += $(this).val() + '-';
                }
            });
            $.ajax({
                url: './?action=loadTags',
                type: 'POST',
                data: {tipo: 10, tags: tags},
                success: function (e) {
                    let res = JSON.parse(e)
                    let tags = ''
                    tags += '<ul class="list-group list-group-sm" id="tags-group">'
                    $.each(res, function (i, item) {
                        tags += '<div class="btn-group" style="padding-right: 0.5rem">\n' +
                            '              <button type="button" class="btn btn-btn-p btn-sm tagsName" value="' + item.id + '" style="font-size: 10px" >' + item.name + '</button>\n' +
                            '              <button type="button" class="btn btn-btn-p btn-sm dropdown-toggle btn-tags-close" name="tags[]" value="' + item.id + '" style="font-size: 10px" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\n' +
                            '                <span>x</span>\n' +
                            '              </button>\n' +
                            '            </div>'
                    });
                    tags += '</ul>'
                    $("#tags").html(tags)
                }
            })
        })

        $("#grabar-producto").click(function (e) {
            e.preventDefault()
            let formDatos = new FormData(document.getElementById('form-producto'))

            $("#tags-group .tagsName").each(function () { /* Recorro la tabla de las formas de pago para tomar el id de la forma de pago y el valor de este */
                let row = $(this)
                let valor = row.val()
                formDatos.append("tags[]", valor);
            });
            let colores = ''
            /* ======== FOREACH PARA RECORRER LA TABLA DE LOS COLORES Y ASOCIAR CON EL PRODUCTO CREADO
            * */
            $('#form-colores input[type=checkbox]').each(function () {
                if (this.checked) {
                    colores += $(this).val() + '-';
                }
            });
            formDatos.append("colores", colores);

            /* ======== FOREACH PARA RECORRER LA TABLA DE LOS COLORES Y ASOCIAR CON EL PRODUCTO CREADO
            * */
            let tallas = ''
            $('#form-tallas input[type=checkbox]').each(function () {
                if (this.checked) {
                    tallas += $(this).val() + '-'
                }
            });
            formDatos.append("tallas", tallas)

            let obj = validaSinValor()
            if (obj.band == false) {
                ProcessProduct(formDatos)

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos incompletos...',
                    html: obj.lista,
                })
            }


        })

        function ProcessProduct(dataFormulario) {
            $.ajax({
                url: './?action=processProduct',
                type: 'POST',
                data: dataFormulario,
                contentType: false,
                processData: false,
                success: function (respuesta) {
                    console.log(respuesta)
                    let res = JSON.parse(respuesta)
                    if (res.substr(0, 1) == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: res.substr(2),
                            timer: 2000
                        })
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: res.substr(2),
                            timer: 2000
                        })
                        location.href = './?view=products';
                    }
                }
            })
        }


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

        $('#paid').select2();
        /** SELECT DE PASISES */
        $('#itreemplazo').select2(); /** SELECT DE PRODUCTO DE REEMPLAZOS*/

        /* ================ SE VALIDA EL CHECK DE PARA LA ACTIVACION DE BOTON MODAL DE COLORES
        * */
        $("#itusacolores").click(function () {
            if ($(this).is(":checked")) {
                $("#btn-Colores").removeAttr('disabled')
            } else {
                $("#btn-Colores").attr('disabled', true)
            }
        })
        /* ================ SE VALIDA EL CHECK DE PARA LA ACTIVACION DEL CAMPO PARA INGRESAR EL COSTO EN CASO DE REQUERIRLO
        * */
        $("#ituse_inventory").click(function () {
            if ($(this).is(":checked")) {
                $("#input-costo").attr('disabled', true)
            } else {
                $("#input-costo").removeAttr('disabled')
            }
        })
        /* ================ SE VALIDA EL CHECK DE PARA LA ACTIVACION DE BOTON MODAL DE TALLAS
        * */
        $("#itusatalla").click(function () {
            if ($(this).is(":checked")) {
                $("#btn-Tallas").removeAttr('disabled')
            } else {
                $("#btn-Tallas").attr('disabled', true)
            }
        })

        /*loadEtiqueta()

         function loadEtiqueta() {
             let valCat = $("#catEdit").val()
             $.ajax({
                 url: './?action=loadDataProds',
                 type: 'POST',
                 data: {option: 3},
                 success: function (resultado) {
                     let res = JSON.parse(resultado)
                     let viewHtml = '<option value="">Seleccione categoria++...</option>'
                     let selected = ''
                     $.each(res, function (i, item) {
                         if (valCat == item.id){
                             seleted = 'selected'
                         }
                         viewHtml += '<option value="' + item.id + '" '+selected+'>' + item.name + '</option>'
                     });
                     $("#categoria").html(viewHtml)
                 }
             })
         }*/

        /*$('#is_color').click(function () {
            if ($(this).is(':checked')) {
                $('#btn-colores-modal').removeAttr('disabled'); //enable input
            } else {
                $('#btn-colores-modal').attr('disabled', true); //disable input
            }
        });*/

        if ($('#ituse_inventory').attr('checked')) {
            console.log("Checkbox seleccionado");
        } else {
            console.log("Checkbox NO seleccionado");
        }


        function validaSinValor() {
            let cont = $('.controlRequired')
            let lista = ''
            lista += '<ul class="list-group">'
            let band = false
            $.each(cont, function (i, item) {
                if (item.value === '') {
                    lista += '<li class="list-group-item">' + $('#' + item.id).attr('nombre') + '</li>'
                    // lista += item.id
                    band = true
                }
            })
            lista += '</ul>'

            let obj = {
                "band": band,
                "lista": lista
            }

            return obj
        }
    })
}