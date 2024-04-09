if(document.getElementById("ro_empleados")){
    
    $(document).ready(function(){
        
        $("#save-modified-ro_empleados").click(function(e){
            e.preventDefault()
            let datos = new FormData(document.getElementById('form-ro_empleados'))
            $.ajax({
                ur:'./?action=ro_processempleados',
                type:'POST',
                data: datos,
                contentType:false,
                cache:false,
                processData:false,
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
        })
        
    })
    
}