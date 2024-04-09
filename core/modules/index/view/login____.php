<?php
var_dump($_SESSION);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css\bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,800;1,200&display=swap" rel="stylesheet">
    <title>HalconStore</title>
</head>
<body>
<style>
    .titulo{
        font-family: 'Work Sans', sans-serif;
        color: #266525;
        font-size:2.5rem;
        text-align: center;
    }
    .fuenteTitulo{
        font-family: 'Work Sans', sans-serif;
        color: #266525;
    }
    .sectionLogin {
        height: 95vh;
        width: 75vw;
        margin: auto;
        background-image: url("storage/logoConfig/003.jpg");
        background-size: cover;
        background-origin: content-box;
        background-position: unset;
    }
    .s{
        font-size: 2rem;
        text-align: center;
    }
</style>
<?php
View::load("loginlp");
?>

<!--<script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>-->
<!--<script>-->
<!--    $(function () {-->
<!--        $("#btnIngresar").click(function (e) {-->
<!--            e.preventDefault()-->
<!--            $.ajax({-->
<!--                url:"./?action=processLoginLP",-->
<!--                type:"POST",-->
<!--                data:$("#formIngreso").serialize(),-->
<!--                success:function (respond) {-->
<!--                    console.log(respond)-->
<!--                }-->
<!--            })-->
<!--        })-->
<!--    })-->
<!--</script>-->
</body>
</html>