if (document.getElementById('menus')) {
    $(document).ready(function () {
        let perfil = $("#perfil").val()
        console.log("menus")

        loadData(perfil)

        function loadData(perfil) {
            $("#table-menus").DataTable({
                "responsive": true,
                "destroy": true,
                "aaSorting": [],
                "ordering": false,
                "lengthMenu": [[-1], ["Todos"]],
                "ajax": {
                    "method": "POST",
                    "url": "index.php?action=loadMenus",
                    "data": {"tipo": 1, "perfil": perfil}
                },
                "columns": [
                    {"data": "orden"},
                    {"data": "nombre"},
                    {"data": "estado"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            });
        }

        $(document).on('click','.btn-reporte-acc',function(){
            $("#modalAccesosReportes").modal('show')
            let idMenu = $(this).attr('idmenu')
            let mnacceso = $(this).attr('mnacceso')
            let nombre = $(this).closest('tr').find('td:eq(1)').text()
            $("#modalAccesosReportes .modal-title").text(nombre)
            $("#modalAccesosReportes #idMenu").val(idMenu)
            $("#modalAccesosReportes #mnacceso").val(mnacceso)
            loadsMenusForPerfil(idMenu)
        })

        $(document).on('click','.chk-estado',function(){
            let seleccionado = 0
            if (this.checked) {
                seleccionado = 1
            }
            let perfil = $("#perfil").val()
            let menu = $("#modalAccesosReportes #idMenu").val()
            let mnacceso = $("#modalAccesosReportes #mnacceso").val()
            let idMenu = $(this).attr('genaccid') // ID DEL REPORTE EN LA TABLA GEN_REPORTES
            let idAcc = $(this).attr('id') // ID DE LA RELACION CON LA TABLA REPORTE DESDE LA TABLA
            // GEN_ACC_REPORT
            updateAccesoReporte(perfil,idMenu,idAcc,seleccionado,menu,mnacceso)
        })

        function updateAccesoReporte(perfil,idMenu,idAcc,seleccionado,menu,mnacceso){
            $.ajax({
                url:'./?action=updateAccReporte',
                type:'POST',
                data:{
                    "perfil":perfil,
                    "idMenu":idMenu,
                    "idAcc":idAcc,
                    "seleccionado":seleccionado,
                    "menu":menu,
                    "mnacceso":mnacceso,
                },
                success:function (respuesta) {
                    let r = JSON.parse(respuesta)
                    if (r.substr(0,1) == 1){
                        Swal.fire({
                            icon: 'success',
                            title: r.substr(2),
                            // text: 'Something went wrong!',
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: r.substr(2),
                            // text: 'Something went wrong!',
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                    }
                    loadsMenusForPerfil(menu)
                }
            })
        }

        /** Carga las opciones del menu de la tabla de reportes para asignar el acceso a estos*/
        function loadsMenusForPerfil(idMenu){
            let perfil = $("#perfil").val()
            $("#table-menus-reportes").DataTable().clear().destroy()
            $("#table-menus-reportes").DataTable({
                "responsive": true,
                "destroy": true,
                "aaSorting": [],
                "ordering": false,
                "lengthMenu": [[-1], ["Todos"]],
                "ajax": {
                    "method": "POST",
                    "url": "./?action=loadMenuReport",
                    "data": {"perfil": perfil,"idReporteMenu":idMenu}
                },
                "columns": [
                    {"data": "numero"},
                    {"data": "nombre"},
                    {"data": "descripcion"},
                    {"data": "checkbox"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            });
        }

        function l(menuAcceso) {
            let perfil = $("#perfil").val()
            $.ajax({
                method: "POST",
                url: "./?action=loadMenuReport",
                data: {"perfil": perfil,"id":menuAcceso},
                success: function (r) {
                    console.log(r)
                }
            })
        }

        $(document).on('change', '#perfil', function () {
            let perfil = $(this).val()
            console.log(perfil);
            loadData(perfil)
        })

        // $(this).parents("tr").find('input[type="checkbox"]').attr('checked', true)
        $(document).on('click', '.chk-input', function () {
            let statChk = ''
            if (this.checked) {
                statChk = 1
            } else {
                statChk = 0
            }
            let orden = $(this).attr('orden')
            let idacceso = $(this).attr('idacceso')
            let idpadresub = $(this).attr('idpadresub')
            let idpadre = $(this).attr('idpadre')
            let idmenu = $(this).attr('idmenu')
            let tipo = 4
            let perfil = $("#perfil").val()
            // console.log(idpadresub+'+'+idpadre)
            $.ajax({
                method: "POST",
                url: "index.php?action=loadMenus",
                data: {"tipo": tipo, "perfil": perfil, "idacceso": idacceso, "idmenu": idmenu, "estado": statChk,"idpadres":idpadresub,"idpadre":idpadre,"orden":orden},
                success: function (res) {
                    if (res.substr(0,1) == 1){
                        Swal.fire({
                            icon: 'success',
                            title: res.substr(2),
                            // text: 'Something went wrong!',
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: res.substr(2),
                            // text: 'Something went wrong!',
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                    }
                }
            })
        })

        $(document).on('click', '.chk-input-home', function () {
            let statChk = ''
            if (this.checked) {
                statChk = 1
            } else {
                statChk = 0
            }
            let idacceso = $(this).attr('idacceso')
            let idmenu = $(this).attr('idmenu')
            let tipo = 4
            let perfil = $("#perfil").val()
            $.ajax({
                method: "POST",
                url: "index.php?action=loadMenus",
                data: {"tipo": tipo, "perfil": perfil, "idacceso": idacceso, "idmenu": idmenu, "estado": statChk},
                success: function (res) {
                    // let res = JSON.parse(r)
                    // console.log(res)
                    if (res.substr(0,1) == 1){
                        Swal.fire({
                            icon: 'success',
                            title: res.substr(2),
                            // text: 'Something went wrong!',
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: res.substr(2),
                            // text: 'Something went wrong!',
                            // footer: '<a href="">Why do I have this issue?</a>'
                        })
                    }
                }
            })
        })

        $(document).on('click', '.chk-input-up', function () {
            let statChk = ''
            if (this.checked) {
                statChk = 1
            } else {
                statChk = 0
            }
            let idacceso = $(this).attr('idacceso')
            let idmenu = $(this).attr('idmenu')
            let tipo = 4
            let perfil = $("#perfil").val()
            $.ajax({
                method: "POST",
                url: "index.php?action=loadMenus",
                data: {"tipo": tipo, "perfil": perfil, "idacceso": idacceso, "idmenu": idmenu, "estado": statChk},
                success: function (r) {
                    console.log(r)
                    // loadData(perfil)
                }
            })
        })


    })
}