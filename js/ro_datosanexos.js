if(document.getElementById('ro_datosanexos')){
    
    $(document).ready(function(){
        
        function loadtipo(){
            $.ajax({
                url:'./?action=ro_processtablaroles',
                type:'POST',
                data:{option:1},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = '<option value="">Tipo de Rol</option>'
                    $.each(res, function(i, item){
                        viewHTML+='<option value="' +item.id+ '">'  +item.name+ '</option>'
                    })
                    $("#rolactual").html(viewHTML)
                }
            })
        }
        
        loadtipo()
        
        function loadempleado(){
            $.ajax({
                url:'./?action=ro_processdatosanexosform',
                type:'POST',
                data:{option:2},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = '<option value="">Tipo empleado</option>'
                    $.each(res, function(i, item){
                        viewHTML+='<option value="' +item.id+ '">'  +item.name+ '</option>'
                    })
                    $("#tipoemp").html(viewHTML)
                }
            })
        }
        
        loadempleado()
      
       $(document).on('click', '#add-data', function(e){
            e.preventDefault(e)
            let negocioId = $("#selectunegocio").val()
            let negocioTxt = $("#selectunegocio").find('option:selected').text();
            let costoId = $("#selectccosto").val()
            let costoTxt = $("#selectccosto").find('option:selected').text();
            let funcionId = $("#selectfunciones").val()
            let funcionTxt = $("#selectfunciones").find('option:selected').text();
            let rangofin = $("#cdrangofin").val()
            let rangoini = $("#cdrangoini").val()
            let valor = $("#addvalor").val()
            let firstanexo = $("#anexodefinible").val()
            let firstanexo1 = $("#anexodefinible").find('option:selected').text()
            if(firstanexo.length==0){
                Swal.fire({
                    icon:'error',
                    title:'Seleccione el anexo definible',
                })
            }else{
                let advalor = $("#addvalor").val()
                if(advalor.length==0){
                    Swal.fire({
                        icon:'error',
                        title:'Ingrese el valor para agregar la fila',
                    })
                }else{
                    let tbodytable = $("#tiporoldata")
                    if(tbodytable.children().length==0){
                        if(Number(valor)<=Number(rangofin)){
                            viewHTML ='<tr><td><input type="text" name="unegocio" value="'+negocioId+'" hidden>' + negocioTxt + 
                                '</td><td><input type="text" name="ccosto" value="'+costoId+'" hidden>' + costoTxt + 
                                '</td><td><input type="text" name="funciones" value="'+funcionId+'" hidden>' + funcionTxt + 
                                '</td><td style="text-align:right">' + valor + 
                                '</td><td>' + '<a class="cuadro btn-sm delete-line" title="Eliminar línea"><span class="glyphicon glyphicon-trash" aria-hidden="true" style="color:red">' + '</td></tr>';
                            $("#tiporoldata").append(viewHTML)
                            $("#addvalor").val(null)
                        }else{
                            Swal.fire({
                                icon:"warning",
                                position: "top",
                                showConfirmButton:false,
                                title:firstanexo1+" permite valores entre " + rangoini + " y " + rangofin,
                                showClass: {
                                popup: `
                                        animate__animated
                                        animate__fadeInUp
                                        animate__faster
                                `
                                },
                                timer:2000,
                                width:200,
                                        
                            })
                            $("#addvalor").val(null)
                        }
                    }else{
                        
                        
                        // $("#tiporoldata tr").each(function(){
                            // let und = $(this).find('td:eq(0)').text()
                            // let ccs = $(this).find('td:eq(1)').text()
                            // let fns = $(this).find('td:eq(2)').text()
                            if(validaRowsTable(costoTxt,funcionTxt,negocioTxt) == true){
                                Swal.fire({
                                    icon:"warning",
                                    position: "top",
                                    showConfirmButton:false,
                                    title:"Ya existe una fila con el mismo registro. Ingrese otro",
                                    showClass: {
                                    popup: `
                                            animate__animated
                                            animate__fadeInUp
                                            animate__faster
                                    `
                                    },
                                timer:2000,
                                width:200,
                                        
                                })
                                $("#addvalor").val(null)
                            }else{
                                if(Number(valor)<=Number(rangofin)){
                                    viewHTML ='<tr><td><input type="text" name="unegocio" value="'+negocioId+'" hidden>' + negocioTxt + 
                                        '</td><td><input type="text" name="ccosto" value="'+costoId+'" hidden>' + costoTxt + 
                                        '</td><td><input type="text" name="funciones" value="'+funcionId+'" hidden>' + funcionTxt + 
                                        '</td><td style="text-align:right">' + valor + 
                                        '</td><td>' + '<a class="cuadro btn-sm delete-line" title="Eliminar línea"><span class="glyphicon glyphicon-trash" aria-hidden="true" style="color:red">' + '</td></tr>';
                                    $("#tiporoldata").append(viewHTML)
                                    $("#addvalor").val(null)
                                }else{
                                    Swal.fire({
                                        icon:"warning",
                                        position: "top",
                                        showConfirmButton:false,
                                        title: firstanexo1 + " permite valores entre " + rangoini + " y " + rangofin,
                                        showClass: {
                                        popup: `
                                                animate__animated
                                                animate__fadeInUp
                                                animate__faster
                                        `
                                        },
                                        timer:2000,
                                        width:200,
                                                
                                    })
                                    $("#addvalor").val(null)
                                }
                            }
                        // }) // fin d each
                    }
                }
            }
        })
        
        function validaRowsTable(cc,fn,und){
            let result = false
            $("#tiporoldata tr").each(function(){
                if($(this).find('td:eq(0)').text() == und && $(this).find('td:eq(1)').text() == cc && $(this).find('td:eq(2)').text() == fn){
                   result = true
                }
            })
            return result
        }
        
        $("#save-data").on('click', function(e){
            let empleado = $("#emidlegal").val()
            if(empleado.length==0){
                Swal.fire({
                    icon:'error',
                    title:'Ingrese o busque los datos del empleado',
                })
            }else{
                let firstanexo = $("#anexodefinible").val()
                if(firstanexo.length==0){
                    Swal.fire({
                        icon:'error',
                        title:'Seleccione el anexo definible',
                    })
                }else{
                    let formDatos = new FormData()
                    $("#tiporoldata tr").each(function(){
                        let row = $(this)
                        let unegocio = row.find('td:eq(0)').find('input').val()
                        let ccosto = row.find('td:eq(1)').find('input').val()
                        let funciones = row.find('td:eq(2)').find('input').val()
                        let advalor = row.find('td:eq(3)').text()
                        formDatos.append("unegocio[]", unegocio)
                        formDatos.append("ccosto[]", ccosto)
                        formDatos.append("funciones[]", funciones)
                        formDatos.append("advalor[]", advalor)
                    })
                    formDatos.append("id", $("#transearch1").val())
                    if($("#fecha").is(':visible')){
                        formDatos.append("acfecha1", $("#fecha").val())
                    }else{
                        formDatos.append("acfecha", $("#truefecha").val())
                    }
                    formDatos.append("emid", $("#idempleado").val())
                    formDatos.append("peid", $("#monthnumber").val())
                    formDatos.append("acanio", $("#yearsave").val())
                    formDatos.append("cdid", $('#anexodefinible').val())
                    formDatos.append("acobserva", $("#conceptext").val())
                    formDatos.append("etqid", $("#etqtaid").val())
                    formDatos.append("subetqid", $("#setqtaid").val())
                    $.ajax({
                        url:'./?action=ro_processindatosanexos',
                        type:'POST',
                        data:formDatos,
                        contentType:false,
                        cache:false,
                        processData: false,
                        success:function(respond){
                            let res = JSON.parse(respond)
                            if(res.substr(0,1)==0){
                                Swal.fire({
                                    icon:'error',
                                    text:res.substr(2),
                                })
                            }else{
                                Swal.fire({
                                    icon:'success',
                                    title:res.substr(2),
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                        location.href='./?view=ro_datosanexos'   
                                    }
                                })
                            }
                        }
                    })
                }
            }
        })
        
        
      $(document).on('click','#btn-buscar-empleado',function(e){
          $("#modal-empleados-data").modal("show")
      })
      
      loadDataEmpleados()
      
      function loadDataEmpleados(){
          let opcion = 1
          $("#empleadosData").DataTable({
                    // "destroy": true,
                    // "keys": true,
                    "ajax": {
                        "method": "POST",
                        "url": "./?action=ro_empleadosDatos",
                        "data": {"option": opcion}
                    },

                    "columns": [
                        {"data" : "cedula"},
                        {"data" : "name"},
                    ],
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                    },
                })
        }
        
      $(document).on('click','#btn-buscar-trans',function(){
          $("#modal-trans-data").modal("show")
      })
      
      loadDataTrnasacciones()
      
      function loadDataTrnasacciones(){
          let opcion = 1
          $("#transaccionData").DataTable({
                    "ajax": {
                        "method": "POST",
                        "url": "./?action=ro_transaccionesDatos",
                        "data": {"option": opcion}
                    },

                    "columns": [
                        {"data" : "id"},
                        {"data" : "fecha"},
                        {"data" : "anexo"},
                        {"data" : "name"},
                    ],
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                    },
          })
        }
        
        $(document).on('click', '#filterdate', function(e){
            e.preventDefault()
            let desde = $("#min").val()
            let hasta = $("#max").val()
            $("#transaccionData").DataTable().destroy()
            
            $("#transaccionData").DataTable({
                    "ajax": {
                        "method": "POST",
                        "url": "./?action=ro_transaccionesFechas",
                        "data": {"desde":desde, "hasta":hasta}
                    },

                    "columns": [
                        {"data" : "id"},
                        {"data" : "fecha"},
                        {"data" : "anexo"},
                        {"data" : "name"},
                    ],
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                    },
          })
        })
 
        /*var minDate, maxDate;
 
        // Custom filtering function which will search data in column four between two values
        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                var min = minDate.val();
                var max = maxDate.val();
                var date = new Date( data[1] );
         
                if (
                    ( min === null && max === null ) ||
                    ( min === null && date <= max ) ||
                    ( min <= date && max === null ) ||
                    ( min <= date && date <= max ) ||
                    ( min >= date && date >= max )
                ) {
                    return true;
                }
                return false;
            }
        );
         
        $(document).ready(function() {
            // Create date inputs
            minDate = new DateTime($('#min'), {
                format: 'MMMM Do YYYY'
            });
            maxDate = new DateTime($('#max'), {
                format: 'MMMM Do YYYY'
            });
         
            // DataTables initialisation
            var table = $('#transaccionData').DataTable();
         
            // Refilter the table
            $(document).on('change', '#min, #max', function () {
                table.draw();
            });
        });*/
        
        
        $(document).on('click', '#empleadosData tbody tr', function(e){
          let celda =$(this).closest('tr').find('td:eq(0)').text()
          $.ajax({
              url:'./?action=ro_processdatosanexosform',
              type:'POST',
              data:{option:9, celda:celda},
              success:function(respond){
                  let res = JSON.parse(respond)
                  viewHTML =''
                  viewHTML1 =''
                  viewHTML2 =''
                  viewHTML3 = ''
                  $.each(res, function(i, item){
                      viewHTML = item.id
                      viewHTML1 = item.cedula
                      viewHTML2 = item.name + ' ' + item.apellido
                      viewHTML3 = item.tiporol
                  })
                  
                  $("#idempleado").val(viewHTML)
                  $("#emidlegal").val(viewHTML1)
                  $("#nameempleado").val(viewHTML2)
                  $("#tiporol").val(viewHTML3)
                  $("#tiporol").trigger('input')
                  $("#modal-empleados-data").modal("hide")
                  $("#new-card").removeAttr('disabled')
              }
          })
        })
        
        $(document).on('change', '#emidlegal', function(){
            let celda = $(this).val()
            $.ajax({
              url:'./?action=ro_processdatosanexosform',
              type:'POST',
              data:{option:9, celda:celda},
              success:function(respond){
                  let res = JSON.parse(respond)
                  viewHTML =''
                  viewHTML1 =''
                  viewHTML2 =''
                  viewHTML3 = ''
                  $.each(res, function(i, item){
                      viewHTML = item.id
                      viewHTML1 = item.cedula
                      viewHTML2 = item.name + ' ' + item.apellido
                      viewHTML3 = item.tiporol
                  })
                  
                  $("#idempleado").val(viewHTML)
                  $("#emidlegal").val(viewHTML1)
                  $("#nameempleado").val(viewHTML2)
                  $("#tiporol").val(viewHTML3)
                  $("#tiporol").trigger('input')
                  $("#new-card").removeAttr('disabled')
              }
          })
        })
        
        $(document).on('click', '#transaccionData tbody tr', function(e){
          let id =$(this).closest('tr').find('td:eq(0)').text()
          $.ajax({
              url:'./?action=ro_processdatosanexosform',
              type:'POST',
              data:{option:10, id:id},
              success:function(respond){
                  let res = JSON.parse(respond)
                    let viewHTML=''
                    $("#transearch").val(res.cabecera.acid)
                    $("#transearch1").val(res.cabecera.acid)
                    $("#fecha").hide()
                    $("#truefecha").show()
                    $("#fecha").hide()
                    $("#truefecha").show()
                    $("#truefecha").val(res.cabecera.fecha)
                    $("#idempleado").val(res.cabecera.emid)
                    $("#nameempleado").val(res.cabecera.empledo)
                    $("#emidlegal").val(res.cabecera.id)
                    $("#monthnumber").val(res.cabecera.peid)
                    $("#monthword").val(res.cabecera.mes)
                    $("#yearsave").val(res.cabecera.anio)
                    $("#yearnumber").val(res.cabecera.anio)
                    $("#anexodefinible1").val(res.cabecera.cdid)
                    $("#anexodefinible").val(res.cabecera.cdid).trigger('change')
                    $("#conceptext").val(res.cabecera.observacion)
                    $.each(res.detalle, function(i,item){
                        viewHTML+='<tr><td>' + '<input id="getid" value="'+item.negocioid+'"hidden>' + item.negocio + '</td><td>' + '<input id="getid" value="'+item.costoid+'"hidden>' + item.costo + '</td><td>' + '<input id="getid" value="'+item.funcionid+'"hidden>' + item.funciones + '</td><td style="text-align:right">' + item.valor + '</td><td>' + '<input id="getid" value="'+item.id+'"hidden>' + '<a class="cuadro btn-sm remove-ro_datosanexos" title="Eliminar"><span class="glyphicon glyphicon-trash" aria-hidden="true" style="color:red">' + '</td></tr>'
                    })
                    $("#tiporoldata").html(viewHTML)
                    $("#new-card").removeAttr('disabled')
                    $("#modal-trans-data").modal("hide")
                    $("#new-card").removeAttr('disabled')
                    $("#cancel-card").removeAttr('disabled')
                    $("#selectunegocio").removeAttr('disabled')
                    $("#selectccosto").removeAttr('disabled')
                    $("#selectfunctiones").removeAttr('disabled')
                    $("#addvalor").removeAttr('disabled')
                    $("#conceptext").removeAttr('disabled')
                    $("#emidlegal").removeAttr('disabled')
                    $("#transearch").attr('disabled', true)
                    $("#btn-buscar-trans").attr('disabled', true)
              }
          })
        })
        
        
        $(document).on('click', '#edit-card', function(e){
            e.preventDefault()
            $("#btn-buscar-trans").removeAttr('disabled')
            $("#transearch").removeAttr('disabled')
            $("#emidlegal").attr('disabled', true)
            $("#anexodefinible").attr('disabled', true)
            $("#conceptext").attr('disabled', true)
            $("#selectunegocio").attr('disabled', true)
            $("#selectccosto").attr('disabled', true)
            $("#selectfunciones").attr('disabled', true)
            $("#addvalor").attr('disabled', true)
            
        })
        
        $(document).on('click', '#new-card', function(e){
            e.preventDefault()
                  if($("#idempleado").val()==''){
                      $("#fecha").show()
                      $("#fecha").removeAttr('disabled')
                      $("#truefecha").hide()
                      $("#transearch").val(null)
                      $("#idempleado").val(null)
                      $("#emidlegal").val(null)
                      $("#nameempleado").val(null)
                      $("#tiporol").val(null)
                      $("#anexodefinible").removeAttr('disabled')
                      $("#anexodefinible1").val(null)
                      $("#conceptext").val(null)
                      $("#etqtaid").val(null)
                      $("#subetqtaid").val(null)
                      $("#monthnumber").val(null)
                      $("#monthword").val(null)
                      $("#yearsave").val(null)
                      $("#yearnumber").val(null)
                      $("#tiporoldata").empty()
                      $("#transearch").attr('disabled', true)
                      $("#emidlegal").removeAttr('disabled')
                      $("#conceptext").removeAttr('disabled')
                      $("#selectunegocio").removeAttr('disabled')
                      $("#selectccosto").removeAttr('disabled')
                      $("#selectfunciones").removeAttr('disabled')
                      $("#addvalor").removeAttr('disabled')
                      $("#btn-buscar-trans").attr('disabled',true)
                  }else{
                    Swal.fire({
                        icon:'warning',
                        title: 'Quiere realiar otra transacción?',
                        text: 'Se ha modificado algunos datos en la transacción actual',
                        showCancelButton:'true',
                        confirmButtonColor:'3085d6',
                        cancelButtonColor:'#d33',
                        confirmButtonText:'Si, confirmar',
                    }).then((result)=>{
                            if(result.isConfirmed){
                              $("#fecha").show()
                              $("#fecha").removeAttr('disabled')
                              $("#truefecha").hide()
                              $("#transearch").val(null)
                              $("#idempleado").val(null)
                              $("#emidlegal").val(null)
                              $("#nameempleado").val(null)
                              $("#tiporol").val(null)
                              $("#anexodefinible").removeAttr('disabled')
                              $("#anexodefinible1").val(null)
                              $("#conceptext").val(null)
                              $("#etqtaid").val(null)
                              $("#subetqtaid").val(null)
                              $("#monthnumber").val(null)
                              $("#monthword").val(null)
                              $("#yearsave").val(null)
                              $("#yearnumber").val(null)
                              $("#tiporoldata").empty()
                              $("#selectunegocio").removeAttr('disabled')
                              $("#selectccosto").removeAttr('disabled')
                              $("#selectfunciones").removeAttr('disabled')
                              $("#transearch").attr('disabled', true)
                              $("#btn-buscar-trans").attr('disabled', true)
                            }
                        })
                    }
        })
      
      
        $(document).on('input', '#tiporol', function(){
            let rol = $(this).val();
            $.ajax({
                url:'./?action=ro_processdatosanexosform',
                type:'POST',
                data:{option:3, rol:rol},
                success:function(respond){
                    let res = JSON.parse(respond)
                    $("#monthnumber").val(res[0])
                    $("#monthword").val(res[1])
                    $("#yearsave").val(res[2])
                    $("#yearnumber").val(res[2])
                }
            })
        })
        
        $(document).on('change', '#transearch', function(){
            let id = $(this).val()
            $.ajax({
                url:'./?action=ro_processdatosanexosform',
                type:'POST',
                data:{option: 10, id:id},
                success:function(respond){
                    let res = JSON.parse(respond)
                        let viewHTML=''
                        if(res.cabecera.estado==2){
                            Swal.fire({
                                icon:"warning",
                                position: "top",
                                showConfirmButton:false,
                                title: "Ésta transacción está anulada",
                                showClass: {
                                popup: `
                                        animate__animated
                                        animate__fadeInUp
                                        animate__faster
                                `
                                },
                                timer:2000,
                                width:200,
                            })
                        }else if(res.cabecera.estado==3){
                            Swal.fire({
                                icon:"warning",
                                position: "top",
                                showConfirmButton:false,
                                title: "Ésta transacción está siendo procesada en el rol",
                                showClass: {
                                popup: `
                                        animate__animated
                                        animate__fadeInUp
                                        animate__faster
                                `
                                },
                                timer:2000,
                                width:200,
                            })
                        }else{
                            $("#fecha").hide()
                            $("#truefecha").show()
                            $("#transearch1").val(res.cabecera.acid)
                            $("#truefecha").val(res.cabecera.fecha)
                            $("#idempleado").val(res.cabecera.emid)
                            $("#nameempleado").val(res.cabecera.empledo)
                            $("#emidlegal").val(res.cabecera.id)
                            $("#monthnumber").val(res.cabecera.peid)
                            $("#monthword").val(res.cabecera.mes)
                            $("#yearsave").val(res.cabecera.anio)
                            $("#yearnumber").val(res.cabecera.anio)
                            $("#anexodefinible1").val(res.cabecera.cdid)
                            $("#anexodefinible").val(res.cabecera.cdid).trigger('change')
                            $("#conceptext").val(res.cabecera.observacion)
                            $.each(res.detalle, function(i,item){
                                viewHTML+='<tr><td>' + '<input id="getid" value="'+item.negocioid+'"hidden>' + item.negocio + '</td><td>' + '<input id="getid" value="'+item.costoid+'"hidden>' + item.costo + '</td><td>' + '<input id="getid" value="'+item.funcionid+'"hidden>' + item.funciones + '</td><td style="text-align:right">' + item.valor + '</td><td>' + '<input id="getid" value="'+item.id+'"hidden>' + '<a class="cuadro btn-sm remove-ro_datosanexos" title="Eliminar"><span class="glyphicon glyphicon-trash" aria-hidden="true" style="color:red">' + '</td></tr>'
                            })
                            $("#tiporoldata").html(viewHTML)
                            $("#new-card").removeAttr('disabled')
                            $("#cancel-card").removeAttr('disabled')
                            $("#transearch").attr('disabled', true)
                            $("#btn-buscar-trans").attr('disabled', true)
                            $("#selectunegocio").removeAttr('disabled')
                            $("#selectccosto").removeAttr('disabled')
                            $("#selectfunctiones").removeAttr('disabled')
                            $("#addvalor").removeAttr('disabled')
                            $("#conceptext").removeAttr('disabled')
                            $("#emidlegal").removeAttr('disabled')
                            $("#transearch").attr('disabled', true)
                            $("#btn-buscar-trans").attr('disabled', true)
                        }
                }
            })
        })
        
        $(document).on('click', '.delete-line', function(){
            $(this).closest('tr').remove()
        })
        
        $(document).on('click', '.remove-ro_datosanexos', function(){
            Swal.fire({
                title: 'Borrar línea?',
                text:'Se elíminará la fila actual de forma temporal',
                icon:'warning',
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                if(result.isConfirmed){
                    $(this).closest('tr').remove()
                }
            })
        })
        
        function loaddetalledata(){
            let id = ''
            if(document.getElementById('transearch')){
                id = $("#transearch").val()
            }
            $.ajax({
                url:'./?action=ro_processdatosanexosform',
                type:'POST',
                data:{option:10, id:id},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML=''
                    
                    $("#truefecha").val(res.cabecera.fecha)
                    $("#nameempleado").val(res.cabecera.empledo)
                    $("#emidlegal").val(res.cabecera.id)
                    $("#monthword").val(res.cabecera.mes)
                    $("#yearnumber").val(res.cabecera.anio)
                    $("#anexodefinible1").val(res.cabecera.cdid)
                    $("#anexodefinible").val(res.cabecera.cdid).trigger('change')
                    $("#conceptext").val(res.cabecera.observacion)
                    $.each(res.detalle, function(i,item){
                        viewHTML+='<tr><td>' + '<input id="getid" value="'+item.negocioid+'"hidden>' + item.negocio + '</td><td>' + '<input id="getid" value="'+item.costoid+'"hidden>' + item.costo + '</td><td>' + '<input id="getid" value="'+item.funcionid+'"hidden>' + item.funciones + '</td><td style="text-align:right">' + item.valor + '</td><td>' + '<input id="getid" value="'+item.id+'"hidden>' + '<a class="cuadro btn-sm remove-ro_datosanexos" title="Eliminar"><span class="glyphicon glyphicon-trash" aria-hidden="true" style="color:red">' + '</td></tr>'
                    })
                    $("#tiporoldata").html(viewHTML)
                }
            })
        }
        
        function loadcampos(){
        let valorcampo = ''
        if(document.getElementById('anexodefinible1')){
            valorcampo = $("#anexodefinible1").val()
        }
            $.ajax({
                url:'./?action=ro_processdatosanexosform',
                type:'POST',
                data:{option:4},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML='<option value="">Datos definibles</option>'
                    let selected = ''
                    $.each(res, function(i, item){
                        if(valorcampo=''){
                            if(valorcampo==item.id){
                                selected='selected'
                            }else{
                                selected=''
                            }
                        }
                        viewHTML+='<option value="' +item.id+'"'+selected+'>'  +item.name+ '</option>'
                        
                    })
                    $("#anexodefinible").html(viewHTML)
                }
            })
        }
        
        loadcampos()
        
        
        $(document).on('change', '#anexodefinible', function(){
            changeDataAnexo($(this).val())
        })
        
        function changeDataAnexo(datosanexos){
            $.ajax({
                url:'./?action=ro_processdatosanexosform',
                type:'POST',
                data:{option:5, datosanexos:datosanexos},
                success:function(respond){
                    let res = JSON.parse(respond)
                    $("#cdclase").val(res.cdclase)
                    $("#cdclase").trigger('input')
                    $("#cdrangoini").val(res.cdrangoini)
                    $("#cdrangofin").val(res.cdrangofin)
                    $("#etqtaid").val(res.etqid)
                    $("#etqtaid").trigger('input')
                    $("#setqtaid").val(res.subetqid)
                    $("#anexodefinible").attr('disabled', true)
                    
                }
            })
        }
        
        $(document).on('input', '#etqtaid', function(){
            let etiqueta = $(this).val();
            $.ajax({
                url:'./?action=ro_processdatosanexosform',
                type:'POST',
                data:{option:11, etiqueta:etiqueta},
                success:function(respond){
                    let res = JSON.parse(respond)
                    $("#funciones").val(res[0])
                    $("#negocio").val(res[1])
                }
            })
        })
        
        $(document).on('input', '#etqtaid', function(){
            let etiqueta = $(this).val()
            $.ajax({
                url:'./?action=ro_processdatosanexosform',
                type:'POST',
                data:{option:6,etiqueta:etiqueta},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let htmlUnidades = ''
                    let htmlFunciones= ''
                    if($("#negocio").val()==1){
                        if(res.unidades){
                        let selected=''
                        $.each(res.unidades, function(i, item){
                            
                            htmlUnidades += '<option value="' + item.id + '"'+selected+'>' + item.name + '</option>'
                        })
                        $("#selectunegocio").html(htmlUnidades)
                        $("#selectunegocio").trigger('change')
                        }
                    }else{
                        $("#selectunegocio").attr('disabled', true)
                            htmlUnidades +='<option value="">No disponible</option>'
                        $("#selectunegocio").html(htmlUnidades)
                    }
                    
                    if($("#funciones").val()==1){
                        if(res.funciones){
                        let selected=''
                        $.each(res.funciones, function(i, item){
                            
                            htmlFunciones += '<option value="' +item.id + '"'+selected+'>' + item.name + '</option>'
                        })
                        $("#selectfunciones").html(htmlFunciones)
                        }
                    }else{
                        $("#selectfunciones").attr('disabled', true)
                            htmlFunciones+='<option value="">No disponible</option>'
                        $("#selectfunciones").html(htmlFunciones)
                    }
                }
            })
        })
        
        $(document).on('change', '#selectunegocio', function(){
            let centro = $(this).val()
            $.ajax({
                url:'./?action=ro_processdatosanexosform',
                type:'POST',
                data:{option:7, centro:centro},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML=''
                    let selected=''
                    $.each(res, function(i,item){
                        viewHTML +='<option value="' +item.id + '"'+selected+'>' + item.name +'</option>'
                    })
                    $("#selectccosto").html(viewHTML)
                }
            })
        })
            
        
        $("#searching").on('click', function(e){
            e.preventDefault(e)
            let emidlegal = ''
            if(document.getElementById('emidlegal')){
                emidlegal = $("#emidlegal").val()
            }
            $.ajax({
                url:'./?action=ro_processdatosanexosform',
                type:'POST',
                data:{option:8, emidlegal:emidlegal},
                success:function(respond){
                    let res = JSON.parse(respond)
                    viewHTML = ''
                    viewHTML1 = ''
                    $.each(res, function(i, item){
                        viewHTML = item.id
                        viewHTML1 = item.name + ' ' + item.apellido
                    })
                    $("#idempleado").val(viewHTML)
                    $("#nameempleado").val(viewHTML1)
                }
            })
        })
        
        $(document).on('click', '#cancel-card', function(e){
            let id = $("#transearch").val()
            let anular = 2
            Swal.fire({
                icon:'warning',
                title: 'Quiere anular la transacción?',
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                if(result.isConfirmed){
                    $.ajax({
                        url:'./?action=ro_processdatosanexosdetallecancel',
                        type:'POST',
                        data:{id:id,anular:anular},
                        success:function(respond){
                            let res = JSON.parse(respond)
                            if(res.substr(0,1)==0){
                                Swal.fire({
                                    icon:"error",
                                        position: "top",
                                    showConfirmButton:false,
                                    title:res.substr(2),
                                    showClass: {
                                    popup: `
                                            animate__animated
                                            animate__fadeInUp
                                            animate__faster
                                    `
                                    },
                                    timer:2000,
                                    width:200,
                                })
                            }else{
                                Swal.fire({
                                    icon:"success",
                                    position: "top",
                                    title:res.substr(2),
                                    showClass: {
                                    popup: `
                                            animate__animated
                                            animate__fadeInUp
                                            animate__faster
                                    `
                                    },
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                        location.href='./?view=ro_datosanexos'
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        /*$("#save-applied").on('click', function(e){
            e.preventDefault
            $.ajax({
                url:'./?action=ro_processindatosanexos',
                type:'POST',
                data:$("#datosanexosformin").serialize(),
                success:function(respond){
                    let res = JSON.parse(respond)
                    if(res.substr(0,1)==0){
                        Swal.fire({
                            icon:"error",
                                position: "top",
                            showConfirmButton:false,
                            title:res.substr(2),
                            showClass: {
                            popup: `
                                    animate__animated
                                    animate__fadeInUp
                                    animate__faster
                            `
                            },
                            timer:2000,
                                idth:200,
                        })
                    }else{
                        Swal.fire({
                            icon:"success",
                            position: "top",
                            showConfirmButton:false,
                            title:res.substr(2),
                            showClass: {
                            popup: `
                                    animate__animated
                                    animate__fadeInUp
                                    animate__faster
                            `
                            },
                            timer:2000,
                            width:200,
                        })
                        loadanexos()
                    }
                }
            })
        })*/
        
        /*function loadanexos(){
            $.ajax({
                url:'./?action=ro_processdatosanexosform',
                type:'POST',
                data:{option:10},
                success:function(respond){
                    let res = JSON.parse(respond)
                    viewHTML =''
                    viewHTML1 =''
                    viewHTML2 =''
                    viewHTML3 =''
                    viewHTML4 =''
                    let suma = 0
                    $.each(res, function(i,item){
                        
                        viewHTML+= '<tr><td>' + item.id + 
                        '</td><td>' + item.empleado + ' ' + item.empleado1 +
                        '</td><td>' + item.negocio + 
                        '</td><td>' + item.costo + 
                        '</td><td>' + item.funcion + 
                        '</td><td class="valordata">' + item.valor + 
                        '</td><td><a class="cuadro btn-sm remove-ro_datosanexos" title="Eliminar"><span class="glyphicon glyphicon-trash" aria-hidden="true" style="color:red"></span></a></td></tr>'
                        
                        suma+= parseFloat(item.valor)
                        
                    })
                    
                    $("#dataanexosempleado").html(viewHTML)
                    $("#loadtotal").val(suma.toFixed(2))
                }
            })
        }
        loadanexos()*/
        
        
    })
    
}