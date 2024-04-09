if(document.getElementById("ro_documentos")){
    
    $(document).ready(function(){
        
        $(document).on('click', '.remove-ro_documentos', function(){
            let id = $(this).closest('tr').find('td:eq(0)').text()
            //console.log(id)
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
                        url:'./?action=ro_processdocumentos',
                        type:'POST',
                        data:{id:id,tipo:3},
                        success:function(resp){
                        //   console.log(resp)
                            let res = JSON.parse(resp)
                            
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
                                     location.href='./?view=ro_viewdocumentos'   
                                    }
                                })
                            }
                        }
                    })
                }
            })
            
        })
        
        $("#save-modified-ro_documentos").click(function(e){
            e.preventDefault()
            let descripcion = $("#dodescrip").val()
            if(descripcion.length==0){
                Swal.fire({
                    icon:'error',
                    title:"Debe ingresar el registro correspondiente",
                })
            }else{
                    $.ajax({
                    url:'./?action=ro_processdocumentos',
                    type:'POST',
                    data:$("#form-ro_documentos").serialize(),
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
                                title:res.substr(2)
                            }).then((result)=>{
                                if(result.isConfirmed){
                                    location.href='./?view=ro_viewdocumentos'
                                }
                            })
                        }
                    }
                })
            }
            
        })
        
    })
    
}