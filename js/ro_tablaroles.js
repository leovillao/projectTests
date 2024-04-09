if(document.getElementById('ro_tablaroles')){
    $(document).ready(function(){
         
        $("#quite").click(function(e){
            e.preventDefault()
            $("#largeModal").modal('hide')
            
        })
         
        // $(document).on('change', '.loaddata', function(e){
        //     let datos  = new FormData()
        //     // console.log($(this).parent('localName'))
        //     // let archivo = $(this).val()
        //     let fileList = e.target.files;
        //     // console.log(fileList);
        //     datos.append("file",fileList)
        //     $.ajax({
        //         url:'./?action=ro_tablarolesexcelreader',
        //         type:'POST',
        //         data:datos,
        //         cache: false,
        //         contentType: false,
        //         processData: false,
        //         success:function(respond){
        //             console.log(respond)
        //             //let res = JSON.parse(respond)
        //         }
        //     })
        // })
        
        function loadtipo(){
            $.ajax({
                url:'./?action=ro_processtablaroles',
                type:'POST',
                data:{option:1},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let viewHTML = '<option value="">Tipo de Rol</option>'
                    $.each(res, function(i, item){
                        viewHTML+='<option value="' +item.id+ '">'  +item.name+ '</option>'
                    })
                    $("#rolact").html(viewHTML)
                }
            })
        }
        
        loadtipo()
        
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
        
        function negocio() {
        var checkbox = $(".negocioemp");
        var hidden = $(".unegocio");
        hidden.hide();
            checkbox.change(function() {
                if (checkbox.is(':checked')) {
                    $(".unegocio").fadeIn("200")
                } else {
                    //hidden.hide();
            $(".unegocio").fadeOut("200")
                    $("#negocio").val(""); 
                    
         }
         })
        }
        negocio()
        
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
        
        function subetq() {
        var checkbox = $(".setqemp");
        var hidden = $(".subetq");
        hidden.hide();
            checkbox.change(function() {
                if (checkbox.is(':checked')) {
                    $(".subetq").fadeIn("200")
                } else {
                    //hidden.hide();
            $(".subetq").fadeOut("200")
                    $("#setq").val(""); 
                    
         }
         })
        }
        subetq()
        
        $(document).on('change', '#rolact', function(){
            let rol = $(this).val();
            $.ajax({
                url:'./?action=ro_processtablaroles',
                type:'POST',
                data:{option:2, rol:rol},
                success:function(respond){
                    let res = JSON.parse(respond)
                    $("#trperact").val(res[0])
                    $("#tranioact").val(res[1])
                }
            })
        })
        
        function cargaDatosEmpleados(rol){
            // let tiporol = $("#rolact").val()
            $.ajax({
                url:'./?action=ro_processtablaroles',
                type:'POST', 
                data:{option:3,tiporol:rol},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let i=0
                    let arrayfijo = document.getElementsByClassName("nametable")
                    viewHTML=''
                    viewsino='<option value="">Seleccione</option>'
                    $.each(res.sino, function(i,item){
                            viewsino+='<option value="'+item.id1+'">'+item.name1+'</option>'
                    })
                    $.each(res.empleados, function(i,item){ 
                        viewHTML+='<tr><td>' + item.id +
                        '</td><td>' + item.costo +
                        '</td><td>' + item.empleado + ' ' + item.empleado1 +
                        '</td><td>' + item.empleado2 +
                        '</td><td>' + item.cargo +
                        '</td><td>' + '<select name="afiliacion" class="tablaroles" namecampo="'+arrayfijo[0].value+'" namDef="afiliacion" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[1].value+'" name="diastrab" namDef="diastrab" id="diastrab" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[2].value+'" name="retju" namDef="retju" id="retju" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[3].value+'" name="sueldobass" namDef="sueldobass" id="sueldobass" style=" width:140px">' +
                        '</td><td>' + '<select name="quincena" class="tablaroles" namecampo="'+arrayfijo[4].value+'" namDef="quincena" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[5].value+'" name="quincena1" namDef="quincena1" id="quincena1" style=" width:140px">' +
                        '</td><td>' + '<select name="terol" class="tablaroles" namecampo="'+arrayfijo[6].value+'" namDef="terol" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<select name="terol" class="tablaroles" namecampo="'+arrayfijo[7].value+'" namDef="terol" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<select name="frol" class="tablaroles" namecampo="'+arrayfijo[8].value+'" namDef="frol" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<select name="cobcon" class="tablaroles" namecampo="'+arrayfijo[9].value+'" namDef="cobcon" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[10].value+'" name="dquincena" namDef="dquincena" id="dquincena" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[11].value+'" name="gdirvivienda" namDef="gdirvivienda" id="gdirvivienda" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[12].value+'" name="gdirsalud" namDef="gdirsalud" id="gdirsalud" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[13].value+'" name="gdireducacion" namDef="gdireducacion" id="gdireducacion" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[14].value+'" name="gdiralimentacion" namDef="gdiralimentacion" id="gdiralimentacion" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[15].value+'" name="gdirvestimenta" namDef="gdirvestimenta" id="gdirvestimenta" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[16].value+'" name="decimocuarto" namDef="decimocuarto" id="decimocuarto" style=" width:140px">' +
                        '</td><td>' + '<select name="antprev" class="tablaroles" namecampo="'+arrayfijo[17].value+'" namDef="antprev" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number"class="tablaroles" namecampo="'+arrayfijo[18].value+'" name="viaticos" namDef="viaticos" id="viaticos" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[19].value+'" name="dvacaciones" namDef="dvacaciones" id="dvacaciones" step="any" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[20].value+'" name="lvacaciones" namDef="lvacaciones" id="lvacaciones" step="any" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[21].value+'" name="pbvacaciones" namDef="pbvacaciones" id="pbvacaciones" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[22].value+'" name="vacaciones" namDef="vacaciones" id="vacaciones" style=" width:140px">' +
                        '</td>'
                    })
                    $("#tiporoldata").html(viewHTML)
                }
            })
        }
        
        $("#rolact").on('change', function(e){
            e.preventDefault()
            
        })
        
        
        
        
        
        
        $("#locked").click(function(e){
            e.preventDefault()
            let rolact = $("#rolact").val()
            $(".empleado").hide()
            $(".cargo").hide()
            $(".unegocio").hide()
            $(".ccosto").hide()
            $(".subetq").hide()
            $(".tiemp").prop('checked', false)
            $(".cargosemp").prop('checked', false)
            $(".negocioemp").prop('checked', false)
            $(".costoemp").prop('checked', false)
            $(".setqemp").prop('checked', false)
            $.ajax({
                url:'./?action=ro_processtablaroles',
                type:'POST',
                data:{option:7,rolact:rolact},
                success:function(respond){
                    let res = JSON.parse(respond)
                    let i=0
                    let arrayfijo = document.getElementsByClassName("nametable")
                    viewHTML=''
                    viewsino='<option value="">Seleccione</option>'
                    $.each(res.sino, function(i,item){
                            viewsino+='<option value="'+item.id1+'">'+item.name1+'</option>'
                        })
                    $.each(res.empleados, function(i,item){ 
                        viewHTML+='<tr><td>' + item.id +
                        '</td><td>' + item.costo +
                        '</td><td>' + item.empleado + ' ' + item.empleado1 +
                        '</td><td>' + item.empleado2 +
                        '</td><td>' + item.cargo +
                        '</td><td>' + '<select name="afiliacion" class="tablaroles" namecampo="'+arrayfijo[0].value+'" namDef="afiliacion" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[1].value+'" name="diastrab" namDef="diastrab" id="diastrab" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[2].value+'" name="retju" namDef="retju" id="retju" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[3].value+'" name="sueldobass" namDef="sueldobass" id="sueldobass" style=" width:140px">' +
                        '</td><td>' + '<select name="quincena" class="tablaroles" namecampo="'+arrayfijo[4].value+'" namDef="quincena" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[5].value+'" name="quincena1" namDef="quincena1" id="quincena1" style=" width:140px">' +
                        '</td><td>' + '<select name="terol" class="tablaroles" namecampo="'+arrayfijo[6].value+'" namDef="terol" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<select name="terol" class="tablaroles" namecampo="'+arrayfijo[7].value+'" namDef="terol" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<select name="frol" class="tablaroles" namecampo="'+arrayfijo[8].value+'" namDef="frol" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<select name="cobcon" class="tablaroles" namecampo="'+arrayfijo[9].value+'" namDef="cobcon" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[10].value+'" name="dquincena" namDef="dquincena" id="dquincena" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[11].value+'" name="gdirvivienda" namDef="gdirvivienda" id="gdirvivienda" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[12].value+'" name="gdirsalud" namDef="gdirsalud" id="gdirsalud" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[13].value+'" name="gdireducacion" namDef="gdireducacion" id="gdireducacion" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[14].value+'" name="gdiralimentacion" namDef="gdiralimentacion" id="gdiralimentacion" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[15].value+'" name="gdirvestimenta" namDef="gdirvestimenta" id="gdirvestimenta" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[16].value+'" name="decimocuarto" namDef="decimocuarto" id="decimocuarto" style=" width:140px">' +
                        '</td><td>' + '<select name="antprev" class="tablaroles" namecampo="'+arrayfijo[17].value+'" namDef="antprev" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number"class="tablaroles" namecampo="'+arrayfijo[18].value+'" name="viaticos" namDef="viaticos" id="viaticos" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[19].value+'" name="dvacaciones" namDef="dvacaciones" id="dvacaciones" step="any" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[20].value+'" name="lvacaciones" namDef="lvacaciones" id="lvacaciones" step="any" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[21].value+'" name="pbvacaciones" namDef="pbvacaciones" id="pbvacaciones" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[22].value+'" name="vacaciones" namDef="vacaciones" id="vacaciones" style=" width:140px">' +
                        '</td>'
                    })
                    $("#tiporoldata").html(viewHTML)
                }
            })
        })
        
        
        $(document).on('click', '#applied', function(event){
            event.preventDefault()
            $.ajax({
                url:'./?action=ro_processtablarolesform',
                type:'POST',
                data:$("#busqueda").serialize(),
                success:function(respond){
                    let res = JSON.parse(respond)
                    let i=0
                    let arrayfijo = document.getElementsByClassName("nametable")
                    viewHTML=''
                    viewsino='<option value="">Seleccione</option>'
                    $.each(res.sino, function(i,item){
                            viewsino+='<option value="'+item.id1+'">'+item.name1+'</option>'
                        })
                    $.each(res.empleados, function(i,item){ 
                        viewHTML+='<tr><td>' + item.id +
                        '</td><td>' + item.costo +
                        '</td><td>' + item.empleado + ' ' + item.empleado1 +
                        '</td><td>' + item.empleado2 +
                        '</td><td>' + item.cargo +
                        '</td><td>' + '<select name="afiliacion" class="tablaroles" namecampo="'+arrayfijo[0].value+'" namDef="afiliacion" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[1].value+'" name="diastrab" namDef="diastrab" id="diastrab" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[2].value+'" name="retju" namDef="retju" id="retju" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[3].value+'" name="sueldobass" namDef="sueldobass" id="sueldobass" style=" width:140px">' +
                        '</td><td>' + '<select name="quincena" class="tablaroles" namecampo="'+arrayfijo[4].value+'" namDef="quincena" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[5].value+'" name="quincena1" namDef="quincena1" id="quincena1" style=" width:140px">' +
                        '</td><td>' + '<select name="terol" class="tablaroles" namecampo="'+arrayfijo[6].value+'" namDef="terol" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<select name="terol" class="tablaroles" namecampo="'+arrayfijo[7].value+'" namDef="terol" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<select name="frol" class="tablaroles" namecampo="'+arrayfijo[8].value+'" namDef="frol" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<select name="cobcon" class="tablaroles" namecampo="'+arrayfijo[9].value+'" namDef="cobcon" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[10].value+'" name="dquincena" namDef="dquincena" id="dquincena" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[11].value+'" name="gdirvivienda" namDef="gdirvivienda" id="gdirvivienda" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[12].value+'" name="gdirsalud" namDef="gdirsalud" id="gdirsalud" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[13].value+'" name="gdireducacion" namDef="gdireducacion" id="gdireducacion" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[14].value+'" name="gdiralimentacion" namDef="gdiralimentacion" id="gdiralimentacion" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[15].value+'" name="gdirvestimenta" namDef="gdirvestimenta" id="gdirvestimenta" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[16].value+'" name="decimocuarto" namDef="decimocuarto" id="decimocuarto" style=" width:140px">' +
                        '</td><td>' + '<select name="antprev" class="tablaroles" namecampo="'+arrayfijo[17].value+'" namDef="antprev" style="width:75px">'+viewsino+'</select>' +
                        '</td><td>' + '<input type="number"class="tablaroles" namecampo="'+arrayfijo[18].value+'" name="viaticos" namDef="viaticos" id="viaticos" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[19].value+'" name="dvacaciones" namDef="dvacaciones" id="dvacaciones" step="any" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[20].value+'" name="lvacaciones" namDef="lvacaciones" id="lvacaciones" step="any" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[21].value+'" name="pbvacaciones" namDef="pbvacaciones" id="pbvacaciones" style=" width:140px">' +
                        '</td><td>' + '<input type="number" class="tablaroles" namecampo="'+arrayfijo[22].value+'" name="vacaciones" namDef="vacaciones" id="vacaciones" style=" width:140px">' +
                        '</td>'
                    })
                    $("#tiporoldata").html(viewHTML)
                }
            })
        })
        
        
        
        function loadempleado(){
            $.ajax({
            url:'./?action=ro_processempleadosData',
            type:'POST',
            data:{option:7},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Tipo Empleado...</option>'
                let selected=''
                $.each(res, function(i, item){
                    
                    viewHTML += '<option value="' +item.id + '">' + item.name + '</option>'
                })
                $("#emtipo").html(viewHTML)
            }
        })
        }
        loadempleado()
        
        function loadcargos(){
            $.ajax({
            url:'./?action=ro_processempleadosData',
            type:'POST',
            data:{option:8},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Cargos...</option>'
                $.each(res, function(i, item){
                    viewHTML += '<option value="' +item.id + '">' + item.name + '</option>'
                })
                $("#cargos").html(viewHTML) 
            }
        })
        }
        loadcargos()
        
        function loadnegocios(){
            $.ajax({
            url:'./?action=ro_processtablaroles',
            type:'POST',
            data:{option:4},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Negocios...</option>'
                $.each(res, function(i, item){
                    viewHTML += '<option value="' +item.id + '">' + item.name + '</option>'
                })
                $("#negocio").html(viewHTML) 
            }
        })
        }
        loadnegocios()
        
        function loadcosto(){
            $.ajax({
            url:'./?action=ro_processtablaroles',
            type:'POST',
            data:{option:5},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Costo...</option>'
                $.each(res, function(i, item){
                    viewHTML += '<option value="' +item.id + '">' + item.name + '</option>'
                })
                $("#ccosto").html(viewHTML) 
            }
        })
        }
        loadcosto()
        
        function loadsetq(){
            $.ajax({
            url:'./?action=ro_processtablaroles',
            type:'POST',
            data:{option:6},
            success:function(respond){
                let res = JSON.parse(respond)
                let viewHTML= '<option value="">Subetiquetas...</option>'
                $.each(res, function(i, item){
                    viewHTML += '<option value="' +item.id + '">' + item.name + '</option>'
                })
                $("#setq").html(viewHTML)
            }
        })
        }
        loadsetq()
        
        
        /*$("#modified-empleados").click(function(e){
            let formDatos = new FormData();
           $("#tiporoldata tr").each(function () {
                    let row = $(this)
                    let id = row.find('td').eq(0).text()
                    let afiliacion = row.find('td').eq(5).find('select').val()
                    let diastrab = row.find('td').eq(6).find('input').val()
                    let retju = row.find('td').eq(7).find('input').val()
                    let sueldobass = row.find('td').eq(8).find('input').val()
                    let quincena = row.find('td').eq(9).find('select').val()
                    let quincena1 = row.find('td').eq(10).find('input').val()
                    let terol = row.find('td').eq(11).find('select').val()
                    let torol = row.find('td').eq(12).find('select').val()
                    let frol = row.find('td').eq(13).find('select').val()
                    let cobcon = row.find('td').eq(14).find('select').val()
                    let dquincena = row.find('td').eq(15).find('input').val()
                    let gdirvivienda = row.find('td').eq(16).find('input').val() 
                    let gdirsalud = row.find('td').eq(17).find('input').val()
                    let gdireducacion = row.find('td').eq(18).find('input').val()
                    let gdiralimentacion = row.find('td').eq(19).find('input').val()
                    let gdirvestimenta = row.find('td').eq(20).find('select').val()
                    let decimocuarto = row.find('td').eq(21).find('select').val()
                    let antprev = row.find('td').eq(22).find('select').val()
                    let viaticos = row.find('td').eq(23).find('input').val()
                    let dvacaciones = row.find('td').eq(24).find('select').val()
                    let lvacaciones = row.find('td').eq(25).find('select').val()
                    let pbvacaciones = row.find('td').eq(26).find('input').val()
                    let vacaciones = row.find('td').eq(27).find('input').val()
                    
                    formDatos.append("id",id)
                    formDatos.append("afiliacion",afiliacion)
                    formDatos.append("diastrab",diastrab)
                    formDatos.append("retju",retju)
                    formDatos.append("sueldobass",sueldobass)
                    formDatos.append("quincena",quincena)
                    formDatos.append("quincena1",quincena1)
                    formDatos.append("terol",terol)
                    formDatos.append("torol",torol)
                    formDatos.append("frol",frol)
                    formDatos.append("cobcon",cobcon)
                    formDatos.append("dquincena",dquincena)
                    formDatos.append("gdirvivienda",gdirvivienda)
                    formDatos.append("gdirsalud",gdirsalud)
                    formDatos.append("gdireducacion",gdireducacion)
                    formDatos.append("gdiralimentacion",gdiralimentacion)
                    formDatos.append("gdirvestiemtna",gdirvestimenta)
                    formDatos.append("decimocuarto",decimocuarto)
                    formDatos.append("antprev",antprev)
                    formDatos.append("viaticos",viaticos)
                    formDatos.append("dvacaciones",dvacaciones)
                    formDatos.append("lvacaciones",lvacaciones)
                    formDatos.append("pbvacaciones",pbvacaciones)
                    formDatos.append("vacaciones",vacaciones)
                    
                    $.ajax({
                        url:'./?action=ro_tablarolesupdate',
                        type:'POST',
                        data: formDatos,
                        contentType:false,
                        cache:false,
                        processData: false,
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
                                        location.href="./?view=ro_tablaroles"
                                    }
                                })
                            }
                        }
                    })
           })
           
        })*/
        
        $(document).on('change','.tablaroles',function(){
            let id = $(this).closest('tr').find('td:eq(0)').text()
            let namecampo = $(this).attr('namecampo')
            let valor = $(this).val()
            $.ajax({
                url:'./?action=ro_tablarolesupdate',
                type:'POST',
                data:{id:id , namecampo:namecampo,valor:valor},
                success:function(respond){
                    let res = JSON.parse(respond)
                    if(res.substr(0,1)==0){
                        Swal.fire({
                            icon:'error',
                                showConfirmButton:false,
                                title:res.substr(2),
                                timer:1000,
                                width:250,
                                height:250,
                                toast:true,
                        })
                        }else{
                            Swal.fire({
                                icon:'success',
                                position:'top',
                                showConfirmButton:false,
                                title:res.substr(2),
                                showClass: {
                                popup: `
                                  animate__animated
                                  animate__fadeInUp
                                  animate__faster
                                `
                                },
                                timer:1000,
                                width:200,
                            })
                            
                    }
                }
            })
            
        })
        
    })
}