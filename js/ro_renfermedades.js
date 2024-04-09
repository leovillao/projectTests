if(document.getElementById('ro_renfermedades')){
    
    $(document).ready(function(){
        
        function loadenfermedad(){
            let ct = ''
            if(document.getElementById('enfermedad')){
                ct = $("#enfermedad").val()
            }
            $.ajax({
                url:'./?action=ro_processempleadosenfermedadesData',
                type:'POST',
                data:{option:1},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML ='<option value="">Seleccione...</option>'
                    let selected = ''
                    $.each(res, function(i, item){
                        if(ct!==''){
                            if(ct==item.id){
                                selected="selected"
                            }else{
                                selected=''
                            }
                        }
                        viewHTML+='<option value="' +item.id+ '"' +selected+'>' +item.name+'</option>'
                    })
                    $("#enid").html(viewHTML)
                }
            })
        }
        loadenfermedad()
        
        $(document).on('click', '.remove-ro_renfermedades', function(){
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
                $.ajax({
                    url:'./?action=ro_processempleadosenfermedades',
                    type:'POST',
                    data:{id:id,tipo:3},
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
                                    location.href="./?view=ro_viewrenfermedades&id="+dlt
                                }
                            })
                        }
                    }
                })
            })
        })
        
        $("#save-modified-ro_renfermedades").on('click', function(e){
            let id = $("#idEmpleado").val()
            e.preventDefault()
            $.ajax({
                url:'./?action=ro_processempleadosenfermedades',
                type:'POST',
                data:$("#form-ro_renfermedades").serialize(),
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
                                location.href="./?view=ro_viewrenfermedades&id="+id
                            }
                        })
                    }
                }
            })
        })
        
    })
    
}