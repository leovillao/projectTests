if(document.getElementById('ro_definircalculos')){
    
    $(document).ready(function(){
        
        
        function loadcampos(){
            
            $.ajax({
                url:'./?action=ro_processdefcalculosData',
                type:'POST',
                data:{option:1},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = ''
                    $.each(res, function(i,item){
                        viewHTML+='<tr><td><input type="text" value="'+item.id1+'" hidden>'+item.id+' </td><td> '+item.name+' </td></tr>'
                    })
                    $("#camposdefinibles").html(viewHTML)
                    $("#camposdefinibles1").html(viewHTML)
                }
            })
            
        }
        
        loadcampos()
        
        function loadcampos1(){
            
            $.ajax({
                url:'./?action=ro_processdefcalculosData',
                type:'POST',
                data:{option:2},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = ''
                    $.each(res, function(i,item){
                        viewHTML+='<tr><td><input type="text" value="'+item.id1+'" hidden>'+item.id+' </td><td> '+item.name+' </td></tr>'
                    })
                    $("#camposdefinibles2").html(viewHTML)
                }
            })
            
        }
        
        loadcampos1()
        
        
        $(document).on('click', '#definible11 tbody tr', function(e){
          let celda =$(this).closest('tr').find('td:eq(0)').text()
          let celda1 =$(this).closest('tr').find('td:eq(1)').text()
          
          $("#formula").val($("#formula").val()+' '+celda)
          $("#formula1").val($("#formula1").val()+celda1)
          $("#formula").focus()
          
        })
        
        $(document).on('mouseup keyup','#formula', function(e){
            let lastselect= e.target.selectionStart
            let demo = $("#formula").val()
            let demo1 = $("#formula1").val()
            let space = demo.split(" ").length-1
            let space1 = demo1.split(" ").length-1
            let spaceselect = space
            if(lastselect==0){
                if(e.keyCode==40){
                    $("#formula1").val(e.key+$("#formula1").val())
                }
            }else{
                if(e.keyCode==8){//=================================================================================================
                    $("#formula1").val($("#formula1").val())
                }else if(e.keyCode==127 || e.keyCode==16 || e.keyCode==37 || e.keyCode==38 || e.keyCode==39 || e.keyCode==40){//=================================================================================================
                    $("#formula1").val()
                }else{//=================================================================================================
                    $("#formula1").val($("#formula1").val()+demo[demo.length-1])
                }
            }
                
        })
        
        $(document).on('click', '#clearing', function(e){
            e.preventDefault()
           $("#formula").val(null)
           $("#formula1").val(null)
        })
        
        $(document).on('click', '#definible1 tbody tr', function(e){
          let celda =$(this).closest('tr').find('td:eq(0)').text()
          let celda1 =$(this).closest('tr').find('td:eq(1)').text()
          $("#condicion1").val($("#condicion1").val()+celda)
          $("#condicion12").val($("#condicion12").val()+celda1)
          $("#condicion1").focus()
        })
        
        $(document).on('mouseup keyup','#condicion1', function(e){
            let lastselect= e.target.selectionStart
            let demo = $("#condicion1").val()
            if(lastselect==0){//=================================================================================================
            console.log(e.keyCode)
                if(e.keyCode==57){
                    $("#condicion12").val(e.key+$("#condicion12").val())
                }else if(e.keyCode==127 || e.keyCode==16){
                    $("#condicion12").val($("#condicion12").val())
                }
            }else{//=================================================================================================
                if(e.keyCode==8){//=================================================================================================
                    $("#condicion12").val($("#condicion12").val()+demo.charAt())
                }else if(e.keyCode==127 || e.keyCode==16){//=================================================================================================
                    
                }else{//=================================================================================================
                    $("#condicion12").val($("#condicion12").val()+demo[demo.length-1])
                }
            }
        })
        
        
        $(document).on('click', '#clearng', function(e){
            e.preventDefault()
           $("#condicion1").val(null)
           $("#condicion12").val(null)
        })
        
        
        $(document).on('click', '#definible2 tbody tr', function(e){
          let celda =$(this).closest('tr').find('td:eq(0)').text()
          $.ajax({
              url:'./?action=ro_processdefcalculosData',
              type:'POST',
              data:{option:3, celda:celda},
              success:function(respond){
                  let res = JSON.parse(respond)
                  let viewHTML =''
                  $("#cdid").val(res[0])
                  $("#nseleccionado").val(res[1])
                  $("#seleccionado").val(res[2])
                  $("#seleccionado").focus()
              }
          })
        })
        
        $(document).on('click', '#showing-data', function(){
            $("#printxlsx-data").removeAttr('disabled')
            $("#printpdf-data").removeAttr('disabled')
            $("#anular-data").removeAttr('disabled')
            $("#delete-data").removeAttr('disabled')
            $("#cacodigo").hide()
            $("#id").show()
            $("#id").addClass('form-control')
            $("#catipo").attr('disabled', true)
            $("#cadescrip").attr('disabled', true)
            $("#cacomenta").attr('disabled', true)
            $("#formula").attr('disabled', true)
            $("#formula1").attr('disabled', true)
            $("#from").attr('disabled', true)
            $("#join").attr('disabled', true)
            $("#condicion1").attr('disabled', true)
            $("#condicion12").attr('disabled', true)
            $("#nseleccionado").attr('disabled', true)
            $("#seleccionado").attr('disabled', true)
            
            
        })
        
         $(document).on('click','#showing-data',function(){
          $("#modal-calculos").modal("show")
        })
          
          loadCalculos()
          
          function loadCalculos(){
              let opcion = 1
              $("#calculosData").DataTable({
                        "ajax": {
                            "method": "POST",
                            "url": "./?action=ro_calculosDatos",
                            "data": {"option": opcion}
                        },
    
                        "columns": [
                            {"data" : "id"},
                            {"data" : "name"},
                        ],
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                        },
              })
            }
        
        $(document).on('click', '#calculosData tbody tr', function(e){
            let id =$(this).closest('tr').find('td:eq(0)').text()
            $("#catipo").removeAttr('disabled')
            $("#cadescrip").removeAttr('disabled')
            $("#cacomenta").removeAttr('disabled')
            $("#formula").removeAttr('disabled')
            $("#formula1").removeAttr('disabled')
            $("#from").removeAttr('disabled')
            $("#join").removeAttr('disabled')
            $("#condicion1").removeAttr('disabled')
            $("#condicion12").removeAttr('disabled')
            $("#nseleccionado").removeAttr('disabled')
            $("#seleccionado").removeAttr('disabled')
            $("#modal-calculos").modal("hide")
            $.ajax({
                url:'./?action=ro_processdefcalculosData',
                type:'POST',
                data:{option:4, id:id},
                success:function(respond){
                    let res = JSON.parse(respond)
                    $("#id").val(res[0])
                    $("cacodigo").val(res[0])
                    $("#catipo1").val(res[1])
                    $("#catipo").val(res[1]).trigger('change')
                    $("#cadescrip").val(res[2])
                    $("#cacomenta").val(res[3])
                    $("#formula").val(res[6])
                    $("#formula1").val(res[7])
                    $("#condicion1").val(res[8])
                    $("#condicion12").val(res[9])
                    $("#from").val(res[4])
                    $("#join").val(res[5])
                    $("#cdid").val(res[10])
                    $("#nseleccionado").val(res[11])
                    $("#seleccionado").val(res[12])
                }
            })
        })
        
        $(document).on('click', '#anular-data', function(){
            let cacodigo=$("#id").val()
            let anular=0
            Swal.fire({
                icon:'warning',
                title: 'Quiere anular el cálculo?',
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                if(result.isConfirmed){
                    $.ajax({
                        url:'./?action=ro_processcanceldefinircalculos',
                        type:'POST',
                        data:{cacodigo:cacodigo,anular:anular},
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
                                        location.href='./?view=ro_definircalculos'
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        $(document).on('click', '#delete-data', function(){
            let cacodigo=$("#id").val()
            Swal.fire({
                icon:'warning',
                title: 'Quiere eliminar el cálculo?',
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                if(result.isConfirmed){
                    $.ajax({
                        url:'./?action=ro_processcanceldefinircalculos',
                        type:'POST',
                        data:{anular:3, cacodigo:cacodigo},
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
                                        location.href='./?view=ro_definircalculos'
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        $(document).on('click', '#save-data', function(){
            $.ajax({
                url:'./?action=ro_processindefinircalculos',
                type:'POST',
                data:$("#definircalculos").serialize(),
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
                                location.href='./?view=ro_definircalculos'   
                            }
                        })
                    }
                }
            })
        })
        
        $(document).on('click', '#printpdf-data', function(){
            window.open('reportes/RRHH/calculospdf.php')
        })
        
        $(document).on('click', '#printxlsx-data', function(){
            window.open('reportes/RRHH/calculosexcel.php')
        })
        
        
        
    })
    
}