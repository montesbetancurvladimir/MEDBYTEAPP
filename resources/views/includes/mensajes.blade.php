{{-- Mensaje de éxito--}}
<style>
    .alert-section {
        margin-top: 20px;
    }

    .icon-cell {
        width: 50px;
        text-align: center;
        vertical-align: middle;
    }

    .alert-heading {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .table {
        margin-bottom: 0;
    }

    .alert {
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .alert-success {
        border-left: 5px solid #28a745;
    }

    .alert-danger {
        border-left: 5px solid #dc3545;
    }

</style>

@if (session('success'))
<section class="alert-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="alert alert-success" role="alert">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="icon-cell"><i class="mdi mdi-check-circle-outline text-success"></i></td>
                                <td>
                                    <h5 class="alert-heading">¡Bien hecho!</h5>
                                    <p class="text-muted">{{ session('success') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<br>
@endif

{{-- Mensaje de error--}}
@if (session('error'))
<section class="alert-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="alert alert-danger" role="alert">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="icon-cell"><i class="mdi mdi-alert-circle-outline text-danger"></i></td>
                                <td>
                                    <h5 class="alert-heading">¡Alerta!</h5>
                                    <p class="text-muted">{{ session('error') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<br>
@endif
