if(document.getElementById("ro_tipodependencia")){
    
    $(document).ready(function(){
        
        $(document).on('click', '.remove-ro_tipodependencia', function(){
            let id=$(this).closest('tr').find('td:eq(0)').text()
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
                    url:'./?action=ro_processtipodependencia',
                    type:'POST',
                    data:{id:id,tipo:3},
                    success:function(respond){
                        //console.log(respond)
                        let res= JSON.parse(respond)
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
                                    location.href='./?view=ro_viewtipodependencia'
                                }
                            })
                        }
                    }
                })
            }
        })
        })
        
        
        $("#save-modified-ro_tipodependencia").click(function(e){
            e.preventDefault()
            let dependencia = $("#tddescrip").val()
            if(dependencia.length==0){
                Swal.fire({
                    icon:'error',
                    title:"Debe ingresar el dato correspondiente",
                })
            }else{
                $.ajax({
                    url:'./?action=ro_processtipodependencia',
                    type:'POST',
                    data:$("#form-ro_tipodependencia").serialize(),
                    success:function(respond){
                        //console.log(respond)
                        let res= JSON.parse(respond)
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
                                    location.href='./?view=ro_viewtipodependencia'
                                }
                            })
                        }
                    }
                })
            }
        })
        
    })
    
}