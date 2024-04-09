if(document.getElementById('ro_restudios')){
    
    $(document).ready(function(){
        
        function loadformacion(){
            let ct = ''
            if(document.getElementById('academia')){
                ct = $("#academia").val()
            }
            $.ajax({
                url:'./?action=ro_processempleadosestudioData',
                type:'POST',
                data:{option:1},
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
                    $("#faid").html(viewHTML)
                }
            })
        }
        loadformacion()
        
        $(document).on('click', '.remove-ro_restudios', function(){
          let id = $(this).closest('tr').find('td:eq(0)').text()
          let dlt = $("#idEmpleado").val()
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
                      url:'./?action=ro_processempleadosestudio',
                      type:'POST',
                      data:{id:id, tipo:3},
                      success:function(respond){
                          let res =JSON.parse(respond)
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
                                      location.href="./?view=ro_viewrestudios&id="+dlt
                                  }
                              })
                          }
                      }
                  })
              }
          })
        })
        
        $("#save-modified-ro_restudios").on('click', function(e){
            let formato = $("#ertipo").val()
            let id = $("#idEmpleado").val()
            if(formato.length==0){
                Swal.fire({
                    icon:'error',
                    title:"Debe seleccionar un dato correspondiente"
                })
            }else{
                let descripcion = $("#erdescrip").val()
                if(descripcion.length==0){
                    Swal.fire({
                        icon:'error',
                        title:"Debe ingresar un dato correspondiente"
                    })
                }else{
                    let observacion = $("#erobservacion").val()
                    if(observacion.length==0){
                        Swal.fire({
                            icon:'error',
                            title:"Debe ingresar un comentario"
                        })
                    }else{
                        $.ajax({
                        url:'./?action=ro_processempleadosestudio',
                        type:'POST',
                        data:$("#form-ro_restudios").serialize(),
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
                                        location.href="./?view=ro_viewrestudios&id="+id
                                    }
                                })
                            }
                        }
                    })
                    }
                }
            }
        })
        
    })
    
}