if (document.getElementById('cliente')) {
    $(document).ready(function () {
        validaEtiqueta()

        function validaEtiqueta() {
            let t = ''
            $.ajax({
                url: './?action=configuraciones',
                // async: false,
                type: 'POST',
                data: {"option": 4},
                success: function (response) {
                    console.log(response)
                    if (response === 0 || response == "") {
                        Swal.fire({
                            icon: 'error',
                            title: "Se debe configurar etiqueta en mantenimiento General",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#grabar-client").prop('disabled', true)
                            }
                        })
                        $("#grabar-client").prop('disabled', true)
                        $("#etiqueta").prop('disabled', 'disabled')
                    } else {
                        $("#etiqueta").val(response).trigger('change')
                        $("#etiqueta").prop('disabled', 'disabled')
                    }
                }
            })
            // return t
        }

        $("#grabar-client").click(function (e) {
            e.preventDefault()
            let valor = ''
            let formData = new FormData(document.getElementById("form-cliente")) // crea un nuevo formData
            $(".btn-id-tags").each(function () {
                valor = $(this).val()
                formData.append("tags[]", valor) // Inserta dentro del objeto formData cada valor encontrado de etiqueta.
            });
            $.ajax({
                url: './?action=processclientes',
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (resultados) {
                    let res = JSON.parse(resultados)
                    if (res.substr(0, 1) == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: res.substr(2),
                        })
                    } else {
                        $("#grabar-client").prop('disabled', true)
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: res.substr(2),
                            showConfirmButton: true,
                            timer: 2800
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.href = './?view=clientes';
                            }
                        })
                    }
                }
            })
        })

        /*/!*Selector de clases para alcanzar todos los select*!/
        var allSelects = document.querySelectorAll("#vinculo");
        /!*Selector de tipo para alcanzar todos los radio*!/
        var allRadios = document.querySelectorAll("input[type=radio]");

        /!*Asignamos dentro del bucle un click listener a cada radio*!/
        allRadios.forEach(function (oneRadio) {
            oneRadio.addEventListener('click', function (e) {
                /!*Verificamos el id para decidir si activar o no*!/
                isEnabled = !(e.target.id === 'optionsRadios3');

                /!*Cambiamos el estado de los select segun el estado de isEnabled*!/
                allSelects.forEach(function (oneSelect) {
                    oneSelect.disabled = isEnabled;
                });
            });
        });*/
        /*loadTagsCliente()
        function loadTagsCliente() {
            let tags = $("#etiquetasTags").val()
            let ar = tags.split('-')
            console.log(ar[0])
        }*/

        $("#is_extranjero").click(function () {
            if ($(this).is(":checked")) {
                $("#ceext_natjur").removeAttr('disabled')
            } else {
                $("#ceext_natjur").attr('disabled', true)
            }
        })

        $("#ceimpdif").click(function () {
            if ($(this).is(":checked")) {
                $("#ceporimp").removeAttr('disabled')
            } else {
                $("#ceporimp").attr('disabled', true)
            }
        })

        /*$("#pa_id").change(function () {
            let pais = $('#pa_id option:selected').html()
            if (pais != "ECUADOR") {
                $("#city_id").attr('disabled', true)
                $("#prov_id").attr('disabled', true)
            } else {
                $("#prov_id").removeAttr('disabled')
                $("#city_id").removeAttr('disabled')
            }
        })*/

        $("#tipocliente").change(function () {
            let id = $(this).val()
            console.log(id)
            if (id == 2) {
                // $("#prov_id").attr('disabled', true)
                $("#vinculo").removeAttr('disabled')

            } else {
                $("#vinculo").attr('disabled', true)
                // $("#city_id").removeAttr('disabled')
            }
        })
        console.log('Cliente vista')
        $(document).on("change", "#prov_id", function () {
            let id = $(this).val()
            console.log(id)
            $.ajax({
                url: './?action=processCity',
                type: 'POST',
                data: {option: 1, idprov: id},
                success: function (resultados) {
                    let res = JSON.parse(resultados)
                    console.log(resultados)
                    let opcion = '<option value="">Ciudad...</option>'
                    res.forEach(function (data, index) {
                        opcion += '<option value="' + data.id + '">' + data.name + '</option>'
                    })
                    $("#city_id").html(opcion)
                }
            })
        })

        $(".delete-cliente").click(function (e) {
            e.preventDefault()
            let id = $(this).attr('idcliente')
            let cename = $(this).attr('namece')
            let row = $(this)
            Swal.fire({
                title: 'Eliminar Registro?',
                text: "Desea eliminar el cliente " + cename + " ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: './?action=processclientes',
                        type: 'POST',
                        data: {"option": 1, id: id},
                        success: function (resultados) {
                            console.log(resultados)
                            let res = JSON.parse(resultados)
                            if (res.substr(0, 1) == 0) {
                                Swal.fire({
                                    icon: 'error',
                                    html: res.substr(2),
                                })
                            } else {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: res.substr(2),
                                    showConfirmButton: true,
                                    timer: 2800
                                })
                                deleteRows(row)
                            }
                        }
                    })
                }
            })
        })

        function deleteRows(row) {
            console.log(row.closest('tr').remove())
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
                        if (catVal != '') {
                            if (catVal == item.id) {
                                selected = "selected"
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
                    let selected = ''
                    $.each(res, function (i, item) {
                        if (item.cat == valor) {
                            if (sub != '') {
                                if (item.id == sub) {
                                    selected = "selected"
                                }
                            }
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
            let table = $("#table").val()
            e.preventDefault()
            let id = $("#id").val()
            let producto = 0
            let tipo = 2
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
                        tags += '<div class="btn-group" style="padding-right: 0.5rem;margin-bottom: 0.5rem">\n' +
                            '              <button type="button" class="btn btn-btn-p btn-sm tagsName" value="' + item.id + '" style="font-size: 10px" >' + item.name + '</button>\n' +
                            '              <button type="button" class="btn btn-btn-p btn-sm dropdown-toggle btn-tags-close btn-id-tags"  name="tags[]" value="' + item.id + '" style="font-size: 10px" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\n' +
                            '                <span>x</span>\n' +
                            '              </button>\n' +
                            '            </div>'
                    });
                    tags += '</ul>'
                    $("#tags").html(tags)
                    $("#modalTags").modal('hide')
                }
            })
        })

        function loadEtiquetas() {
            let varConfig = $("#configInv").val()
            if (varConfig.length == 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Se debe configurar etiqueta de inventario",
                })
                console.log(varConfig)
            }
            // } else {
            //
            //     let select = ''
            //     $.ajax({
            //         url: './?action=loadDataProds',
            //         type: 'POST',
            //         data: {option: 2},
            //         success: function (resultado) {
            //             let res = JSON.parse(resultado)
            //             let viewHtml = '<option value="">Seleccione etiqueta...</option>'
            //             $.each(res, function (i, item) {
            //                 if (item.se == 0) {
            //                     viewHtml += '<option value="' + item.id + '"  >' + item.name + '</option>'
            //                 }
            //             });
            //             $("#etiqueta").html(viewHtml)
            //         }
            //     })
            // }
        }

        $("#etiqueta").change(function (event) {
            event.preventDefault()
            let idetiqueta = $(this).val()
            let subEtiqueta = ''
            loadSubEti(idetiqueta)
        })

        let eqt = $("#etiq").val()
        loadSubEti(eqt)

        function loadSubEti(etq) {
            let idSetiq = $("#setiq").val()
            let respuesta = 0
            $.ajax({
                url: './?action=loadDataProds',
                type: 'POST',
                data: {option: 7, idetq: etq},
                success: function (dato) {
                    let res = JSON.parse(dato)
                    let viewHtml = '<option value="">Seleccione subetiqueta...</option>'
                    $.each(res, function (i, item) {
                        let select = ""
                        if (idSetiq == item.id) {
                            select = "selected"
                        }
                        viewHtml += '<option value="' + item.id + '" ' + select + ' >' + item.name + '</option>'
                    });
                    $("#subetiqueta").html(viewHtml)
                }
            })
        }
    })
}