if(document.getElementById("ro_empleados")){
    
    $(document).ready(function(){
        
        $("#identificador").change(function () { 
            var ruc = $(this).val(); 
            $.ajax({
                url: 'index.php?action=validaRuc',
                type: 'GET',
                data: {ruc: ruc},
                success: function (e) {
                    if (e == 0) {
                        alert('Identificacion ingresada incorrecta..!!!')
                        $("#identificador").val('');
                        $('#identificador').focus();
                    }
                }
            })
        });
    
        $("#nacimiento").blur(function(){
            var edad = $(this).val();
            $.ajax({
                url:'./?action=ro_processempleadosedad',
                type:'GET',
                data: {edad:edad},
                success:function(e){
                    if(e==0){
                        alert('La fecha de nacimiento no cumple con la mayoría de edad establecida...!!!')
                        $("#nacimiento").val('');
                        $("#nacimiento").focus();
                    }
                }
            })
        })
        
     //Extracción de datos de las tablas para el registro y modificación de empleados  
        function loadgrupoetnico(){
            let ct = ''
             if(document.getElementById('etnico')){
                 ct = $("#etnico").val()
             }
            $.ajax({
            url:'./?action=ro_processempleadosData',
            type:'POST',
            data:{option:1},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Seleccione...</option>'
                let selected=''
                
                $.each(res, function(i, item){
                    if (ct != ''){
                            if (ct == item.id){
                                selected = "selected"
                            }else{
                                selected=''
                            }
                        }
                    viewHTML += '<option value="' +item.id + '"' +selected+'>' + item.name + '</option>'
                })
                $("#etnia").html(viewHTML)
                
            }
        })
        } 
        loadgrupoetnico()
        
        function loadprofesion(){
            let ct = ''
             if(document.getElementById('pro')){
                 ct = $("#pro").val()
             }
            $.ajax({
            url:'./?action=ro_processempleadosData',
            type:'POST',
            data:{option:5},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Seleccione...</option>'
                let selected=''
                
                $.each(res, function(i, item){
                    if(ct !== ''){
                        if(ct == item.id){
                            selected = "selected"
                        }else{
                            selected =''
                        }
                    }
                    viewHTML += '<option value="' +item.id + '"' +selected+ '>' + item.name + '</option>'
                })
                $("#profesion").html(viewHTML)
                
                
            }
        })
        }
        loadprofesion()
        
        function loadnacion(){
            let ct = ''
            if(document.getElementById('nacion')){
                 ct = $("#nacion").val()
            }
            $.ajax({
            url:'./?action=ro_processempleadosData',
            type:'POST',
            data:{option:6},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Seleccione...</option>'
                let selected=''
                $.each(res, function(i, item){
                    if (ct !== ''){
                            if (ct == item.id){
                                selected = "selected"
                            }else{
                                selected =''
                            }
                        }
                    viewHTML += '<option value="' +item.id+ '"' +selected+ '>' + item.name + '</option>'
                })
                $("#nacionalidad").html(viewHTML)
            }
        })
        }
        loadnacion()
        
        function loadtipo(){
            let ct = ''
             if(document.getElementById('empleado')){
                 ct = $("#empleado").val()
             }
            $.ajax({
            url:'./?action=ro_processempleadosData',
            type:'POST',
            data:{option:7},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Seleccione...</option>'
                let selected=''
                $.each(res, function(i, item){
                    if(ct !== ''){
                        if(ct == item.id){
                            selected = "selected"
                        }else{
                            selected = ''
                        }
                    }
                    viewHTML += '<option value="' +item.id + '"'+selected+'>' + item.name + '</option>'
                })
                $("#tiemp").html(viewHTML)
                
                
            }
        })
        }
        loadtipo()
        
        function loadcargo(){
            let ct = ''
             if(document.getElementById('cargos')){
                 ct = $("#cargos").val()
             }
            $.ajax({
            url:'./?action=ro_processempleadosData',
            type:'POST',
            data:{option:8},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Seleccione...</option>'
                let selected=''
                $.each(res, function(i, item){
                    if(ct !== ' '){
                        if(ct==item.id){
                            selected = "selected"
                        }else{
                            selected =''
                        }
                    }
                    viewHTML += '<option value="' +item.id + '"'+selected+'>' + item.name + '</option>'
                }) 
                $("#cargo").html(viewHTML)
            }
        })
        }
        loadcargo()
        
        function loadrol(){
            let ct = ''
             if(document.getElementById('rol')){
                 ct = $("#rol").val()
             }
            $.ajax({
            url:'./?action=ro_processempleadosData',
            type:'POST',
            data:{option:9},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Seleccione...</option>'
                let selected=''
                $.each(res, function(i, item){
                    if(ct !== ''){
                        selected = "selected"
                    }else{
                        selected = ''
                    }
                    viewHTML += '<option value="' +item.id + '"'+selected+'>' + item.name + '</option>'
                })
                $("#tiro").html(viewHTML) 
            }
        })
        }
        loadrol() 
        
        $(document).on('change', '#tiro', function(){
            let rol = $(this).val();
            $.ajax({
                url:'./?action=ro_processempleadosData',
                type:'POST',
                data:{option:27, rol:rol},
                success:function(respond){
                    let res = JSON.parse(respond)
                    $("#peid").val(res[0])
                    $("#tranioact").val(res[1])
                    $("#caid").val(res[2])
                }
            })
        })
        
        function loadformacion(){
            let ct = ''
            if(document.getElementById('academia')){
                ct = $("#academia").val()
            }
            $.ajax({
            url:'./?action=ro_processempleadosData',
            type:'POST',
            data:{option:10},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Seleccione...</option>'
                let selected=''
                $.each(res, function(i, item){
                    if(ct!==''){
                        if(ct==item.id){
                            selected = "selected"
                        }else{
                            selected =''
                        }
                    }
                    viewHTML += '<option value="' +item.id + '"'+selected+'>' + item.name + '</option>'
                })
                $("#forad").html(viewHTML)
                
                
            }
        })
        }
        loadformacion()
        
        function loadentidades(){
            let ct = ''
             if(document.getElementById('etb')){
                 ct = $("#etb").val()
             }
            $.ajax({
            url:'./?action=ro_processempleadosData',
            type:'POST',
            data:{option:11},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Seleccione...</option>'
                let selected=''
                $.each(res, function(i, item){
                    if(ct!==''){
                        if(ct==item.id){
                            selected="selected"
                        }else{
                            selected =''
                        }
                    }
                    viewHTML += '<option value="' +item.id + '"'+selected+'>' + item.name + '</option>'
                })
                $("#entidades").html(viewHTML)
                
                
            }
        })
        }
        loadentidades()
        
        function loadetiquetas(){
            let etqConfig = $("#configInv").val()
            $.ajax({
                url:'./?action=ro_processempleadosData',
                type:'POST',
                data:{option:12,etqConfig:etqConfig},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML= '<option value="">Seleccione...</option>'
                    let selected=''
                    //$.each(res, function(i, item){
                        viewHTML += '<option value="' +res.id + '">' + res.name + '</option>'
                    //})
                    $("#etiqueta").html(viewHTML)
                }
            })
        }
        loadetiquetas()
        
        $(document).on('change','#etiqueta',function(){
            let etiqueta = $(this).val()
            
            $.ajax({
                url:'./?action=ro_processempleadosData',
                type:'POST',
                data:{option:13,etiqueta:etiqueta},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML= '<option value="">Seleccione...</option>'
                    let selected=''
                    $.each(res, function(i, item){
                        
                        viewHTML += '<option value="' +item.id + '"'+selected+'>' + item.name + '</option>'
                    })
                    $("#subetiqueta").html(viewHTML)
                }
            })
        })
        
        
        $(document).on('change','#etiqueta',function(){
            let etiqueta = $(this).val()
            $.ajax({
                url:'./?action=ro_processempleadosData',
                type:'POST',
                data:{option:14,etiqueta:etiqueta},
                success:function(respond){
                    let res = JSON.parse(respond)
                    if(res.unidades){
                        
                        let htmlUnidades = '<option value="">Seleccione...</option>'
                        let selected=''
                        $.each(res.unidades, function(i, item){
                            
                            htmlUnidades += '<option value="' + item.id + '"'+selected+'>' + item.name + '</option>'
                        })
                        $("#unidad").html(htmlUnidades)
                    }
                    if(res.funciones){
                        
                        let htmlFunciones= '<option value="">Seleccione...</option>'
                        let selected=''
                        $.each(res.funciones, function(i, item){
                            
                            htmlFunciones += '<option value="' +item.id + '"'+selected+'>' + item.name + '</option>'
                        })
                        $("#funcion").html(htmlFunciones)
                    }
                }
            })
        })
        
        
        
        
        $(document).on('change', '#unidad', function(){
            let centro = $(this).val()
            $.ajax({
                url:'./?action=ro_processempleadosData',
                type:'POST',
                data:{option:15, centro:centro},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML='<option value="">Seleccione...</option>'
                    let selected=''
                    $.each(res, function(i,item){
                        viewHTML +='<option value="' +item.id + '"'+selected+'>' + item.name +'</option>'
                    })
                    $("#centrocosto").html(viewHTML)
                }
            })
        })
        
        
        $("#matrimonio").change(function(){
            let id= $(this).val()
            if(id==2){
                $("#emmatrimonio").removeAttr('disabled')
                $("#emconyuge").removeAttr('disabled')
                $("#emcon_trabaja").removeAttr('disabled')
            }else{
                $("#emmatrimonio").attr('disabled', true)
                $("#emconyuge").attr('disabled', true)
                $("#emcon_trabaja").attr('disabled', true)   
            }
        })
        
        let td = $("#pareja").val()
        
        listenEstadoCivil(td)
        
        function listenEstadoCivil(td){
            // let td = $(this).va()
            if(td==2){
                $("#emmatrimonio").removeAttr('disabled')
                $("#emconyuge").removeAttr('disabled')
                $("#emcon_trabaja").removeAttr('disabled')
            }else{
                $("#emmatrimonio").attr('disabled', true)
                $("#emconyuge").attr('disabled', true)
                $("#emcon_trabaja").attr('disabled', true)
            }
        }
        
        $("#discapacidad").change(function(){
            let discapacidad = $(this).val()
            if(discapacidad==1){
                $("#empdiscap").removeAttr('disabled')
            }else{
                $("#empdiscap").attr('disabled', true)
            }
        })
        
        let dp = $("#diselect").val()
        iddiscapacidad(dp)
        function iddiscapacidad(dp){
            if(dp==1){
                $("#empdiscap").removeAttr('disabled')
            }else{
                $("#empdiscap").attr('disabled', true)
            }
        }
        
        $("#paisnac").change(function () {
            let paisnac = $('#paisnac option:selected').html()
            if (paisnac != "ECUADOR") {
                $("#cdnac").attr('disabled', true)
                $("#provnac").attr('disabled', true)
            } else {
                $("#cdnac").removeAttr('disabled')
                $("#provnac").removeAttr('disabled')
            }
        })
        
        function loadpaisesnac(){
            let pais =''
            if(document.getElementById('paiso')){
                pais=$("#paiso").val()
            }
            $.ajax({
                url:'./?action=ro_processempleadosData',
                type:'POST',
                data:{option:2},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = '<option value="">Seleccione...</option>'
                    let selected = ''
                    $.each(res, function(i, item){
                        if(pais!==''){
                            if(pais==item.id){
                                selected="selected"
                            }else{
                                selected=''
                            }
                        }
                        viewHTML+='<option value="' + item.id + '"' +selected+'>' +item.name+ '</option>'
                    })
                    $("#paisnac").html(viewHTML)
                }
            })
        }
        
        loadpaisesnac()
        
        $(document).on('change', '#paisnac', function(){
            let provdata  = $(this).val()
            $.ajax({
                url:'./?action=ro_processempleadosData',
                type:'POST',
                data:{option:3, provdata:provdata},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = '<option value="">Seleccione...</option>'
                    $.each(res, function(i,item){
                        viewHTML +='<option value="' +item.id+'">'+item.name+'</option>'
                    })
                    $("#provnac").html(viewHTML)
                }
            })
        })
        
        $(document).on('change', '#provnac', function(){
            let cddata = $(this).val()
            $.ajax({
                url:'./?action=ro_processempleadosData',
                type:'POST',
                data:{option:4,cddata:cddata},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = '<option value="">Seleccione...</option>'
                    $.each(res, function(i,item){
                        viewHTML +='<option value="' +item.id+'">'+item.name+'</option>'
                    })
                    $("#cdnac").html(viewHTML)
                }
            })
        })
        
        $("#paisdom").change(function () {
            let pais = $('#paisdom option:selected').html()
            if (pais !== "ECUADOR") {
                $("#cddom").attr('disabled', true)
                $("#provdom").attr('disabled', true)
            } else {
                $("#cddom").removeAttr('disabled')
                $("#provdom").removeAttr('disabled')
            }
        })
        
        function loadpaisesdom(){
            let pais =''
            if(document.getElementById('dompais')){
                pais=$("#dompais").val()
            }
            $.ajax({
                url:'./?action=ro_processempleadosData',
                type:'POST',
                data:{option:20},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = '<option value="">Seleccione...</option>'
                    let selected = ''
                    $.each(res, function(i, item){
                        if(pais!==''){
                            if(pais==item.id){
                                selected="selected"
                            }else{
                                selected=''
                            }
                        }
                        viewHTML+='<option value="' + item.id + '"' +selected+'>' +item.name+ '</option>'
                    })
                    $("#paisdom").html(viewHTML)
                }
            })
        }
        
        loadpaisesdom()
        
        
        $(document).on('change', '#paisdom', function(){
            let provdatadom  = $(this).val()
            $.ajax({
                url:'./?action=ro_processempleadosData',
                type:'POST',
                data:{option:21, provdatadom:provdatadom},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = '<option value="">Seleccione...</option>'
                    $.each(res, function(i,item){
                        viewHTML +='<option value="' +item.id+'">'+item.name+'</option>'
                    })
                    $("#provdom").html(viewHTML)
                }
            })
        })
        
        $(document).on('change', '#provdom', function(){
            let cddatadom = $(this).val()
            $.ajax({
                url:'./?action=ro_processempleadosData',
                type:'POST',
                data:{option:22,cddatadom:cddatadom},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = '<option value="">Seleccione...</option>'
                    $.each(res, function(i,item){
                        viewHTML +='<option value="' +item.id+'">'+item.name+'</option>'
                    })
                    $("#cddom").html(viewHTML)
                }
            })
        })
        
        function inputFile(input){
            if(input.files && input.files[0]){
                var reader = new FileReader()
                
                reader.onload = function(e){
                    //$('#form-ro_empleados + image').remove()
                    $('#miniwindow').html('<img src="'+e.target.result+'" width="145" height="100" align="center"/> ')
                }
                reader.readAsDataURL(input.files[0])
            }
        }
        $("#foto").change(function(){
            inputFile(this)
        })
        
        
        
    //Eliminación de datos
    
        $(document).on('click', '.remove-ro_empleados', function(){
            let id= $(this).closest('tr').find('td:eq(0)').text()
            Swal.fire({
                title: 'Eliminar registro?',
                text:'Esta accion no se puede deshacer',
                icon:'warning',
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                if(result.isConfirmed){
                    $.ajax({
                        url:'./?action=ro_processempleados',
                        type:'POST', 
                        data:{id:id,tipo:3},
                        success:function(respond){
                            let res = JSON.parse(respond)
                            if(res.substr(0,1) == 0){
                                Swal.fire({
                                    icon:'error',
                                    title:res.substr(2),
                                })
                            }else{
                                Swal.fire({
                                    icon:'success',
                                    title:res.substr(2),
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                     location.href='./?view=ro_viewempleados'   
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        //Inserción y modificación de datos para empleados
        
        $("#save-modified-ro_empleados").click(function(e){
            let nombre = $("#emnombre").val()
            if(nombre.length==0){
                Swal.fire({
                    icon:'error',
                    title:'Ingrese su nombre',
                })
            }else{
               let apellido = $("#emapellido").val()
               if(apellido.length==0){
                   Swal.fire({
                       icon:'error',
                       title:'Ingrese su apellido',
                   })
               }else{
                   let legal = $("#identificador").val()
                   if(legal.length==0){
                       Swal.fire({
                           icon:'error',
                           title:'Ingrese su cédula',
                       })
                   }else{
                       let fecnac = $("#nacimiento").val()
                       if(fecnac.length==0){
                           Swal.fire({
                               icon:'error',
                            title:'Ingrese su fecha de nacimiento',
                           })
                       }else{
                           let calle = $("#emdom_calle").val()
                           if(calle.length==0){
                               Swal.fire({
                                   icon:'error',
                               title:'Ingrese su dirección',
                               })
                           }else{
                                let vivienda = $("#emdom_num").val()
                                if(vivienda.length==0){
                                    Swal.fire({
                                        icon:'error',
                                        title:'Ingrese el número de vivienda',
                                    })
                                }else{
                                    let telefono = $("#emdom_fono1").val()
                                    if(telefono.length==0){
                                        Swal.fire({
                                            icon:'error',
                                            title:'Ingrese su número de telefono',
                                        })
                                    }else{
                                        let celular = $("#emcelular").val()
                                        if(celular.length==0){
                                            Swal.fire({
                                                icon:'error',
                                                title:'Ingrese su número celular',
                                            })
                                        }else{
                                            let correo = $("#emmail").val()
                                            if(correo.length==0){
                                                Swal.fire({
                                                    icon:'error',
                                                    title:'Ingrese su correo electrónico'
                                                })
                                            }else{
                                                let datos = new FormData(document.getElementById('form-ro_empleados'))
                                                $.ajax({
                                                     url:'./?action=ro_processempleados',
                                                     type:'POST', 
                                                     data: datos,
                                                     contentType:false,
                                                     cache:false,
                                                     processData: false,
                                                     success:function(respond){
                                                         let res = JSON.parse(respond)
                                                         if(res.substr(0,1)==0){
                                                             Swal.fire({
                                                                 icon:'error',
                                                                 title:res.substr(2),
                                                             })
                                                         }else{
                                                             Swal.fire({
                                                                 icon:'success',
                                                                 title:res.substr(2),
                                                             }).then((result)=>{
                                                                 if(result.isConfirmed){
                                                                     location.href='./?view=ro_viewempleados'
                                                                }
                                                            })
                                                        }
                                                    }
                                                })
                                            }
                                        }
                                    }
                                }               
                            }
                        }
                    }
                }
            }
        })
    })
}