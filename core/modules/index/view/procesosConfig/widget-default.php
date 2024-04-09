<div class="container">
    <div class="row">
        <div class="col-md-3">
            <button id="btnStock" class="btn btn-primary">StockProcedure</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(document).on('click', '#btnStock', function () {
            $.ajax({
                url: './?action=processConfig',
                type: 'POST',
                success: function (respuesta) {
                    console.log(respuesta)
                }
            })
        })
    })
</script>