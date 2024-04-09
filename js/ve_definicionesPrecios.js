import {
    scriptRuleta,
    loadActiveModulo,
    getIdActiveBtn,
    loadBotton,
    nextButtom,
    getIdActive,
    antButton
} from './funcionesdef.js';

let valorBandera = $("#definicionesprecios").val()
if (valorBandera == "definicionprecios") {

    $(document).ready(function () {
        $('#clientes').select2({
            // theme: "classic"
        });
        $('#productos').select2({
            // theme: "classic"
        });

        $('#modalNewDefiniciones').on('shown.bs.modal', function () {
            $("#modalNewDefiniciones .modal-header").css('background', '#75A03B')
            $("#modalNewDefiniciones .modal-header").css('color', 'white')
        })

        let objetoProduct = '';
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                objetoProduct = JSON.parse(this.responseText);
                // console.log(this.responseText)
            }
        }
        xhttp.open("GET", './?action=definiciones&option=1');
        xhttp.send();


        $(document).on('change', "#categorias", function () {
            let subcategorias = ''
            if ($(this).val() !== '') {
                let expresion = new RegExp(`${$(this).val()}.*`, "i");
                let productosobj = objetoProduct.filter(producto => expresion.test(producto.cat));
                subcategorias += '<option value="0">Todos</option>'
                $.each(productosobj, function (i, item) {
                    subcategorias += '<option value="' + item.id + '">' + item.name + '</option>'
                })
            }
            $("#subcategoria").html(subcategorias)
        })

        loadActiveModulo('ruleta', 'active-block', 'in-active')
        getIdActiveBtn('carousel-bullet', 'active-bt-act')
        $.when(loadBotton('footer-nav', 'active-bt-act', 'active-bt', 'ruleta')).then(validaPrimerContent('carousel-bullet'));


        function validaPrimerContent(classCarruselBullet) {
            $("." + classCarruselBullet).each(function () {
                if (this.classList.contains("btnSgt-1")) {
                    console.log("contiene")
                } else {
                    console.log("Nocontiene")
                }
            });
        }

        $(document).on('click', "#siguiente", function (e) {
            next()
            let num = $("#bnd-grupos").text()
            visibleNavAndSiguiente(parseInt(num))
        })
        $(document).on('click', '#anterior', function (e) {
            previous()
            let num = $("#bnd-grupos").text()
            visibleNavAndSiguiente(parseInt(num))
        })

        function next() {
            getIdActive('ruleta', 'active-block')
            nextButtom('ruleta', 'carousel-bullet', 'active-block', 'in-active', 'active-bt-act', 'active-bt')
            $("#bnd-grupos").text(getNumerodecontenedorNext())
        }

        function previous() {
            getIdActive('ruleta', 'active-block')
            antButton('ruleta', 'carousel-bullet', 'active-block', 'in-active', 'active-bt-act', 'active-bt')
            $("#bnd-grupos").text(getNumerodecontenedorPreview())
        }

        let d = new scriptRuleta()
        let ar_1 = new Array();
        ar_1.push(
            {
                icono: '0',
                texto: 'Se visualiza las listas de definiciones que ya se encuentrar creadas.'
            }
        )
        ar_1.push(
            {
                icono: '1',
                texto: 'Se seleccionan los productos y sus clasificaciones para poder crear la definición.'
            }
        )
        ar_1.push(
            {
                icono: '2',
                texto: 'Se seleccionan los productos y sus clasificaciones para poder crear la definición.'
            }
        )
        ar_1.push(
            {
                icono: '3',
                texto: 'Se selecciona la lista de precios ,su descuento ,su comisión y se procede a guardar la definición.'
            }
        )
        d.texto = ar_1;

        $("#modulos-iconos").html(d.getClasePadre())

        $(document).on('click', "#newDefinicion", function () {
            getIdActive('ruleta', 'active-block')
            nextButtom('ruleta', 'carousel-bullet', 'active-block', 'in-active', 'active-bt-act', 'active-bt')
        })

        /*================================================================================
        * Actualizacion dia 5 de julio del 2021
        * ==============================================================================*/

        $(document).on('click', "#nuevaDefinicionPrecios", function (e) {
            e.preventDefault()
            $("#modalDefPrecios form").trigger('reset')
            $("#modalDefPrecios").modal('show')
            $("#modalDefPrecios .modal-title").text("Nueva definición")
            $("#modalDefPrecios #estado-group").css('display', 'block')
        })

        loadDefiniciones()

        function loadDefiniciones() {
            $("#table-definiciones").DataTable({
                "destroy": true,
                "ajax": {
                    "method": "POST",
                    "url": "./?action=ve_processDefinicionesPrecios",
                    "data": {"option": 1}
                },
                "columns": [
                    {"data": "cont"},
                    {"data": "name"},
                    {"data": "fechaini"},
                    {"data": "fechafin"},
                    {"data": "estado"},
                    {"data": "button"},
                    // {"data": "creado"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            })
            // clickEdit()
            // clickOpenDef()
        }


        $(document).on('click', "#btn-prueba", function () {
            console.log("prueba")
            loadDefiniciones()
        })

        // function clickEdit() {
        $(document).on('click', '.btn-edit-def', function (e) {
            e.preventDefault()
            const id = $(this).attr('id');
            const estado = $(this).attr('estado');
            const fechaini = $(this).attr('fechaini');
            const fechafin = $(this).attr('fechafin');
            const name = $(this).closest('tr').find('td:eq(1)').text()
            $("#modalDefPrecios .modal-title").text(name)
            $("#modalDefPrecios #nuevaDefinicion").val(name)
            $("#modalDefPrecios #fechaInicio").val(fechaini)
            $("#modalDefPrecios #fechaHasta").val(fechafin)
            $("#modalDefPrecios #option").val(3)
            $("#modalDefPrecios #idDef").val(id)
            $("#modalDefPrecios #estado-group").css('display', 'none')
            $("#modalDefPrecios").modal('show')
        })

        // function clickEdit() {
        $(document).on('click', '.btn-anul-def', function (e) {
            e.preventDefault()
            const id = $(this).attr('id');
            const name = $(this).closest('tr').find('td:eq(1)').text()
            Swal.fire({
                title: 'Desactivar Definición',
                text: "Seguro desea desactivar la deficinición " + name + " !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Desactivar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    anularDefinicion(id, name)
                }
            })
        })

        /*=====================================================
        *       FUNCION PARA ANULAR LA DEFINICION
        *  */
        $(document).on('click', '.btn-act-def', function (e) {
            e.preventDefault()
            const id = $(this).attr('id');
            const name = $(this).closest('tr').find('td:eq(1)').text()
            Swal.fire({
                title: 'Activar Definición',
                text: "Desea activar la deficinición " + name + " !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Acivar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    activarDefinicion(id, name)
                }
            })
        })
        /*====================================================*/

        /*====================================================
        *   FUNCION PARA ACTIVAR LA DEFINICION
        *  */
        function activarDefinicion(id, name) {
            $.ajax({
                url: './?action=ve_processDefinicionesPrecios',
                type: 'POST',
                data: {option: 6, id: id, name: name},
                success: function (respond) {
                    if (respond.substr(0, 1) == 0) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: respond.substr(2),
                            showConfirmButton: false,
                            timer: 1000
                        })
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: respond.substr(2),
                            showConfirmButton: false,
                            timer: 1000
                        })
                        loadDefiniciones()
                    }
                }
            })
        }

        /*==================================================*/

        /*====================================================
        *       FUNCION PARA ANULAR LA DEFINICION
        * */
        function anularDefinicion(id, name) {
            $.ajax({
                url: './?action=ve_processDefinicionesPrecios',
                type: 'POST',
                data: {option: 5, id: id, name: name},
                success: function (respond) {
                    if (respond.substr(0, 1) == 0) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: respond.substr(2),
                            showConfirmButton: false,
                            timer: 1000
                        })
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: respond.substr(2),
                            showConfirmButton: false,
                            timer: 1000
                        })
                        loadDefiniciones()
                    }
                }
            })
        }

        $(document).on('click', '#nuevo-det-def', function (e) {
            e.preventDefault()
            let id = $(this).attr('idcab')
            $("#idDefinicionCab").val(id);
            for (let r = 0; r <= 1; r++) {
                previous()
                let num = $("#bnd-grupos").text()
                visibleNavAndSiguiente(parseInt(num))
            }
        })
        /*=====================================================*/

        /*====================================================
        *     SE DA CLICK Y SE EDITA EL DETALLE DE LA DEFINICION
        * */
        $(document).on('click', ".btn-edit-det", function (e) {
            e.preventDefault()
            const id = $(this).attr('id');
            const name = $(this).closest('tr').find('td:eq(1)').text()
            let htmlSpan = '<span class="label label-default">' + name + '</span>'
            $("#nameDefinicionEditar").html(htmlSpan)
            $(".hidden-button").css('display', 'block')
            $("#idDefinicionCab").val(id);
            $("#nuevo-det-def").attr("idCab", id);

            /* =======================================================================================
                EJECUTA LA FUNCION NEXT() PARA MOSTRAR EL CONTENIDO DEL CONTENEDOR VISUAL QUE SE DESEA
            * =======================================================================================*/
            loadDataDetalleDefinicion(id)
            for (let o = 0; o <= 2; o++) {
                next()
                let num = $("#bnd-grupos").text()
                visibleNavAndSiguiente(parseInt(num))
            }
        })

        /*==================================================================
        *           CARGA EL DETALLE DE LA DEFINCION DE PRECIO
        * */
        function loadDataDetalleDefinicion(id) {
            $.ajax({
                url: './?action=ve_processDefinicionesPrecios',
                type: 'POST',
                data: {option: 4, id: id},
                success: function (e) {
                    let datos = JSON.parse(e)
                    let htmlBody = ''
                    let cont = 1
                    $.each(datos, function (i, item) {
                        htmlBody += "" +
                            "<tr>" +
                            "<td>" + cont + "</td>" +
                            "<td>" + item.tipo1 + "</td>" +
                            "<td>" + item.tipo2 + "</td>" +
                            "<td>" + item.etiqueta + "</td>" +
                            "<td>" + item.marca + "</td>" +
                            "<td>" + item.setiqueta + "</td>" +
                            "<td>" + item.categoria + "</td>" +
                            "<td>" + item.scategoria + "</td>" +
                            "<td>" + item.producto + "</td>" +
                            "<td>" + item.vendedor + "</td>" +
                            "<td>" + item.clasificacion + "</td>" +
                            "<td>" + item.pais + "</td>" +
                            "<td>" + item.provincia + "</td>" +
                            "<td>" + item.ciudad + "</td>" +
                            "<td>" + item.tag + "</td>" +
                            "<td>" + item.cliente_tag + "</td>" +
                            "<td>" + item.listaprecio + "</td>" +
                            "<td>" + item.descuento + "</td>" +
                            "<td>" + item.comision + "</td>" +
                            "<td>" +
                            "<a class='btn-edit-detalle' " +
                            "id='" + item.id + "' " +
                            "tipo1='" + item.tipo1ID + "' " +
                            "tipo2='" + item.tipo2ID + "' " +
                            "etiqueta='" + item.etiquetaID + "' " +
                            "setiqueta='" + item.setiquetaID + "' " +
                            "categoria='" + item.categoriaID + "' " +
                            "scategoria='" + item.scategoriaID + "' " +
                            "marca='" + item.marcaID + "' " +
                            "clasificacion='" + item.clasificacionID + "' " +
                            "producto='" + item.productoID + "' " +
                            "vendedor='" + item.vendedorID + "' " +
                            "recaudador='" + item.recaudadorID + "' " +
                            "pais='" + item.paisID + "' " +
                            "provincia='" + item.provinciaID + "' " +
                            "ciudad='" + item.ciudadID + "' " +
                            "tag='" + item.tagID + "' " +
                            "clientetag='" + item.cliente_tagID + "' " +
                            "listaprecio='" + item.listaprecioID + "' " +
                            "descuento='" + item.descuento + "' " +
                            "comision='" + item.comision + "' " +
                            "><i class='btn btn-xs glyphicon glyphicon-pencil'></i></a>" +
                            "<a><i class='btn btn-xs glyphicon glyphicon-trash' id='" + item.id + "'></i></a>" +
                            "</td>" +
                            "</tr>"
                        cont++
                    })
                    $("#table-det-def tbody").html(htmlBody)
                }
            })
        }

        /*================================================================*/

        /*==================================================================
        *      CARGA LA INFORMACION DEL DETALLE PARA LA EDICION
        * */
        $(document).on('click', ".btn-edit-detalle", function (e) {
            e.preventDefault()
            let id = $(this).attr("id")
            let tipo1 = $(this).attr("tipo1") /* GRUPO DE PRODUCTOS */
            let tipo2 = $(this).attr("tipo2") /* GRUPO DE CLIENTE */

            let objPro = {}

            objPro.id = $(this).attr("id")
            objPro.grupo = $(this).attr("tipo2")
            objPro.etiqueta = $(this).attr("etiqueta")
            objPro.setiqueta = $(this).attr("setiqueta")
            objPro.categoria = $(this).attr("categoria")
            objPro.scategoria = $(this).attr("scategoria")
            objPro.producto = $(this).attr("producto")
            objPro.marca = $(this).attr("marca")
            objPro.tags = $(this).attr("tag")

            changeGrupoProductos(objPro) /* =========== OBJETO DE PRODUCTO */

            let objCli = {}
            objCli.id = $(this).attr("id")
            objCli.grupo = $(this).attr("tipo1")
            objCli.vendedor = $(this).attr("vendedor")
            objCli.recaudador = $(this).attr("recaudador")
            objCli.pais = $(this).attr("pais")
            objCli.clasificacion = $(this).attr("clasificacion")
            objCli.provincia = $(this).attr("provincia")
            objCli.ciudad = $(this).attr("ciudad")
            objCli.cliente = $(this).attr("clientetag")

            changeGrupoClientes(objCli) /* =========== OBJETO DE CLIENTE */

            let listaprecio = $(this).attr("listaprecio")
            let descuento = $(this).attr("descuento")
            let comision = $(this).attr("comision")

            $("#comision").val(comision)
            $("#descuento").val(descuento)
            $("#optionDef").val(2)
            $("#idDefinicionDet").val(id)


            $("#listaprecio").val($(this).attr("listaprecio")).trigger('change')

            for (let r = 0; r <= 1; r++) {
                previous()
                let num = $("#bnd-grupos").text()
                visibleNavAndSiguiente(parseInt(num))
            }
        })
        /*================================================================*/

        $(document).on('click', "#proccessDefinicion", function (e) {
            e.preventDefault()
            let nombre = $("#nuevaDefinicion").val()
            let datosform = $("#modalDefPrecios form").serialize()
            $.ajax({
                url: './?action=ve_processDefinicionesPrecios',
                type: 'POST',
                data: datosform,
                success: function (respond) {
                    let d = JSON.parse(respond)

                    if (d[0].substr(0, 1) == 0) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: d[0].substr(4),
                            showConfirmButton: false,
                            timer: 1000
                        })
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: d[0].substr(4),
                            showConfirmButton: false,
                            timer: 1000
                        })
                        if (d[0].substr(2, 1) == 1) {
                            loadDefiniciones()
                            $("#modalDefPrecios").modal('hide')
                            let htmlSpan = '<span class="label label-default">' + nombre + '</span>'
                            $("#nameDefinicionEditar").html(htmlSpan)
                            $("#idDefinicionCab").val(d[1])
                            $("#nuevo-det-def").attr("idCab", d[1])
                            next()
                            let num = $("#bnd-grupos").text()
                            visibleNavAndSiguiente(parseInt(num))
                        } else {
                            loadDefiniciones()
                            $("#modalDefPrecios").modal('hide')
                            // next()
                        } /* SI LA RESPUESTA DE ESTA VALIDACION ES VERDADERA , ES POR QUE EL PROCESO EJECUTADO ES EL DE CREACION Y PASA AL SIGUIENTE CONTENEDOR VISUAL PARA LA CREACION DE LA DEFINICION */
                    }
                }
            })
        }) /* EJECUTA EL PROCESO DE GRABACION EN YA SEA PARA EL PROCESO DE CREACION DE NUEVA DEFINICION O PARA ACTUALIZARLA , TAMBIEN CAMBIA EL CONTENEDOR VISUAL LUEGO DE CREARLA */


        document.getElementById("grp-productos").addEventListener("change", function () {
            let objP = {}
            objP.grupo = $(this).val()
            changeGrupoProductos(objP)
        });

        document.getElementById("grp-productos-clientes").addEventListener("change", function () {
            let objC = {}
            objC.grupo = $(this).val()
            changeGrupoClientes(objC)
        });

        /* ===== FUNCION PARA HABILITAR LAS OPCIONES EN EL GRUPO DE CLIENTES */

        $(document).on('click', '#cancelProcess', function (event) {
            event.preventDefault()
            $("#formularioClientes").trigger('reset')
            $("#formularioProductos").trigger('reset')
            $("#formularioPrecios").load(' #formularioPrecios')
            $("#nameDefinicionEditar").text('DEFINICION DE PRECIOS')
            for (let r = 0; r <= 2; r++) {
                previous()
            }
        })

        function changeGrupoClientes(idGrupoCli) {
            console.log(JSON.stringify(idGrupoCli))
            if (idGrupoCli.grupo == 2) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')
                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')

            } else if (idGrupoCli.grupo == 3) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')
                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#recaudador").removeAttr('disabled')
                $("#recaudador").val(idGrupoCli.recaudador).trigger('change')

            } else if (idGrupoCli.grupo == 4) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#clasificacion").removeAttr('disabled')
                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')

            } else if (idGrupoCli.grupo == 5) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#paises").removeAttr('disabled')
                $("#paises").val(idGrupoCli.pais).trigger('change')

            } else if (idGrupoCli.grupo == 6) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#provincias").removeAttr('disabled')
            } else if (idGrupoCli.grupo == 7) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#ciudades").removeAttr('disabled')
                $("#ciudades").val(idGrupoCli.ciudad).trigger('change')

            } else if (idGrupoCli.grupo == 8) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#recaudador").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#recaudador").val(idGrupoCli.recaudador).trigger('change')

            } else if (idGrupoCli.grupo == 9) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#clasificacion").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change') /*=====*/
            } else if (idGrupoCli.grupo == 10) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#paises").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#paises").val(idGrupoCli.pais).trigger('change')

            } else if (idGrupoCli.grupo == 11) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#provincias").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#provincias").val(idGrupoCli.provincia).trigger('change')

            } else if (idGrupoCli.grupo == 12) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#ciudades").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#ciudades").val(idGrupoCli.ciudad).trigger('change')

            } else if (idGrupoCli.grupo == 13) { /*====================*/
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#recaudador").removeAttr('disabled')
                $("#clasificacion").removeAttr('disabled')


                $("#recaudador").val(idGrupoCli.recaudador).trigger('change')
                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')

            } else if (idGrupoCli.grupo == 14) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#recaudador").removeAttr('disabled')
                $("#paises").removeAttr('disabled')

                $("#recaudador").val(idGrupoCli.recaudador).trigger('change')
                $("#paises").val(idGrupoCli.pais).trigger('change')

            } else if (idGrupoCli.grupo == 15) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#recaudador").removeAttr('disabled')
                $("#provincias").removeAttr('disabled')

                $("#recaudador").val(idGrupoCli.recaudador).trigger('change')
                $("#provincias").val(idGrupoCli.provincia).trigger('change')

            } else if (idGrupoCli.grupo == 16) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#recaudador").removeAttr('disabled')
                $("#ciudades").removeAttr('disabled')

                $("#recaudador").val(idGrupoCli.recaudador).trigger('change')
                $("#ciudades").val(idGrupoCli.ciudad).trigger('change')

            } else if (idGrupoCli.grupo == 17) { /*====================*/
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#clasificacion").removeAttr('disabled')
                $("#paises").removeAttr('disabled')

                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')
                $("#paises").val(idGrupoCli.pais).trigger('change')

            } else if (idGrupoCli.grupo == 18) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#clasificacion").removeAttr('disabled')
                $("#provincias").removeAttr('disabled')

                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change') /* ===== */
                $("#provincias").val(idGrupoCli.provincia).trigger('change')

            } else if (idGrupoCli.grupo == 19) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#clasificacion").removeAttr('disabled')
                $("#ciudades").removeAttr('disabled')

                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')
                $("#ciudades").val(idGrupoCli.ciudad).trigger('change')

            } else if (idGrupoCli.grupo == 20) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#paises").removeAttr('disabled')
                $("#provincias").removeAttr('disabled')

                $("#paises").val(idGrupoCli.pais).trigger('change')
                $("#provincias").val(idGrupoCli.provincia).trigger('change')

            } else if (idGrupoCli.grupo == 21) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#paises").removeAttr('disabled')
                $("#ciudades").removeAttr('disabled')

                $("#paises").val(idGrupoCli.pais).trigger('change')
                $("#ciudades").val(idGrupoCli.ciudad).trigger('change')

            } else if (idGrupoCli.grupo == 22) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#provincias").removeAttr('disabled')
                $("#ciudades").removeAttr('disabled')

                $("#provincias").val(idGrupoCli.provincia).trigger('change')
                $("#ciudades").val(idGrupoCli.ciudad).trigger('change')

            } else if (idGrupoCli.grupo == 23) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#recaudador").removeAttr('disabled')
                $("#clasificacion").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#recaudador").val(idGrupoCli.recaudador).trigger('change')
                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')

            } else if (idGrupoCli.grupo == 24) { /*====================*/
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#recaudador").removeAttr('disabled')
                $("#paises").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#recaudador").val(idGrupoCli.recaudador).trigger('change')
                $("#paises").val(idGrupoCli.pais).trigger('change')

            } else if (idGrupoCli.grupo == 25) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#recaudador").removeAttr('disabled')
                $("#provincias").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#recaudador").val(idGrupoCli.recaudador).trigger('change')
                $("#provincias").val(idGrupoCli.provincia).trigger('change')

            } else if (idGrupoCli.grupo == 26) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#recaudador").removeAttr('disabled')
                $("#ciudades").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#recaudador").val(idGrupoCli.recaudador).trigger('change')
                $("#ciudades").val(idGrupoCli.ciudad).trigger('change')

            } else if (idGrupoCli.grupo == 27) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#clasificacion").removeAttr('disabled')
                $("#paises").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')
                $("#paises").val(idGrupoCli.pais).trigger('change')

            } else if (idGrupoCli.grupo == 28) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#clasificacion").removeAttr('disabled')
                $("#provincias").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')
                $("#provincias").val(idGrupoCli.provincia).trigger('change')

            } else if (idGrupoCli.grupo == 29) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#vendedor").removeAttr('disabled')
                $("#clasificacion").removeAttr('disabled')
                $("#ciudades").removeAttr('disabled')

                $("#vendedor").val(idGrupoCli.vendedor).trigger('change')
                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')
                $("#ciudades").val(idGrupoCli.ciudad).trigger('change')

            } else if (idGrupoCli.grupo == 30) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#clasificacion").removeAttr('disabled')
                $("#paises").removeAttr('disabled')
                $("#provincias").removeAttr('disabled')

                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')
                $("#paises").val(idGrupoCli.pais).trigger('change')
                $("#provincias").val(idGrupoCli.provincia).trigger('change')

            } else if (idGrupoCli.grupo == 31) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#clasificacion").removeAttr('disabled')
                $("#paises").removeAttr('disabled')
                $("#ciudades").removeAttr('disabled')

                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')
                $("#paises").val(idGrupoCli.pais).trigger('change')
                $("#ciudades").val(idGrupoCli.ciudad).trigger('change')

            } else if (idGrupoCli.grupo == 32) {
                $("#grp-productos-clientes").val(idGrupoCli.grupo).trigger('change')

                $(".disable-slt-clients").attr('disabled', 'disabled')
                $("#clientes").removeAttr('disabled')

                $("#clientes").val(idGrupoCli.cliente).trigger('change')
            }

        }

        function changeGrupoProductos(idGrupoPro) {
            switch (idGrupoPro.grupo) {
                case '2':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')
                    $("#categorias").removeAttr('disabled')

                    $("#categorias").val(idGrupoPro.categoria).trigger('change')
                    break;
                case '3':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')
                    $("#subcategoria").removeAttr('disabled')

                    $("#subcategoria").val(idGrupoPro.scategoria).trigger('change')
                    break;
                case '4':
                    console.log(idGrupoPro.grupo)
                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')
                    $("#etiqueta").removeAttr('disabled')

                    $("#etiqueta").val(idGrupoPro.etiqueta).trigger('change')
                    break;
                case '5':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')
                    $("#subetiqueta").removeAttr('disabled')

                    $("#subetiqueta").val(idGrupoPro.setiqueta).trigger('change')
                    break;
                case '6':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#marcas").removeAttr('disabled')
                    $("#marcas").val(idGrupoPro.marca).trigger('change')
                    break;
                case '7':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')
                    $("#etiqTags").removeAttr('disabled') /* pendiente select multiple*/
                    let tags = idGrupoPro.tags.split(',')
                    $("#etiqTags").val(tags).trigger('change')

                    break;
                case '8':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#categorias").removeAttr('disabled')
                    $("#subcategoria").removeAttr('disabled')

                    $("#categorias").val(idGrupoPro.categoria).trigger('change')
                    $("#subcategoria").val(idGrupoPro.scategoria).trigger('change')
                    break;
                case '9':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#categorias").removeAttr('disabled')
                    $("#etiqueta").removeAttr('disabled')

                    $("#etiqueta").val(idGrupoPro.etiqueta).trigger('change')
                    $("#categorias").val(idGrupoPro.categoria).trigger('change')
                    break;
                case '10':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#categorias").removeAttr('disabled')
                    $("#subetiqueta").removeAttr('disabled')

                    $("#categorias").val(idGrupoPro.categoria).trigger('change')
                    $("#subetiqueta").val(idGrupoPro.setiqueta).trigger('change')
                    break;
                case '11':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#categorias").removeAttr('disabled')
                    $("#marcas").removeAttr('disabled')

                    $("#marcas").val(idGrupoPro.marca).trigger('change')
                    $("#categorias").val(idGrupoPro.categoria).trigger('change')
                    break;
                case '12':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#categorias").removeAttr('disabled')
                    $("#etiqTags").removeAttr('disabled')

                    $("#categorias").val(idGrupoPro.categoria).trigger('change')
                    $("#etiqTags").val(idGrupoPro.categoria).trigger('change') /* pendiente revisar */
                    break;
                case '13':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#subcategoria").removeAttr('disabled')
                    $("#etiqueta").removeAttr('disabled')

                    $("#subcategoria").val(idGrupoPro.scategoria).trigger('change')
                    $("#etiqueta").val(idGrupoPro.etiqueta).trigger('change')
                    break;
                case '14':
                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')
                    $("#subcategoria").removeAttr('disabled')
                    $("#subetiqueta").removeAttr('disabled')

                    $("#subcategoria").val(idGrupoPro.scategoria).trigger('change')
                    $("#subetiqueta").val(idGrupoPro.setiqueta).trigger('change')
                    break;
                case '15':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#subcategoria").removeAttr('disabled')
                    $("#marcas").removeAttr('disabled')

                    $("#subcategoria").val(idGrupoPro.scategoria).trigger('change')
                    $("#marcas").val(idGrupoPro.marca).trigger('change')
                    break;
                case '16':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#subcategoria").removeAttr('disabled')
                    $("#etiqTags").removeAttr('disabled')

                    $("#subcategoria").val(idGrupoPro.scategoria).trigger('change')
                    $("#etiqTags").val(idGrupoPro.marca).trigger('change') /* pendiente revisar */
                    break;
                case '17':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#etiqueta").removeAttr('disabled')
                    $("#marcas").removeAttr('disabled')

                    $("#etiqueta").val(idGrupoPro.etiqueta).trigger('change')
                    $("#marcas").val(idGrupoPro.marca).trigger('change')
                    break;
                case '18':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#marcas").removeAttr('disabled')
                    $("#etiqTags").removeAttr('disabled')

                    $("#marcas").val(idGrupoPro.marca).trigger('change')
                    $("#etiqTags").val(idGrupoPro.marca).trigger('change') /* pendiente revisar */
                    break;
                case '19':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#categorias").removeAttr('disabled')
                    $("#subcategoria").removeAttr('disabled')
                    $("#etiqueta").removeAttr('disabled')

                    $("#categorias").val(idGrupoPro.categoria).trigger('change')
                    $("#subcategoria").val(idGrupoPro.scategoria).trigger('change')
                    $("#etiqueta").val(idGrupoPro.etiqueta).trigger('change')

                    break;
                case '20':

                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#categorias").removeAttr('disabled')
                    $("#subcategoria").removeAttr('disabled')
                    $("#subetiqueta").removeAttr('disabled')

                    $("#categorias").val(idGrupoPro.categoria).trigger('change')
                    $("#subcategoria").val(idGrupoPro.scategoria).trigger('change')
                    $("#subetiqueta").val(idGrupoPro.setiqueta).trigger('change')
                    break;
                case '21':
                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#categorias").removeAttr('disabled')
                    $("#subcategoria").removeAttr('disabled')
                    $("#marcas").removeAttr('disabled')

                    $("#categorias").val(idGrupoPro.categoria).trigger('change')
                    $("#subcategoria").val(idGrupoPro.scategoria).trigger('change')
                    $("#marcas").val(idGrupoPro.marca).trigger('change')
                    break;
                case '22':
                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#categorias").removeAttr('disabled')
                    $("#subcategoria").removeAttr('disabled')
                    $("#etiqTags").removeAttr('disabled')

                    $("#categorias").val(idGrupoPro.categoria).trigger('change')
                    $("#subcategoria").val(idGrupoPro.scategoria).trigger('change')
                    $("#etiqTags").val(idGrupoPro.scategoria).trigger('change') /* revisar */
                    break;
                case '23':
                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#etiqueta").removeAttr('disabled')
                    $("#subetiqueta").removeAttr('disabled')
                    $("#marcas").removeAttr('disabled')

                    $("#etiqueta").val(idGrupoPro.etiqueta).trigger('change')
                    $("#subetiqueta").val(idGrupoPro.setiqueta).trigger('change')
                    $("#marcas").val(idGrupoPro.marca).trigger('change')
                    break;
                case '24':
                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#etiqueta").removeAttr('disabled')
                    $("#subetiqueta").removeAttr('disabled')
                    $("#etiqTags").removeAttr('disabled')

                    $("#etiqueta").val(idGrupoPro.etiqueta).trigger('change')
                    $("#subetiqueta").val(idGrupoPro.setiqueta).trigger('change')
                    $("#etiqTags").val(idGrupoPro.marca).trigger('change') /* REVISAR PENDIENTE */
                    break;
                case '25':
                    $("#grp-productos").val(idGrupoPro.grupo).trigger('change')
                    $(".disable-slt-product").attr('disabled', 'disabled')

                    $("#productosGrp").removeAttr('disabled')

                    $("#productosGrp").val(idGrupoPro.producto).trigger('change')
                    break;
                default:
                    break;

            }

        }

        /* ======================================================
        *      ENVIAR DOS FORMULARIOS AL MISMO TIEMPO
        * */

        $(document).on('click', "#grabarDefinicion", function (event) {
            event.preventDefault();
            let idCab = $("#nuevo-det-def").attr("idCab")
            let idDet = $("#idDefinicionDet").val()

            let desc = $("#descuento").val()
            let comi = $("#comision").val()
            if (comi.length != 0 && comi != '') {
                if (desc != '' && desc.length != 0) {
                    let allData = $("#formularioProductos, #formularioClientes, #formularioPrecios").serialize();
                    $.ajax({
                        url: './?action=ve_processDefinicionPre',
                        type: 'POST',
                        data: allData,
                        success: function (respond) {
                            console.log(respond)
                            if (respond.substr(0, 1) == 0) {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'error',
                                    title: respond.substr(2),
                                    showConfirmButton: false,
                                    timer: 1000
                                })
                            } else {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: respond.substr(2),
                                    showConfirmButton: false,
                                    timer: 1000
                                })
                                loadDataDetalleDefinicion(idCab)
                                $("#formularioProductos").trigger('reset')
                                $("#formularioClientes").trigger('reset')
                                $("#formularioPrecios").trigger('reset')
                            }
                        }
                    })
                } else {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: "Para grabar debe registrar descuento o comisión",
                        showConfirmButton: false,
                        timer: 1000
                    })
                }
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: "Para grabar debe registrar descuento o comisión",
                    showConfirmButton: false,
                    timer: 1000
                })
            }
        });

        /*======================================================*/

        // console.log(document.getElementsByClassName("carousel-bullet").length)

        function getNumerodecontenedorNext() {
            let totCont = document.getElementsByClassName("carousel-bullet");
            let valorBandera = $("#bnd-grupos").text()
            let result = 0
            if (parseInt(valorBandera) == parseInt(totCont.length)) {
                result = 1
            } else {
                result = parseInt(valorBandera) + 1
            }
            return result
        }

        function getNumerodecontenedorPreview() {
            let totCont = document.getElementsByClassName("carousel-bullet");
            let valorBandera = $("#bnd-grupos").text()
            let result = parseInt(valorBandera) - 1
            return result
        }

        const f = 1

        visibleNavAndSiguiente(f)

        function visibleNavAndSiguiente(valor) {
            console.log(valor)
            let totalCont = document.getElementsByClassName("carousel-bullet")
            if (valor == 1) {
                $("#footer-nav").css("display", "none")
            } else {
                $("#footer-nav").css("display", "block")
            }
            if (valor == totalCont.length) {
                $("#siguiente").css("display", "none")
            } else {
                $("#siguiente").css("display", "inline-block")
            }
        }

        $(document).on('click', '.btn-impr-def', function (e) {
            e.preventDefault()
            let id = $(this).attr('id')
            $.ajax({
                url: './?action=ve_printDataPrecios',
                type: 'POST',
                data: {id: id},
                success: function (respuesta) {
                    let rd = JSON.parse(respuesta)
                    if (rd.data == null) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: "No contiene detalle",
                            showConfirmButton: false,
                            timer: 1000
                        })
                    } else {
                        printJS(
                            {
                                printable: rd.data,
                                documentTitle: 'SMARTAG - Sistema empresarial.',
                                targetStyles: ['*'],
                                maxWidth: 300,
                                header: '<div class="title"><h3>' + rd.empresa.nameEmpresa + '</h3></div>' +
                                    '<div class="custom-h3"><h4 class="title">Lista de Precios - ' + rd.cabecera.cabecera + ' </h4></div>',
                                properties: [
                                    "Grp Cliente",
                                    "Grp Productos",
                                    "Etiqueta",
                                    "Subetiqueta",
                                    "Categoria",
                                    "Subcategoria",
                                    "Producto",
                                    "Vendedor",
                                    "Pais",
                                    "Provincia",
                                    "Ciudad",
                                    "Productos tags",
                                    "Cliente tags",
                                    "Lista precio",
                                    "Clasificación",
                                    "Descuento",
                                    "Comisión"
                                ],
                                style: '.clase { display: flex;justify-content:space-between ; } .custom-h3{display:flex;margin:0px} .title{margin:0px}  table tbody tr td{align-text:center}',
                                type: 'json',
                                // onPrintDialogClose: true
                            }
                        )
                    }
                }
            })
        })


    }) // Fin de funcion document.ready
}
