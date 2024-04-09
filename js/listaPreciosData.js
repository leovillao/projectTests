$(function () {
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
    $('#buscarProducto').keyup(function (event) {
        var codigo = event.key;
        if(codigo === 8 || codigo === 46){
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
            url: './?action=listaprecios_productos',
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
                // console.log(r)
                if (r.data != "") {
                    const numPrecios = $(".valoresNumerico")
                    let totalPreciosInput = numPrecios.length
                    for (let o = 1; o <= totalPreciosInput; o++) {
                        $(".namePrice" + o).val(r.precios['precio' + o])
                    }

                    r.data.forEach(function (item, dato) {
                        let etq = ""
                        option += '<tr class="productoData" data-id="' + item.itid + '">' +
                            '<td>' + item.codigo + '</td>' +
                            '<td>' + item.name + '</td>' +
                            '<td>' + item.unidad + '</td>' +
                            '<td>' + item.costo + '</td>' +
                            '<td style="' + getColorStatus(item.pvp1) + '" >' + item.pvp1 + '</td>' +
                            '<td style="' + getColorStatus(item.pvp2) + '" >' + item.pvp2 + '</td>' +
                            '<td style="' + getColorStatus(item.pvp3) + '" >' + item.pvp3 + '</td>' +
                            '<td style="' + getColorStatus(item.pvp4) + '" >' + item.pvp4 + '</td>' +
                            '<td style="' + getColorStatus(item.pvp5) + '" >' + item.pvp5 + '</td>' +
                            '<td style="' + getColorStatus(item.pvp6) + '" >' + item.pvp6 + '</td>' +
                            '<td style="' + getColorStatus(item.pvp7) + '" >' + item.pvp7 + '</td>' +
                            '<td style="' + getColorStatus(item.pvp8) + '" >' + item.pvp8 + '</td>' +
                            '<td style="' + getColorStatus(item.pvp9) + '" >' + item.pvp9 + '</td>' +
                            '<td style="' + getColorStatus(item.pvp10) + '" >' + item.pvp10 + '</td>' +
                            '</tr>';
                    })
                    let totProductos = r.tp
                    $("#totalProductos").text("Total de productos : " + r.tp)
                    $('#tbody-productos').html(option);

                    let totalProvee = document.querySelectorAll('.productoData')

                    if (parseInt(totProductos) > parseInt(totalProvee.length)) {
                        if (ultimoProducto) {
                            observador.unobserve(ultimoProducto);
                        }
                        const productosVisibles = document.querySelectorAll('.productoData')
                        ultimoProducto = productosVisibles[productosVisibles.length - 1]
                        observador.observe(ultimoProducto)
                    }
                } else {
                    option += '<tr style="background-color: rgba(117, 117, 117,.4)"><td colspan="14"><b>SIN LISTA DE PRECIOS ASOCIADA</b></td></tr>';
                    $('#tbody-productos').html(option);
                    $("#totalProductos").text("Total de productos : 0 ")
                }
            }
        })
    }

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
    $(document).on("click", "#tbody-productos tr", function () {
        let id = $(this).attr('data-id')
        $.ajax({
            url: "./?action=listaprecios_dataRenta",
            type: "POST",
            data: {"id": id},
            success: function (respond) {
                let t = JSON.parse(respond)
                let option = ""
                // $.each(t.precios,function (item, dato) {
                $("#tituloProductoData").text(t.productos.name)
                $("#idProducto").val(id)

                if (t.productos.nameprecio1.substr(0, 3) !== "PVP") {
                    $('.namePrice1').val(parseFloat(t.productos.pvp1).toFixed(2))
                    option += "<tr>\n" +
                        "<td>" + t.productos.nameprecio1 + "</td>\n" +
                        "<td><input type='' size='4' class='valoresNumericos inputValor' num='1' valordb='" + t.productos.pvp1 + "' value='" + t.productos.pvp1 + "'></td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Margen</td>\n" +
                        "<td>" + t.productos.margen1 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Rentabilidad</td>\n" +
                        "<td>" + t.productos.renta1 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td colspan='2' style='background-color: darkgray'></td>\n" +
                        "</tr>"
                }
                if (t.productos.nameprecio2.substr(0, 3) !== "PVP") {
                    $('.namePrice2').val(parseFloat(t.productos.pvp2).toFixed(2))

                    option += "<tr>\n" +
                        "<td>" + t.productos.nameprecio2 + "</td>\n" +
                        "<td><input type='' size='4'  class='valoresNumericos inputValor' num='2'  valordb='" + t.productos.pvp2 + "'  value='" + t.productos.pvp2 + "'></td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Margen</td>\n" +
                        "<td>" + t.productos.margen2 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Rentabilidad</td>\n" +
                        "<td>" + t.productos.renta2 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td colspan='2' style='background-color: darkgray'></td>\n" +
                        "</tr>";
                }

                if (t.productos.nameprecio3.substr(0, 3) !== "PVP") {
                    $('.namePrice3').val(t.productos.pvp3)

                    option += "<tr>\n" +
                        "<td>" + t.productos.nameprecio3 + "</td>\n" +
                        "<td><input type='' size='4'   class='valoresNumericos inputValor' num='3'  valordb='" + t.productos.pvp3 + "'  value='" + t.productos.pvp3 + "'></td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Margen</td>\n" +
                        "<td>" + t.productos.margen3 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Rentabilidad</td>\n" +
                        "<td>" + t.productos.renta3 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td colspan='2' style='background-color: darkgray'></td>\n" +
                        "</tr>";
                }

                if (t.productos.nameprecio4.substr(0, 3) !== "PVP") {
                    $('.namePrice4').val(parseFloat(t.productos.pvp4).toFixed(2))

                    option += "<tr>\n" +
                        "<td>" + t.productos.nameprecio4 + "</td>\n" +
                        "<td><input type='' size='4'   class='valoresNumericos inputValor' num='4'  valordb='" + t.productos.pvp4 + "'  value='" + t.productos.pvp4 + "'></td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Margen</td>\n" +
                        "<td>" + t.productos.margen4 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Rentabilidad</td>\n" +
                        "<td>" + t.productos.renta4 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td colspan='2' style='background-color: darkgray'></td>\n" +
                        "</tr>";
                }

                if (t.productos.nameprecio5.substr(0, 3) !== "PVP") {
                    $('.namePrice5').val(parseFloat(t.productos.pvp5).toFixed(2))

                    option += "<tr>\n" +
                        "<td>" + t.productos.nameprecio5 + "</td>\n" +
                        "<td><input type='' size='4'   class='valoresNumericos inputValor' num='5'  valordb='" + t.productos.pvp5 + "'  value='" + t.productos.pvp5 + "'></td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Margen</td>\n" +
                        "<td>" + t.productos.margen5 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Rentabilidad</td>\n" +
                        "<td>" + t.productos.renta5 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td colspan='2' style='background-color: darkgray'></td>\n" +
                        "</tr>";
                }

                if (t.productos.nameprecio6.substr(0, 3) !== "PVP") {
                    $('.namePrice6').val(parseFloat(t.productos.pvp6).toFixed(2))

                    option += "<tr>\n" +
                        "<td>" + t.productos.nameprecio6 + "</td>\n" +
                        "<td><input type='' size='4'   class='valoresNumericos inputValor' num='6'  valordb='" + t.productos.pvp6 + "'  value='" + t.productos.pvp6 + "'></td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Margen</td>\n" +
                        "<td>" + t.productos.margen6 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Rentabilidad</td>\n" +
                        "<td>" + t.productos.renta6 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td colspan='2' style='background-color: darkgray'></td>\n" +
                        "</tr>" +
                        "<tr>\n";
                }

                if (t.productos.nameprecio7.substr(0, 3) !== "PVP") {
                    $('.namePrice7').val(parseFloat(t.productos.pvp7).toFixed(2))

                    option += "<td>" + t.productos.nameprecio7 + "</td>\n" +
                        "<td><input type='' size='4'   class='valoresNumericos inputValor' num='7'  valordb='" + t.productos.pvp7 + "'  value='" + t.productos.pvp7 + "'></td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Margen</td>\n" +
                        "<td>" + t.productos.margen7 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Rentabilidad</td>\n" +
                        "<td>" + t.productos.renta7 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td colspan='2' style='background-color: darkgray'></td>\n" +
                        "</tr>";
                }

                if (t.productos.nameprecio8.substr(0, 3) !== "PVP") {
                    $('.namePrice8').val(parseFloat(t.productos.pvp8).toFixed(2))

                    option += "<tr>\n" +
                        "<td>" + t.productos.nameprecio8 + "</td>\n" +
                        "<td><input type='' size='4'   class='valoresNumericos inputValor' num='8'  valordb='" + t.productos.pvp8 + "'  value='" + t.productos.pvp8 + "'></td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Margen</td>\n" +
                        "<td>" + t.productos.margen8 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Rentabilidad</td>\n" +
                        "<td>" + t.productos.renta8 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td colspan='2' style='background-color: darkgray'></td>\n" +
                        "</tr>";
                }


                if (t.productos.nameprecio9.substr(0, 3) !== "PVP") {
                    $('.namePrice9').val(parseFloat(t.productos.pvp9).toFixed(2))

                    option += "<tr><td>" + t.productos.nameprecio9 + "</td>\n" +
                        "<td><input type='' size='4'   class='valoresNumericos inputValor' num='9'  valordb='" + t.productos.pvp9 + "'  value='" + t.productos.pvp9 + "'></td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Margen</td>\n" +
                        "<td>" + t.productos.margen9 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Rentabilidad</td>\n" +
                        "<td>" + t.productos.renta9 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td colspan='2' style='background-color: darkgray'></td>\n" +
                        "</tr>";
                }

                if (t.productos.nameprecio10.substr(0, 3) !== "PVP") {
                    $('.namePrice10').val(parseFloat(t.productos.pvp10).toFixed(2))

                    option += "<tr><td>" + t.productos.nameprecio10 + "</td>\n" +
                        "<td><input type='' size='4'   class='valoresNumericos inputValor'  num='10'  valordb='" + t.productos.pvp10 + "'  value='" + t.productos.pvp10 + "'></td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Margen</td>\n" +
                        "<td>" + t.productos.margen10 + "</td>\n" +
                        "</tr>\n" +
                        "<tr>\n" +
                        "<td>Rentabilidad</td>\n" +
                        "<td>" + t.productos.renta10 + "</td>\n"
                }

                // })
                $("#tableDataRentabilidad").html(option);
            }
        })
    })

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
