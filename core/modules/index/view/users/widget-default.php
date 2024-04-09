<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <a href="index.php?view=newuser" class="btn btn-primary pull-right btn-sm"><i class='glyphicon glyphicon-user'></i>Nuevo Usuario</a>
            <h1>Lista de Usuarios</h1>
            <br>
            <?php
            $users = UserData::getAll();
            if (count($users) > 0){
            // si hay usuarios
            ?>
            <table class="compact display" style="width: 100%" id="table-usuarios">
                <thead>
                <th>Nombre completo</th>
                <th>Username</th>
                <th>Activo</th>
                <th>Perfil</th>
                <th></th>
                </thead>
                <?php foreach ($users as $user) {
                    ?>
                    <tr>
                        <td><?php echo strtoupper($user->name . " " . $user->lastname); ?></td>
                        <td><?php echo $user->username; ?></td>
                        <td>
                            <?php if ($user->is_active): ?>
                                <i class="glyphicon glyphicon-ok"></i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= PerfilesData::getById($user->pfid)->pfnombre?>
                        </td>
                        <td style="width:30px;">
                            <a href="index.php?view=edituser&id=<?php echo $user->id; ?>" class="btn btn-warning btn-xs">Editar</a>
                        </td>
                    </tr>
                    <?php
                }
                echo '</table>';
                } else {
                    echo '<h3>No tiene usuarios...</h3>';
                }
                ?>
        </div>
    </div>
</div>

<script>
    $(function () {
        $("#table-usuarios").DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
            }
        })
    })
</script>
