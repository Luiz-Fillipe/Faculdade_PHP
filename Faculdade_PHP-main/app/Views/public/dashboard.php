<?php

require_once __DIR__ . '/../../helpers/auth.php';

require_login();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel Administrativo — Portal SI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
        }

        .btn-marca-azul {
            background-color: #12294B;
            color: #ffffff;
            border: none;
        }

        .btn-marca-azul:hover,
        .btn-marca-azul:focus {
            background-color: #0d1f3a;
            color: #ffffff;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark mb-4" style="background-color: #12294B;">
        <div class="container">
            <span class="navbar-brand">Portal SI</span>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body text-center">

                        <h2 class="card-title mb-4">
                            Login realizado com sucesso!
                        </h2>

                        <p class="mb-2">
                            Bem-vindo,
                            <strong>
                                <?= htmlspecialchars($_SESSION['usuario_nome'], ENT_QUOTES, 'UTF-8'); ?>
                            </strong>.
                        </p>

                        <p class="mb-4">
                            Perfil:
                            <strong>
                                <?= htmlspecialchars($_SESSION['usuario_perfil'], ENT_QUOTES, 'UTF-8'); ?>
                            </strong>
                        </p>

                        <a href="logout.php" class="btn btn-marca-azul w-100">
                            Encerrar Sessão
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>