if (document.getElementById('menus')) {
    $(document).ready(function () {
        let perfil = $("#perfil").val()

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
                    // {"defaultContent": "<button class='btn btn-xs btn-success btn-edit-perf'><i class=\"fa fa-edit\" aria-hidden=\"true\"></i></button><button class='btn-deleted-perf btn btn-danger btn-xs'><i class=\"fa fa-remove\" aria-hidden=\"true\"></i></button>"},
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
                },
            });
        }

        // var tableProTot = /

        l(perfil)

        function l(perfil) {
            $.ajax({
                method: "POST",
                url: "index.php?action=loadMenus",
                data: {"tipo": 1, "perfil": perfil},
                success: function (r) {
                    // console.log(r)
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
                success: function (r) {
                    console.log(r)
                    // loadData(perfil)
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
                success: function (r) {
                    console.log(r)
                    // loadData(perfil)
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