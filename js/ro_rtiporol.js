if(document.getElementById("ro_rtiporol")){
    
    $(document).ready(function(){
        
        $(document).on('click', '.remove-ro_rtiporol', function(){
            let id = $(this).closest('tr').find('td:eq(0)').text()
            Swal.fire({
                icon:'warning',
                title:"Eliminar registro?",
                text:"Esta accion no se puede deshacer",
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                $.ajax({
                    url:'./?action=ro_processrtiporol',
                    type:'POST',
                    data:{id:id,tipo:3},
                    success:function(respond){
                        console.log(respond)
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
                                    location.href='./?view=ro_viewrtiporol'
                                }
                            })
                        }
                    }
                })
            })
        })
        
        
        
        function loadperiodoinicial(){
            let inicio =''
            if(document.getElementById('inicio')){
                inicio = $("#inicio").val()
            }
            $.ajax({
            url:'./?action=ro_processrtiporolData',
            type:'POST',
            data:{option:1},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Seleccione...</option>'
                let selected=''
                $.each(res, function(i, item){
                    if(inicio!==''){
                        if(inicio==item.id){
                            selected="selected"
                        }else{
                            selected=''
                        }
                    }
                    viewHTML += '<option value="' +item.id + '"'+selected+ '>' + item.name + '</option>'
                })
                $("#inicial").html(viewHTML)
            }
        })
        }
        loadperiodoinicial()
        
        
        function loadperiodofinal(){
            let fin=''
            if(document.getElementById('fin')){
                fin = $("#fin").val()
            }
            $.ajax({
            url:'./?action=ro_processrtiporolData',
            type:'POST',
            data:{option:1},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Seleccione...</option>'
                let selected=''
                $.each(res, function(i, item){
                    if(fin!==''){
                        if(fin==item.id){
                            selected="selected"
                        }else{
                            selected=''
                        }
                    }
                    viewHTML += '<option value="' +item.id + '"' +selected+'>' + item.name + '</option>'
                })
                $("#final").html(viewHTML)
            }
        })
        }
        loadperiodofinal()
        
        
        function loadcalculos(){
            let calculo = ''
            if(document.getElementById('rcalculo')){
                calculo = $("#rcalculo").val()
            }
            $.ajax({
                url:'./?action=ro_processrtiporolData',
                type:'POST',
                data:{option:2},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML= '<option value="">Seleccione... </option>'
                    let selected=''
                    $.each(res, function(i, item){
                        if(calculo!==''){
                            if(calculo==item.id){
                                selected = "selected"
                            }else{
                                selected = ''
                            }
                        }
                        viewHTML += '<option value="' +item.id + '"'+selected+ '>' + item.name + '</option>'
                    })
                    $("#calculo").html(viewHTML)
                   
            }
            })
        }
        loadcalculos()
        
        $("#save-modified-ro_rtiporol").click(function(e){
            e.preventDefault()
            let tipo = $("#trdescrip")
            if(tipo.length==0){
                Swal.fire({
                    icon:'error',
                    title:"Debe ingresar el dato correspondiente",
                })
            }else{
                $.ajax({
                    url:'./?action=ro_processrtiporol',
                    type:'POST',
                    data:$("#form-ro_rtiporol").serialize(),
                    success:function(respond){
                        //console.log(respond)
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
                                location.href='./?view=ro_viewrtiporol'
                            })
                        }
                    }
                })
            }
        })
        
    })
    
}