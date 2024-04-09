if(document.getElementById('ro_observaciones')){
    
    $(document).ready(function(){
        
        $("#rolact").change(function(){
            $(".predata").show()
        })
         
        $("#quite").click(function(e){
            e.preventDefault()
            $("#largeModal").modal('hide')
            
        })
        
        function empleado() {
        var checkbox = $(".tiemp");
        var hidden = $(".empleado");
        hidden.hide();
            checkbox.change(function() {
                if (checkbox.is(':checked')) {
                    $(".empleado").fadeIn("200")
                } else {
                    //hidden.hide();
            $(".empleado").fadeOut("200")
                    $("#emtipo").val(""); 
                    
         }
         })
        }
        empleado()
        
        function cargos() {
        var checkbox = $(".cargosemp");
        var hidden = $(".cargo");
        hidden.hide();
            checkbox.change(function() {
                if (checkbox.is(':checked')) {
                    $(".cargo").fadeIn("200")
                } else {
                    //hidden.hide();
            $(".cargo").fadeOut("200")
                    $("#cargos").val(""); 
                    
         }
         })
        }
        cargos()
        
        function costo() {
        var checkbox = $(".costoemp");
        var hidden = $(".ccosto");
        hidden.hide();
            checkbox.change(function() {
                if (checkbox.is(':checked')) {
                    $(".ccosto").fadeIn("200")
                } else {
                    //hidden.hide();
            $(".ccosto").fadeOut("200")
                    $("#ccosto").val(""); 
                    
         }
         })
        }
        costo()
        
        function loadtiporol(){
            $.ajax({
                url:'./?action=ro_processobservaciones',
                type:'POST',
                data:{option:1},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML='<option value="">Rol Actual</option>'
                    $.each(res, function(i, item){
                        viewHTML += '<option value="'+item.id+'">'+item.name+'</option>'
                    })
                    $("#rolact").html(viewHTML)
                }
            })
        }
        
        loadtiporol()
        
        $(document).on('input', '#rolact', function(){
            let rol = $(this).val()
            $.ajax({
                url:'./?action=ro_processobservaciones',
                type:'POST',
                data:{option:2, rol:rol},
                success:function(respond){
                    let res = JSON.parse(respond)
                    $("#trperact").val(res[0])
                    $("#tranioact").val(res[1])
                }
            })
        })
        
        function loadtipoemp(){
            $.ajax({
                url:'./?action=ro_processobservaciones',
                type:'POST',
                data:{option:3},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML='<option value="">Tipo Empleado</option>'
                    $.each(res, function(i, item){
                        viewHTML += '<option value="'+item.id+'">'+item.name+'</option>'
                    })
                    $("#emtipo").html(viewHTML)
                }
            })
        }
        
        loadtipoemp()
        
        function loadcosto(){
            $.ajax({
                url:'./?action=ro_processobservaciones',
                type:'POST',
                data:{option:4},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML='<option value="">Centro Costo</option>'
                    $.each(res, function(i, item){
                        viewHTML += '<option value="'+item.id+'">'+item.name+'</option>'
                    })
                    $("#ccosto").html(viewHTML)
                }
            })
        }
        
        loadcosto()
        
        function loadcargos(){
            $.ajax({
                url:'./?action=ro_processobservaciones',
                type:'POST',
                data:{option:5},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML='<option value="">Cargos</option>'
                    $.each(res, function(i, item){
                        viewHTML += '<option value="'+item.id+'">'+item.name+'</option>'
                    })
                    $("#cargos").html(viewHTML)
                }
            })
        }
        
        loadcargos()
        
        function cargadatosEmpleados(rol){
            let tiporol = $("#rolact").val()
             $.ajax({
                 url:'./?action=ro_processobservaciones',
                 type:'POST', 
                 data:{option:6,tiporol:rol},
                 success:function(respond){
                     let res = JSON.parse(respond)
                     let i=0
                     let arrayfijo = document.getElementsByClassName("nametable")
                     viewHTML=''
                     let viewHTML2 = 0
                     $.each(res, function(i,item){ 
                         
                         viewHTML2 = i+1
                         
                         viewHTML+='<tr><td>' + item.id +
                         '</td><td>' + item.costo +
                         '</td><td>' + item.empleado + ' ' + item.empleado1 +
                         '</td><td>' + item.empleado2 +
                         '</td><td>' + item.cargo +
                         '</td><td>' + '<input type="number" value="'+item.item1+'" class="tablaroles" namecampo="'+arrayfijo[0].value+'" name="diastrab" namDef="diastrab" id="diastrab" step="any" style=" width:75px">' +
                         '</td><td>' + '<input type="number" value="'+item.item2+'" class="tablaroles" namecampo="'+arrayfijo[1].value+'" name="diastrab" namDef="diastrab" id="diastrab" step="any" style=" width:75px">' +
                         '</td><td>' + '<input type="number" value="'+item.item3+'" class="tablaroles" namecampo="'+arrayfijo[2].value+'" name="retju" namDef="retju" id="retju" step="any" style=" width:75px">' +
                         '</td><td>' + '<input type="text" value="'+item.item4+'" class="tablaroles" namecampo="'+arrayfijo[3].value+'" name="roobserva" namDef="roobserva" id="roobserva" style=" width:200px">' +
                         '</td></tr>'
                     })
                     $("#tiporoldata").html(viewHTML)
                     $("#rototal").val(viewHTML2)
                 }
             })
        }
        
        $("#rolact").on('change', function(e){
            e.preventDefault()
            cargadatosEmpleados($(this).val())
        })
        
        $(document).on('click', '#applied', function(event){
            event.preventDefault()
            $.ajax({
                url:'./?action=ro_processobservacionesform',
                type:'POST',
                data:$("#filtersearch").serialize(),
                success:function(respond){
                    let res = JSON.parse(respond)
                     let i=0
                     let arrayfijo = document.getElementsByClassName("nametable")
                     viewHTML=''
                     let viewHTML2 = 0
                     $.each(res, function(i,item){ 
                         viewHTML2 = i+1
                         viewHTML+='<tr><td>' + item.id +
                         '</td><td>' + item.costo +
                         '</td><td>' + item.empleado + ' ' + item.empleado1 +
                         '</td><td>' + item.empleado2 +
                         '</td><td>' + item.cargo +
                         '</td><td>' + '<input type="number" value="'+item.item1+'" class="tablaroles" namecampo="'+arrayfijo[0].value+'" name="diastrab" namDef="diastrab" id="diastrab" step="any" style=" width:75px">' +
                         '</td><td>' + '<input type="number" value="'+item.item2+'" class="tablaroles" namecampo="'+arrayfijo[1].value+'" name="diastrab" namDef="diastrab" id="diastrab" step="any" style=" width:75px">' +
                         '</td><td>' + '<input type="number" value="'+item.item3+'" class="tablaroles" namecampo="'+arrayfijo[2].value+'" name="retju" namDef="retju" id="retju" step="any" style=" width:75px">' +
                         '</td><td>' + '<input type="text" value="'+item.item4+'" class="tablaroles" namecampo="'+arrayfijo[3].value+'" name="roobserva" namDef="roobserva" id="roobserva" style=" width:200px">' +
                         '</td></tr>'
                     })
                     $("#tiporoldata").html(viewHTML)
                     $("#rototal").val(viewHTML2)
                }
            })
        })
        
    })
    
    
}