if(document.getElementById('ro_haberdesc')){
    
    $(document).ready(function(){
        
        
        
      $(document).on('click', '#open-card', function(e){
            e.preventDefault()
            $("#btn-buscar-trans").removeAttr('disabled')
            $("#transearch").removeAttr('disabled')
            $("#transearch").focus()
            $("#print-transaction").hide()
            $("#hdfecha1").attr('disabled', true)
            $("#emidlegal").attr('disabled', true)
            $("#cdid").attr('disabled', true)
            $("#tbid").attr('disabled', true)
            $("#dtid").attr('disabled', true)
            $("#hdtotal").attr('disabled', true)
            $("#hdcuotas").attr('disabled', true)
            $("#hdintervalo").attr('disabled', true)
            $("#peid").attr('disabled', true)
            $("#hdanio").attr('disabled', true)
            $("#hdobserva").attr('disabled', true)
            
      })
      
      $(document).on('click', '#new-card', function(e){
            e.preventDefault()
            if($("#hdfecha1").is(':disabled')){
                $("#btn-buscar-trans").show()
                $("#transearch").show()
                $("#btn-buscar-trans").attr('disabled', true)
                $("#transearch").attr('disabled', true)
                $("#print-transaction").hide()
                $("#hdfecha1").show()
                $("#hdfecha").hide()
                $("#hdfecha1").removeAttr('disabled')
                $("#emidlegal").removeAttr('disabled')
                $("#cdid").removeAttr('disabled')
                $("#tbid").removeAttr('disabled')
                $("#dtid").removeAttr('disabled')
                $("#hdtotal").removeAttr('disabled')
                $("#hdcuotas").removeAttr('disabled')
                $("#hdintervalo").removeAttr('disabled')
                $("#peid").removeAttr('disabled')
                $("#hdanio").removeAttr('disabled')
                $("#hdobserva").removeAttr('disabled')
            }else{
                if($("#emidlegal").val()!=''){
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
                                $("#btn-buscar-trans").attr('disabled', true)
                                $("#transearch").attr('disabled', true)
                                $("#nameempleado").val(null)
                                $("#print-transaction").hide()
                                $("#transearch").val(null)
                                $("#hdfecha1").removeAttr('disabled')
                                $("#emidlegal").val(null)
                                $("#hdtotal").val(null)
                                $("#hdcuotas").val(null)
                                $("#hdintervalo").val(null)
                                $("#peid").val(null)
                                $("#hdanio").val(null)
                                $("#hdobserva").val(null)
                                $("#haberdescdata").empty()
                            }
                    })   
                }
            }
            
      })
      
      $("#save-data").on('click', function(e){
            let suma = 0
            let convert = 0
            let totales = $("#hdtotal").val()
            let empleado = $("#emidlegal").val()
            if(empleado.length==0){
                Swal.fire({
                    icon:'error',
                    title:'Ingrese o busque los datos del empleado',
                })
            }else{
                    let formDatos = new FormData()
                    $("#haberdescdata tr").each(function(){
                        let row = $(this)
                        let ddanio = row.find('td:eq(0)').text()
                        let peidcount = row.find('td:eq(1)').find('input').val()
                        let ddcuota = row.find('td:eq(2)').text()
                        let ddvalor = row.find('td:eq(3)').find('input').val()
                        let valorto = $("#valor").val()
                        let ddsaldo = row.find('td:eq(2)').find('input').val()
                        formDatos.append("ddanio[]", ddanio)
                        formDatos.append("peidcount[]", peidcount)
                        formDatos.append("ddcuota[]", ddcuota)
                        formDatos.append("ddvalor[]", ddvalor)
                        formDatos.append("ddabono[]", ddvalor)
                        formDatos.append("ddsaldo[]", ddsaldo)
                        convert = ddvalor
                        suma += Number(convert)
                        
                    })
                    formDatos.append("id", $("#transearch1").val())
                    if($("#hdfecha1").is(':visible')){
                        formDatos.append("hdfecha1", $("#hdfecha1").val())
                    }else{
                        formDatos.append("hdfecha", $("#hdfecha").val())
                    }
                    formDatos.append("emid", $("#emid").val())
                    formDatos.append("cdid", $('#cdid').val())
                    formDatos.append("tbid", $('#tbid').val())
                    formDatos.append("dtid", $('#dtid').val())
                    formDatos.append("hdtotal", $('#hdtotal').val())
                    formDatos.append("hdsaldo", $('#hdsaldo').val())
                    formDatos.append("hdcuotas", $('#hdcuotas').val())
                    formDatos.append("hdintervalo", $('#hdintervalo').val())
                    formDatos.append("peid", $("#peid").val())
                    formDatos.append("hdanio", $("#hdanio").val())
                    formDatos.append("hdobserva", $("#hdobserva").val())
                    if(suma.toFixed(2)>Number(totales)){
                        Swal.fire({
                            icon:"warning",
                            position: "top",
                            showConfirmButton:false,
                            title: "La suma total de las cuotas es de: $"+suma.toFixed(2),
                            text: "Por favor, modifique una de las cuotas para equilibrar el total de las cuotas con el total original",
                            showClass: {
                            popup: `
                                    animate__animated
                                    animate__fadeInUp
                                    animate__faster
                            `
                            },
                            timer:5000,
                            width:600,
                        })
                    }else{
                        $.ajax({
                            url:'./?action=ro_processinhaberdesc',
                            type:'POST',
                            data:formDatos,
                            contentType:false,
                            cache:false,
                            processData: false,
                            success:function(respond){
                                let res = JSON.parse(respond)
                                if(res.mensaje.substr(0,1)==0){
                                    Swal.fire({
                                        icon:'error',
                                        text:res.mensaje.substr(2),
                                    })
                                }else{
                                    Swal.fire({
                                        icon:'success',
                                        title:res.mensaje.substr(2),
                                    }).then((result)=>{
                                        window.open('reportes/RRHH/comprobante.php?id='+res.id)
                                        if(result.isConfirmed){
                                            location.href='./?view=ro_haberdesc'
                                        }
                                    })
                                }
                            }
                        })
                    }
            }
        })
      
       $(document).on('click', '#anular-card', function(){
            let id = $("#transearch1").val()
            let anular = 0
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
                        url:'./?action=ro_processhaberdesccancel',
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
                                        location.href='./?view=ro_haberdesc'
                                    }
                                })
                            }
                        }
                    })
                }
            })
      })
      
      $(document).on('change', '#transearch', function(){
            let id = $(this).val()
            $.ajax({
                url:'./?action=ro_processhaberdescform',
                type:'POST',
                data:{option:6, id:id},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = ''
                    if(res.cabecera.hdestado==0){
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
                                width:300,
                        })
                    }else{
                        if(res.cabecera.obligacion!=null){
                             Swal.fire({
                                icon:"warning",
                                    position: "top",
                                    showConfirmButton:false,
                                    title: "Ésta transacción ya está relacionada con una obligación de tesorería",
                                    showClass: {
                                    popup: `
                                            animate__animated
                                            animate__fadeInUp
                                            animate__faster
                                    `
                                    },
                                    timer:4000,
                                    width:600,
                            })
                        }else{
                            $("#hdfecha1").hide()
                            $("#hdfecha").show()
                            $("#emidlegal").removeAttr('disabled')
                            $("#tbid").removeAttr('disabled')
                            $("#cdid").removeAttr('disabled')
                            $("#dtid").removeAttr('disabled')
                            $("#hdtotal").removeAttr('disabled')
                            $("#hdcuotas").removeAttr('disabled')
                            $("#hdintervalo").removeAttr('disabled')
                            $("#peid").removeAttr('disabled')
                            $("#hdanio").removeAttr('disabled')
                            $("#hdobserva").removeAttr('disabled')
                            $("#hdfecha").val(res.cabecera.hdfecha)
                            $("#transearch").val(res.cabecera.hdid)
                            $("#transearch1").val(res.cabecera.hdid)
                            $("#emid").val(res.cabecera.emid)
                            $("#emidlegal").val(res.cabecera.id)
                            $("#nameempleado").val(res.cabecera.empleado)
                            $("#peid1").val(res.cabecera.peid)
                            $("#peid").val(res.cabecera.peid).trigger('change')
                            $("#tbid1").val(res.cabecera.tbid)
                            $("#tbid").val(res.cabecera.tbid).trigger('change')
                            $("#cdid1").val(res.cabecera.cdid)
                            $("#cdid").val(res.cabecera.cdid).trigger('change')
                            $("#dtid1").val(res.cabecera.dtid)
                            $("#dtid").val(res.cabecera.dtid).trigger('change')
                            $("#hdtotal").val(res.cabecera.hdtotal)
                            $("#hdcuotas").val(res.cabecera.hdcuotas)
                            $("#hdintervalo").val(res.cabecera.hdintervalo)
                            $("#hdanio").val(res.cabecera.hdanio)
                            $("#hdobserva").val(res.cabecera.hdobserva)
                            $("#hdestado").val(res.cabecera.hdestado)
                            $.each(res.detalle, function(i, item){
                                viewHTML+='<tr><td>' + item.anio + 
                                '</td><td><input type="text" name="peidcount" id="peidcount" value="'+item.periodo+'" hidden>' + item.pename + 
                                '</td><td style="text-align:right"><input type="number" style="text-align:right" name="ddsaldo" value="'+item.saldo+'" hidden>' + item.cuota + 
                             '</td><td><input type="number" id="valor" value="'+item.ddvalor+'" style="text-align:right"></td></tr>' 
                            })  
                            $("#haberdescdata").html(viewHTML)
                            $("#anular-card").removeAttr('disabled')
                            $("#transearch").attr('disabled', true)
                            $("#btn-buscar-trans").attr('disabled', true)
                        }
                    }
                    
                }
            })
      })
      
      $(document).on('click', '#print-card', function(){
            let id = $("#transearch").val()
            window.open('reportes/RRHH/comprobante.php?id='+id)  
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
        
    })
    
    $(document).on('click','#btn-buscar-trans',function(){
          $("#modal-trans-data").modal("show")
    })
    
    /*$(document).on('click','#btn-print-transaction',function(){
          $("#modal-trans-print").modal("show")
    })*/
      
      loadDatahaberdesc()
      
      function loadDatahaberdesc(){
          let opcion = 1
          $("#transaccionData").DataTable({
                    "ajax": {
                        "method": "POST",
                        "url": "./?action=ro_haberdescDatos",
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
        
      //loadHaberDescPrint()
      
      /*function loadHaberDescPrint(){
          let opcion = 1
          $("#transactionPrint").DataTable({
                    "ajax": {
                        "method": "POST",
                        "url": "./?action=ro_haberdescDatos",
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
        }*/
    
    $(document).on('click', '#filterdate', function(e){
            e.preventDefault()
            let desde = $("#min").val()
            let hasta = $("#max").val()
            $("#transaccionData").DataTable().destroy()
            
            $("#transaccionData").DataTable({
                    "ajax": {
                        "method": "POST",
                        "url": "./?action=ro_haberdescFechas",
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
    
    /*$(document).on('click', '#filterprint', function(e){
            e.preventDefault()
            let desde = $("#minPrint").val()
            let hasta = $("#maxPrint").val()
            $("#transactionPrint").DataTable().destroy()
            
            $("#transactionPrint").DataTable({
                    "ajax": {
                        "method": "POST",
                        "url": "./?action=ro_haberdescFechas",
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
        })*/
    
    $(document).on('click', '#empleadosData tbody tr', function(e){
          let celda =$(this).closest('tr').find('td:eq(0)').text()
          $.ajax({
              url:'./?action=ro_processhaberdescform',
              type:'POST',
              data:{option:1, celda:celda},
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
                  
                  $("#emid").val(viewHTML)
                  $("#emidlegal").val(viewHTML1)
                  $("#nameempleado").val(viewHTML2)
                  $("#modal-empleados-data").modal("hide")
                  $("#new-card").removeAttr('disabled')
              }
          })
    })
    
    $(document).on('click', '#transaccionData tbody tr', function(e){
          let id =$(this).closest('tr').find('td:eq(0)').text()
          $.ajax({
              url:'./?action=ro_processhaberdescform',
              type:'POST',
              data:{option:6, id:id},
              success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = ''
                    if(res.cabecera.hdestado==0){
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
                                width:300,
                        })
                    }else{
                        if(res.cabecera.obligacion!=null){
                             Swal.fire({
                                icon:"warning",
                                    position: "top",
                                    showConfirmButton:false,
                                    title: "Ésta transacción ya está relacionada con una obligación de tesorería",
                                    showClass: {
                                    popup: `
                                            animate__animated
                                            animate__fadeInUp
                                            animate__faster
                                    `
                                    },
                                    timer:4000,
                                    width:600,
                            })
                        }else{
                            $("#hdfecha1").hide()
                            $("#hdfecha").show()
                            $("#emidlegal").removeAttr('disabled')
                            $("#tbid").removeAttr('disabled')
                            $("#cdid").removeAttr('disabled')
                            $("#dtid").removeAttr('disabled')
                            $("#hdtotal").removeAttr('disabled')
                            $("#hdcuotas").removeAttr('disabled')
                            $("#hdintervalo").removeAttr('disabled')
                            $("#peid").removeAttr('disabled')
                            $("#hdanio").removeAttr('disabled')
                            $("#hdobserva").removeAttr('disabled')
                            $("#hdfecha").val(res.cabecera.hdfecha)
                            $("#transearch").val(res.cabecera.hdid)
                            $("#transearch1").val(res.cabecera.hdid)
                            $("#emid").val(res.cabecera.emid)
                            $("#emidlegal").val(res.cabecera.id)
                            $("#nameempleado").val(res.cabecera.empleado)
                            $("#peid1").val(res.cabecera.peid)
                            $("#peid").val(res.cabecera.peid).trigger('change')
                            $("#tbid1").val(res.cabecera.tbid)
                            $("#tbid").val(res.cabecera.tbid).trigger('change')
                            $("#cdid1").val(res.cabecera.cdid)
                            $("#cdid").val(res.cabecera.cdid).trigger('change')
                            $("#dtid1").val(res.cabecera.dtid)
                            $("#dtid").val(res.cabecera.dtid).trigger('change')
                            $("#hdtotal").val(res.cabecera.hdtotal)
                            $("#hdcuotas").val(res.cabecera.hdcuotas)
                            $("#hdintervalo").val(res.cabecera.hdintervalo)
                            $("#hdanio").val(res.cabecera.hdanio)
                            $("#hdobserva").val(res.cabecera.hdobserva)
                            $("#hdestado").val(res.cabecera.hdestado)
                            $.each(res.detalle, function(i, item){
                                viewHTML+='<tr><td>' + item.anio + 
                                '</td><td><input type="text" name="peidcount" id="peidcount" value="'+item.periodo+'" hidden>' + item.pename + 
                                '</td><td style="text-align:right"><input type="number" style="text-align:right" name="ddsaldo" value="'+item.saldo+'" hidden>' + item.cuota + 
                             '</td><td><input type="number" id="valor" value="'+item.ddvalor+'" style="text-align:right"></td></tr>' 
                            })  
                            $("#haberdescdata").html(viewHTML)
                            $("#anular-card").removeAttr('disabled')
                            $("#transearch").attr('disabled', true)
                            $("#modal-trans-data").modal("hide")
                            $("#btn-buscar-trans").attr('disabled', true)
                        }
                    }
                    
                }
          })
        })
        
    /*$(document).on('click', '#transactionPrint tbody tr', function(e){
          let id =$(this).closest('tr').find('td:eq(0)').text()
          Swal.fire({
                icon:'warning',
                title:'La transacción #'+id+' generará un reporte PDF',
                text:'Desea continuar?',
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                $("#modal-trans-print").modal("hide")
                if(result.isConfirmed){
                    window.open('reportes/RRHH/comprobante.php?id='+id)
                }
            }) 
        })*/
    
    $(document).on('change', '#emidlegal', function(){
            let celda = $(this).val()
            $.ajax({
              url:'./?action=ro_processhaberdescform',
              type:'POST',
              data:{option:1, celda:celda},
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
                  
                  $("#emid").val(viewHTML)
                  $("#emidlegal").val(viewHTML1)
                  $("#nameempleado").val(viewHTML2)
                  $("#tiporol").val(viewHTML3)
                  $("#tiporol").trigger('input')
                  $("#new-card").removeAttr('disabled')
              }
          })
        })
        
    
    
    
    function loadperiodo(){
        let valpeid = ''
        if(document.getElementById('peid1')){
            valpeid = $("#peid1").val()
        }
        $.ajax({
            url:'./?action=ro_processhaberdescform',
            type:'POST',
            data:{option:2},
            success:function(respond){
                let res = JSON.parse(respond)
                viewHTML='<option value="">Periodo</option>'
                selected=''
                $.each(res, function(i,item){
                    if(valpeid=''){
                            if(valpeid==item.id){
                                selected='selected'
                            }else{
                                selected=''
                            }
                            console.log(selected)
                    }
                    viewHTML+='<option value="' +item.id+'"'+selected+'>'  +item.name+ '</option>'
                })
                $("#peid").html(viewHTML)
            }
        })
    }
    
    loadperiodo()
    
    function loadtipofilter(){
        $.ajax({
            url:'./?action=ro_processhaberdescform',
            type:'POST',
            data:{option:3},
            success:function(respond){
                let res = JSON.parse(respond)
                viewHTML='<option value="">Tipo</option>'
                $.each(res, function(i,item){
                    viewHTML+='<option value=' +item.id+ '>' +item.name+ '</option>'
                })
                $("#tbid").html(viewHTML)
            }
        })
    }
    
    loadtipofilter()
    
    $(document).on('change', '#tbid', function(){
        let tipo = $(this).val()
        $.ajax({
            url:'./?action=ro_processhaberdescform',
            type:'POST',
            data:{option:4, tipo:tipo},
            success:function(respond){
                let res = JSON.parse(respond)
                viewHTML= ''
                $.each(res, function(i,item){
                   viewHTML+='<option value=' +item.id+ '>' +item.name+ '</option>'
                })
                $("#cdid").html(viewHTML)
            }
        })
    })
    
    $(document).on('change', '#tbid', function(){
        let tipo = $(this).val()
        $.ajax({
            url:'./?action=ro_processhaberdescform',
            type:'POST',
            data:{option:5, tipo:tipo},
            success:function(respond){
                let res = JSON.parse(respond)
                viewHTML= ''
                $.each(res, function(i,item){
                    viewHTML+='<option value=' +item.id+ '>' +item.name+ '</option>'
                })
                $("#dtid").html(viewHTML)
            }
        })
    })
    
    $(document).on('click', '#generate', function(){
        let total = $("#hdtotal").val()
        let cuota = $("#hdcuotas").val()
        let interval = $("#hdintervalo").val()
        let periodo = $("#peid").val()
        let anio = $("#hdanio").val()
        let viewHTML = ''
        let valor = Number(total)/Number(cuota)
        let abono = 0
        let global = 0
        let resta = 0
        let suma = 0
        let saldo = valor - abono
        let tbodydata = $("#haberdescdata")
        let arrayMeses = new Array()
        arrayMeses[1] = "Enero";
        arrayMeses[2] = "Febrero";
        arrayMeses[3] = "Marzo";
        arrayMeses[4] = "Abril";
        arrayMeses[5] = "Mayo";
        arrayMeses[6] = "Junio";
        arrayMeses[7] = "Julio";
        arrayMeses[8] = "Agosto";
        arrayMeses[9] = "Septiembre";
        arrayMeses[10] = "Octubre";
        arrayMeses[11] = "Noviembre";
        arrayMeses[12] = "Diciembre";
        if(periodo==''){
             Swal.fire({
                icon:"warning",
                position: "top",
                showConfirmButton:false,
                title: "Debe seleccionar el periodo para establecer las cuotas",
                showClass: {
                popup: `
                        animate__animated
                        animate__fadeInUp
                        animate__faster
                `
                },
                timer:4000,
                width:400,
            })
        }else{
            if(tbodydata.children().length==0){
            let o =  periodo
            for(i=1; i<=cuota; i++){
                if(o > 12){
                    o = Number(o) - 12
                    anio = Number(anio) + 1
                }else{
                    o = o
                }
                global += Number(valor.toFixed(2))
                if(i==cuota){
                    if(global>total){
                        rest = global-total
                        valor = valor-rest
                    }else{
                        sum = total-global
                        valor = valor + sum
                    }
                }
                viewHTML+='<tr><td>'+anio+'</td><td><input type="text" name="peidcount" id="peidcount" value="'+o+'" hidden>'+arrayMeses[o]+
                '</td><td style="text-align:right"><input type="number" style="text-align:right" name="ddsaldo" value="'+saldo.toFixed(2)+'" hidden> '+i+
                '</td><td><input type="number" id="valor" value="'+valor.toFixed(2)+'" style="text-align:right"></td></tr>' 
                //o++
                o = Number(o) + Number(interval)
            }
    
            $("#haberdescdata").append(viewHTML)
            
            }else{
                
                $("#haberdescdata").empty()
                let o =  periodo
                for(i=1; i<=cuota; i++){
                    if(o > 12){
                        o = Number(o) - 12
                        anio = Number(anio) + 1
                    }else{
                        o = o
                }
                global += Number(valor.toFixed(2))
                if(i==cuota){
                    if(global>total){
                        rest = global-total
                        valor = valor-rest
                    }else{
                        sum = total-global
                        valor = valor + sum
                    }
                }
                viewHTML+='<tr><td>'+anio+'</td><td><input type="text" name="peidcount" id="peidcount" value="'+o+'" hidden>'+arrayMeses[o]+
                '</td><td style="text-align:right"><input type="number" style="text-align:right" name="ddsaldo" value="'+saldo.toFixed(2)+'" hidden> '+i+
                '</td><td><input type="number" id="valor" value="'+valor.toFixed(2)+'" style="text-align:right"></td></tr>' 
                //o++
                o = Number(o) + Number(interval)
                    
                    
                }
                $("#haberdescdata").append(viewHTML)
            }
        }
    })
        
        /*$(document).on('click', '#anular-card', function(){
            let id = $("#transearch1").val()
            let anular = 0
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
                        url:'./?action=ro_processhaberdesccancel',
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
                                        location.href='./?view=ro_haberdesc'
                                    }
                                })
                            }
                        }
                    })
                }
            })
      })*/
      
}