import {
    scriptRuleta,
    loadActiveModulo,
    getIdActiveBtn,
    loadBotton,
    nextButtom,
    getIdActive,
    antButton
} from './funcionesdef.js';

let valorBandera = $("#definicionesrentabilidad").val()
if (valorBandera == "definicionesrentabilidad") {

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
        /* FUNCION QUE RECIBE EL NUMERO DEL ICONO A MOSTRAR Y EL TEXTO A MOSTRAR*/
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
        /*ar_1.push(
            {
                icono: '2',
                texto: 'Se seleccionan los productos y sus clasificaciones para poder crear la definición.'
            }
        )*/
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
                    "url": "./?action=ve_processDefinicionesRenta",
                    "data": {"option": 1}
                },
                "columns": [
                    {"data": "cont"},
                    {"data": "tipo"},
                    {"data": "name"},
                    // {"data": "fechafin"},
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

        // function clickEdit() {
        $(document).on('click', '.btn-edit-def', function (e) {
            e.preventDefault()
            const id = $(this).attr('id');
            const estado = $(this).attr('estado');
            const fechaini = $(this).attr('fechaini');
            const tipoDef = $(this).attr('tipoDef');
            const fechafin = $(this).attr('fechafin');
            const name = $(this).closest('tr').find('td:eq(2)').text()
            $("#modalDefPrecios .modal-title").text(name)
            $("#modalDefPrecios #nuevaDefinicion").val(name)
            $("#modalDefPrecios #fechaInicio").val(fechaini)
            $("#modalDefPrecios #fechaHasta").val(fechafin)
            $("#modalDefPrecios #tipoDefinicion").val(tipoDef).trigger('change')
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
            console.log(id)
            $.ajax({
                url: './?action=ve_processDefinicionesRenta',
                type: 'POST',
                data: {option: 6, id: id, name: name},
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
                url: './?action=ve_processDefinicionesRenta',
                type: 'POST',
                data: {option: 5, id: id, name: name},
                success: function (respond) {
                    // console.log(respond)
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
            for (let r = 0; r < 1; r++) {
                previous()
                let num = $("#bnd-grupos").text()
                visibleNavAndSiguiente(parseInt(num)) // SE VALIDA LA VISAUALIZACION DE LOS BOTONES DE SIGUIENTE Y
            }
        })
        /*=====================================================*/

        /*====================================================
        *     SE DA CLICK Y SE EDITA EL DETALLE DE LA DEFINICION
        * */
        $(document).on('click', ".btn-edit-det", function (e) {
            e.preventDefault()
            const id = $(this).attr('id'); // SE TOMA EL VALOR DEL ATRIBUTO ID QUE CONTIENE EL ID DE LA DEFINICION
            // DE RENTABILIDAD PARA EDITAR SU DETALLE
            const tipodef = $(this).attr('tipodef')
            const name = $(this).closest('tr').find('td:eq(1)').text() // SE TOMA EL NOMBRE DE LA DEFINICION DE
            // RENTABILIDAD PARA MOSTRARLO EN LA CABECERA DE LA VENTANA E IDENTIFICAR LA DEFINICION QUE SE ESTA EDITANDO
            let htmlSpan = '<span class="label label-default">' + name + '</span>' // SE ASIGNA EL FORMATO DEL
            // NOMBRE DE LA DEFINICION DE RENTABILIDAD
            $("#nameDefinicionEditar").html(htmlSpan) // SE ASIGNA EL NOMBRE DE LA DEFINICION EN EL CONTENEDOR
            $(".hidden-button").css('display', 'block')
            $("#idDefinicionCab").val(id); // SE ASIGNA EL ID DE LA DEFINICION QUE SE ESTA EDITANDO PARA EL PROCESO
            // DE GRABACION DEL DETALLE
            $("#nuevo-det-def").attr("idCab", id); // SE ASIGNA EL ATRIBUTO ID PARA IDENTIFICAR LA CABECERA QUE SE
            // VA A EDITAR
            $("#tipodefinicion").val(tipodef); // SE ASIGNA EL ID AL BOTON DE GRABAR PARA PROCESAR
            if (tipodef == 3) {
                if ($("#blockTipoDef").hasClass('displayNoneListaPrecios')) {
                    $("#blockTipoDef").removeClass('displayNoneListaPrecios')
                    $("#blockTipoDef").removeClass('displayBlockListaPrecios')
                    $("#textListaPreciosAfec").text("Lista precios Afectada")
                    $("#listapreciobase").removeAttr('disabled')
                }
            } else {
                $("#listapreciobase").attr('disabled', 'disabled')
            }

            /* =======================================================================================
                EJECUTA LA FUNCION NEXT() PARA MOSTRAR EL CONTENIDO DEL CONTENEDOR VISUAL QUE SE DESEA
            * =======================================================================================*/
            loadDataDetalleDefinicion(id)
            for (let o = 0; o <= 1; o++) {
                next() // FUNCION QUE EJECTUA EL PROCESO DE AVANZAR AL SIGUIENTE PROCESO
                let num = $("#bnd-grupos").text() // SE TOMA EL NUMERO DE LA VENTANA QUE SE ESTA VISUALIZANDO PARA
                // EL PROCESO DE VISUALIZACION Y OCULTACION DE LOS BOTONES DE SIGUIENTE Y ANTERIOR
                visibleNavAndSiguiente(parseInt(num)) // SE VALIDA LA VISAUALIZACION DE LOS BOTONES DE SIGUIENTE Y
                // ANTERIOR
            }
        })

        /*==================================================================
        *           CARGA EL DETALLE DE LA DEFINCION DE PRECIO
        * */
        function loadDataDetalleDefinicion(id) {
            $.ajax({
                url: './?action=ve_processDefinicionesRenta',
                type: 'POST',
                data: {option: 4, id: id},
                success: function (e) {
                    // console.log(e)
                    let datos = JSON.parse(e)
                    // console.log(datos)
                    let htmlBody = ''
                    let cont = 1
                    $.each(datos, function (i, item) {
                        htmlBody += "" +
                            "<tr>" +
                            "<td>" + cont + "</td>" +
                            // "<td>" + item.tipo1 + "</td>" +
                            "<td>" + item.tipo2 + "</td>" +
                            "<td>" + item.etiqueta + "</td>" +
                            "<td>" + item.marca + "</td>" +
                            "<td>" + item.setiqueta + "</td>" +
                            "<td>" + item.categoria + "</td>" +
                            "<td>" + item.scategoria + "</td>" +
                            "<td>" + item.producto + "</td>" +
                            "<td>" + item.tag + "</td>" +
                            "<td>" + item.listapreciobase + "</td>" +
                            "<td>" + item.listaprecio + "</td>" +
                            "<td>" + item.rentabilidad + "</td>" +
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
                            "producto='" + item.productoID + "' " +
                            "tag='" + item.tagID + "' " +
                            "listaprecio='" + item.listaprecioID + "' " +
                            "listapreciobase='" + item.listapreciobaseID + "' " +
                            "rentabilidad='" + item.rentabilidad + "' " +
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
            let tipodef = $(this).attr("listapreciobase")
            console.log(tipodef)
            let tipo1 = $(this).attr("tipo1") /* GRUPO DE PRODUCTOS */
            let tipo2 = $(this).attr("tipo2") /* GRUPO DE CLIENTE */

            if (tipodef.length != 0) { // SE VALIDA EL TIPO DE DEFINICION PARA VISUALIZAR EL SEGUNDO COMBO
                if ($("#blockTipoDef").hasClass('displayNoneListaPrecios')) {
                    $("#blockTipoDef").removeClass('displayNoneListaPrecios')
                    $("#blockTipoDef").removeClass('displayBlockListaPrecios')
                    $("#textListaPreciosAfec").text("Lista precios Afectada")
                    $("#listapreciobase").removeAttr('disabled')
                    $("#listapreciobase").val($(this).attr("listapreciobase")).trigger('change')

                }
            } else {
                $("#listapreciobase").attr('disabled', 'disabled')
            }

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

            /*let objCli = {}
            objCli.id = $(this).attr("id")
            objCli.grupo = $(this).attr("tipo1")
            objCli.vendedor = $(this).attr("vendedor")
            objCli.recaudador = $(this).attr("recaudador")
            objCli.pais = $(this).attr("pais")
            objCli.clasificacion = $(this).attr("clasificacion")
            objCli.provincia = $(this).attr("provincia")
            objCli.ciudad = $(this).attr("ciudad")
            objCli.cliente = $(this).attr("clientetag")*/

            // changeGrupoClientes(objCli) /* =========== OBJETO DE CLIENTE */

            let listaprecio = $(this).attr("listaprecio")
            let rentabilidad = $(this).attr("rentabilidad")
            // let comision = $(this).attr("comision")

            $("#rentabilidad").val(rentabilidad)
            $("#optionDef").val(2)
            $("#idDefinicionDet").val(id)


            $("#listaprecio").val($(this).attr("listaprecio")).trigger('change')

            for (let r = 0; r < 1; r++) {
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
                url: './?action=ve_processDefinicionesRenta',
                type: 'POST',
                data: datosform,
                success: function (respond) {
                    // console.log(respond)
                    let d = JSON.parse(respond)
                    // console.log(d)
                    if (d[0].substr(0, 1) == 0) { // VALIDA SI HUBO ERROR AL PROCESAR LA CABECERA DE DEFINICION DE
                        // RENTABILIDAD
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: d[0].substr(4),
                            showConfirmButton: false,
                            timer: 1000
                        })
                    } else { // MUESTRA EL MENSAJE DE CREACION O EDICION REALIZADA CON EXITO
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: d[0].substr(4),
                            showConfirmButton: false,
                            timer: 1000
                        })
                        if (d[0].substr(2, 1) == 1) { /*VALIDA SI LA RESPUESTA TRA EL PARAMETRO QUE IDENTIFICA LA
                         CREACION DEL LA NUEVA DEFINICION DE RENTABILIDAD*/
                            loadDefiniciones() // RECARGA LA TABLA DE CABECERA DE LA DEFINICION DE RENTABILIDAD
                            $("#modalDefPrecios").modal('hide') // ESCONDE LA VENTANA MODAL LUEGO DE CREAR LA
                            // DEFINICION DE RENTABILIDAD
                            let htmlSpan = '<span class="label label-default">' + nombre + '</span>' // ASIGNA EL
                            // NOMBRE CREADO PARA QUE SE PUEDA IDENTIFICAR LA DEFINICION QUE SE ESTA EDITANDO
                            $("#nameDefinicionEditar").html(htmlSpan) // MUESTRA EL NOMBRE DE LA NUEVA DEFINICION
                            $("#idDefinicionCab").val(d[1]) // SE ASIGNA EL ID DE LA NUEVA DEFINICION DE RENTABILIDAD
                            $("#nuevo-det-def").attr("idCab", d[1])  // SE ASIGNA EL ID DE LA NUEVA DEFINICION DE RENTABILIDAD
                            next() // EJECUTA LA ACCION QUE AVANZA AL SIGUIENTE PROCESO DESPUES DE CREAR LA DEFINICION
                            let num = $("#bnd-grupos").text() // SE TOMA EL VALOR QUE IDENTIFICA EL CONTENEDOR QUE
                            // SE ESTA VISUALIZANDO EN LA VENTANA
                            visibleNavAndSiguiente(parseInt(num)) // EJECUTA LA FUNCION QUE VISUALIZA LOS BOTONES DE
                            // SIGUIENTE O ANTERIOR
                        } else {
                            loadDefiniciones()// RECARGA LA TABLA DE CABECERA DE LA DEFINICION DE RENTABILIDAD
                            $("#modalDefPrecios").modal('hide')// ESCONDE LA VENTANA MODAL LUEGO DE CREAR LA
                            // DEFINICION DE RENTABILIDAD
                        }
                    }
                }
            })
        }) /* EJECUTA EL PROCESO DE GRABACION EN YA SEA PARA EL PROCESO DE CREACION DE NUEVA DEFINICION O PARA ACTUALIZARLA , TAMBIEN CAMBIA EL CONTENEDOR VISUAL LUEGO DE CREARLA */


        document.getElementById("grp-productos").addEventListener("change", function () {
            let objP = {}
            objP.grupo = $(this).val()
            changeGrupoProductos(objP)
        });

        /*document.getElementById("grp-productos-clientes").addEventListener("change", function () {
            let objC = {}
            objC.grupo = $(this).val()
            changeGrupoClientes(objC)
        });*/

        /* ===== FUNCION PARA HABILITAR LAS OPCIONES EN EL GRUPO DE CLIENTES */

        $(document).on('click', '#cancelProcess', function (event) {
            event.preventDefault()
            $("#formularioClientes").trigger('reset')
            $("#formularioProductos").trigger('reset')
            $("#formularioPrecios").load(' #formularioPrecios')
            $("#nameDefinicionEditar").text('DEFINICION DE PRECIOS')
            for (let r = 0; r <= 1; r++) {
                previous()
            }
        })

        /*function changeGrupoClientes(idGrupoCli) {
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
                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')
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

            } else if (idGrupoCli.grupo == 13) {
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

            } else if (idGrupoCli.grupo == 17) {
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

                $("#clasificacion").val(idGrupoCli.clasificacion).trigger('change')
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

            } else if (idGrupoCli.grupo == 24) {
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
        }*/

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
                    $("#etiqTags").removeAttr('disabled')
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
                    $("#etiqTags").val(idGrupoPro.categoria).trigger('change')
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
                    $("#etiqTags").val(idGrupoPro.marca).trigger('change')
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
                    $("#etiqTags").val(idGrupoPro.marca).trigger('change')
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
                    $("#etiqTags").val(idGrupoPro.scategoria).trigger('change')
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
                    $("#etiqTags").val(idGrupoPro.marca).trigger('change')
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

            let rentabilidad = $("#rentabilidad").val()
            if (rentabilidad.length != 0 && rentabilidad != '') {
                // if (desc != '' && desc.length != 0) {
                let allData = $("#formularioProductos, #formularioPrecios").serialize();
                $.ajax({
                    url: './?action=ve_processDefinicionesRe',
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
                /*} else {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: "Para grabar debe registrar descuento o comisión",
                        showConfirmButton: false,
                        timer: 1000
                    })
                }*/
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: "Para grabar debe registrar % de rentabilidad",
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

        $(document).on('click', ".btn-impr-def", function (e) {
            e.preventDefault()
            let id = $(this).attr('id')
            $.ajax({
                url: './?action=ve_printDataRenta',
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
                        // printJS({printable: rd.someJSONdata, properties: ['name', 'email', 'phone'], type: 'json'})
                        printJS(
                            {
                                printable: rd.data,
                                documentTitle: 'SMARTAG - Sistema empresarial.',
                                targetStyles: ['*'],
                                header: '<div class="title"><h3>' + rd.empresa.nameEmpresa + '</h3></div>' +
                                    '<div class="custom-h3"><h4 class="title">Descripción : ' + rd.cabecera.cabecera + '</h4></div>' +
                                    '<span>Tipo : ' + rd.cabecera.tipo + '</span>',
                                properties: ['#', 'Grp Productos', 'Etiqueta', 'Sub-etiqueta', 'Categoria', 'Sub-categoria', 'Producto', 'Marca', 'Etiquetas', 'Lista precio', 'Rentabilidad'],
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
