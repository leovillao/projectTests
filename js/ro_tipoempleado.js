if(document.getElementById("ro_tipoempleado")){
    
    $(document).ready(function(){
        
        $(document).on('click', '.remove-ro_tipoempleado', function(){
            let id = $(this).closest('tr').find('td:eq(0)').text()
            //console.log(id)
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
                        url:'./?action=ro_processtipoempleado',
                        type: 'POST',
                        data:{id:id, tipo:3},
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
                                        location.href='./?view=ro_viewtipoempleado'
                                    }
                                })
                            }
                        }
                    })
                }
            })
        })
        
        $("#save-modified-ro_tipoempleado").click(function(e){
            e.preventDefault()
            let tipoempleado = $("#tedescrip").val()
            if(tipoempleado.length==0){
                Swal.fire({
                    icon:'error',
                    title: "Debe ingresar el dato correspondiente",
                })
            }else{
                $.ajax({
                    url:'./?action=ro_processtipoempleado',
                    type:'POST',
                    data:$("#form-ro_tipoempleado").serialize(),
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
                                    location.href='./?view=ro_viewtipoempleado'
                                }
                            })
                        }
                    }
                })
            }
        })
        
    })
    
}