if(document.getElementById('ro_rtipodependencia')){
    
    $(document).ready(function(){
        
        function loadtipo(){
            let ct = ''
            if(document.getElementById('dependencia')){
                ct = $("#dependencia").val()
            }
            $.ajax({
                url:'./?action=ro_processempleadostpr',
                type:'POST',
                data:{option:1},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = '<option value="">Seleccione...</option>'
                    let selected =''
                    $.each(res, function(i, item){
                        if(ct!==''){
                            if(ct==item.id){
                                selected="selected"
                            }else{
                                selected=''
                            }
                        }
                        viewHTML+='<option value="' +item.id+ '"' +selected+ '>'  +item.name+ '</option>'
                    })
                    $("#tdid").html(viewHTML)
                }
            })
        }
        
        loadtipo()
        
        $(document).on('click', '.remove-ro_rtipodependencia', function(){
            let dlt = $("#idEmpleado").val()
            let id = $(this).closest('tr').find('td:eq(0)').text()
            Swal.fire({
                icon:'warning',
                title: 'Eliminar registro?',
                text:'Esta accion no se puede deshacer',
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                if(result.isConfirmed){
                    $.ajax({
                        url:'./?action=ro_processempleadostp',
                        type:'POST',
                        data:{id:id,tipo:3},
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
                                    if(result.isConfirmed){
                                        location.href="./?view=ro_viewrtipodependencia&id="+dlt
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        $("#save-modified-ro_rtipodependencia").on('click', function(e){
            let id = $("#idEmpleado").val()
            e.preventDefault()
            let dependencia = $("#tiname").val()
            if(dependencia.length==0){
                Swal.fire({
                    icon:'error',
                    title:'No se ha ingresado todos los registros',
                })
            }else{
                $.ajax({
                url:'./?action=ro_processempleadostp',
                type:'POST',
                data:$("#form-ro_rdependencia").serialize(),
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
                                location.href="./?view=ro_viewrtipodependencia&id="+id
                            }
                        })
                    }
                }
            })
            }
        })
        
    })
    
}