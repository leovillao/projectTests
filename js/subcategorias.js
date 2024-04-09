if(document.getElementById('subcategoryfluid')){
    $(document).ready(function(){
        
        $(document).on('click', '.remove-scategoria', function(){
            let id= $(this).closest('tr').find('td:eq(0)').text()
            //console.log(id)
            Swal.fire({
                icon:'warning',
                title:"Eliminar registro?",
                text:"Esta accion no se puede deshacer",
                showCancelButton:'true',
                confirmButtonColor:'3085d6',
                cancelButtonColor:'#d33',
                confirmButtonText:'Si, confirmar',
            }).then((result)=>{
                if(result.isConfirmed){
                    $.ajax({
                        url:'./?action=processsubcategorias',
                        type:'POST',
                        data: {id:id, tipo:3},
                        success:function(request){
                            let rest = JSON.parse(request)
                            if(rest.substr(0,1)==0){
                                Swal.fire({
                                    icon:'error',
                                    title:rest.substr(2),
                                })
                            }else{
                                Swal.fire({
                                    icon:'success',
                                    title:rest.substr(2),
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                        location.href='./?view=subcategorias'
                                    }
                                })
                            }
                        }
                    })
                }
            })
            
        })
        
        
        loadCategoria()
        
         function loadCategoria() {
             let ct = ''
             if(document.getElementById('sc')){
                 ct = $("#sc").val()
             }
             $.ajax({
                url: './?action=processsubcategoriasData',
                type: 'POST',
                data: {option: 3},
                success: function (resultado) {
                    let res = JSON.parse(resultado)
                    let viewHtml = '<option value="">Seleccione categoria...</option>'
                    let selected = ''
                    
                    $.each(res, function (i, item) {
                        if (ct != ''){
                            if (ct == item.id){
                                selected = "selected"
                            }else{
                                selected = ""
                            }
                        }
                        viewHtml += '<option value="' + item.id + '" '+selected+' >' + item.name + '</option>'
                    });
                    $("#categoria").html(viewHtml)
                }
            })
        }

        
        
        $("#save-modified-scategory").click(function(e){
            e.preventDefault()
            let nombre = $("#ct2name").val()
            if(nombre.length==0){
                Swal.fire({
                    icon:'error',
                    title: "Debe ingresar el nombre de la subcategoria",
                })
            }else{
                let descripcion = $("#ct2description").val()
                if(descripcion.length==0){
                    Swal.fire({
                        icon:'error',
                        title:"Debe ingresar la descripcion",
                    })
                }else{
                    $.ajax({
                        url:'./?action=processsubcategorias',
                        type:'POST',
                        data: $("#form-scategoria").serialize(),
                        success:function(respond){
                            //console.log(respond)
                            let res = JSON.parse(respond)
                            if(res.substr(0,1)==0){
                                Swal.fire({
                                    icon: 'error',
                                    title:res.substr(2),
                                })
                            }else{
                                Swal.fire({
                                    icon:'success',
                                    title:res.substr(2),
                                }).then((result)=>{
                                    if(result.isConfirmed){
                                        location.href='./?view=subcategorias'
                                    }
                                })
                            }
                        }
                    })
                }
            }
            
        })
        
    })
    
    
}