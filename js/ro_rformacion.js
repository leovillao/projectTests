if(document.getElementById("ro_rformacion")){
    
    $(document).ready(function(){
        
        $(document).on('click', '.remove-ro_rformacion', function(){
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
                        url:'./?action=ro_processrformacion',
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
                                        location.href='./?view=ro_viewrformacion'
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        $("#save-modified-ro_rformacion").click(function(e){
            e.preventDefault()
            let formacion = $("#fadescrip").val()
            if(formacion.length==0){
                Swal.fire({
                    icon:'error',
                    title:"Debe ingresar el dato correspondiente",
                })
            }else{
                $.ajax({
                    url:'./?action=ro_processrformacion',
                    type:'POST',
                    data:$("#form-ro_rformacion").serialize(),
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
                                    location.href='./?view=ro_viewrformacion'
                                }
                            })
                        }
                    }
                })
            }
        })
        
    })
    
}