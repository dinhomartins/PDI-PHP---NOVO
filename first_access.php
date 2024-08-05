<?php
session_start();
include 'api/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verifica se o email existe na tabela de usuários
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $userExists = $result->num_rows > 0;
    } else {
        echo '<script>alert("Erro na preparação da consulta de usuários: ' . $conn->error . '");</script>';
        $userExists = false;
    }

    // Verifica se o email existe na tabela de gestores
    $sql_manager = "SELECT id FROM managers WHERE email = ?";
    $stmt_manager = $conn->prepare($sql_manager);
    if ($stmt_manager) {
        $stmt_manager->bind_param('s', $email);
        $stmt_manager->execute();
        $result_manager = $stmt_manager->get_result();
        $managerExists = $result_manager->num_rows > 0;
    } else {
        echo '<script>alert("Erro na preparação da consulta de gestores: ' . $conn->error . '");</script>';
        $managerExists = false;
    }

    if ($userExists) {
        $userId = $result->fetch_assoc()['id'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql_update = "UPDATE users SET password = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update) {
            $stmt_update->bind_param('si', $hashedPassword, $userId);
            if ($stmt_update->execute()) {
                echo '<script>alert("Senha cadastrada com sucesso"); window.location.href = "login.php";</script>';
            } else {
                echo '<script>alert("Erro ao cadastrar senha");</script>';
            }
            $stmt_update->close();
        } else {
            echo '<script>alert("Erro na preparação da consulta de atualização de usuários: ' . $conn->error . '");</script>';
        }
    } elseif ($managerExists) {
        $managerId = $result_manager->fetch_assoc()['id'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql_update_manager = "UPDATE managers SET password = ? WHERE id = ?";
        $stmt_update_manager = $conn->prepare($sql_update_manager);
        if ($stmt_update_manager) {
            $stmt_update_manager->bind_param('si', $hashedPassword, $managerId);
            if ($stmt_update_manager->execute()) {
                echo '<script>alert("Senha cadastrada com sucesso"); window.location.href = "login.php";</script>';
            } else {
                echo '<script>alert("Erro ao cadastrar senha");</script>';
            }
            $stmt_update_manager->close();
        } else {
            echo '<script>alert("Erro na preparação da consulta de atualização de gestores: ' . $conn->error . '");</script>';
        }
    } else {
        echo '<script>alert("Email não cadastrado, favor procurar o administrador");</script>';
    }

    if ($stmt) $stmt->close();
    if ($stmt_manager) $stmt_manager->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Primeiro Acesso - PDI Sistema</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Primeiro Acesso - PDI Sistema</h1>
    </header>
    <main class="p-4 w-full max-w-md">
        <section class="bg-white p-8 shadow rounded">
            <h2 class="text-xl mb-4">Cadastrar Senha</h2>
            <form method="POST" action="first_access.php">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="border p-2 w-full" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                    <input type="password" name="password" id="password" class="border p-2 w-full" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white p-2 w-full">Cadastrar Senha</button>
            </form>
            <button onclick="window.location.href='login.php'" class="bg-gray-500 text-white p-2 w-full mt-2">Voltar</button>
        </section>
    </main>
</body>
</html>
