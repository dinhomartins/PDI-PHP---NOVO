<?php
session_start();
include 'api/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    // Verifica se o email existe na tabela de usuários
    $sql = "SELECT id, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $userExists = $result->num_rows > 0;

    // Verifica se o email existe na tabela de gestores
    $sql_manager = "SELECT id, password FROM managers WHERE email = ?";
    $stmt_manager = $conn->prepare($sql_manager);
    $stmt_manager->bind_param('s', $email);
    $stmt_manager->execute();
    $result_manager = $stmt_manager->get_result();
    $managerExists = $result_manager->num_rows > 0;

    if ($userExists) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($remember) {
                setcookie("email", $email, time() + (86400 * 30), "/");
                setcookie("password", $password, time() + (86400 * 30), "/");
            }

            // Redireciona de acordo com o papel do usuário
            if ($user['role'] == 'administrador') {
                header("Location: admin.php");
            } elseif ($user['role'] == 'gestor') {
                header("Location: gestor.php");
            } else {
                header("Location: usuario.php");
            }
            exit();
        } else {
            $error_message = "Senha incorreta";
        }
    } elseif ($managerExists) {
        $manager = $result_manager->fetch_assoc();
        if (password_verify($password, $manager['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $manager['id'];
            $_SESSION['role'] = 'gestor';

            if ($remember) {
                setcookie("email", $email, time() + (86400 * 30), "/");
                setcookie("password", $password, time() + (86400 * 30), "/");
            }

            header("Location: gestor.php");
            exit();
        } else {
            $error_message = "Senha incorreta";
        }
    } else {
        $error_message = "Email não cadastrado, favor procurar o administrador";
    }

    $stmt->close();
    $stmt_manager->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PDI Sistema</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
    <header class="bg-green-500 text-white p-4 w-full text-center">
        <h1 class="text-2xl">Login - PDI Sistema</h1>
    </header>
    <main class="p-4 w-full max-w-md">
        <section class="bg-white p-8 shadow rounded">
            <h2 class="text-xl mb-4">Entrar</h2>
            <?php if (isset($error_message)): ?>
                <p class="text-red-500 mb-4"><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
            <form method="POST" action="login.php">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="border p-2 w-full" value="<?= isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                    <input type="password" name="password" id="password" class="border p-2 w-full" value="<?= isset($_COOKIE['password']) ? htmlspecialchars($_COOKIE['password'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
                </div>
                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2" <?= isset($_COOKIE['email']) ? 'checked' : '' ?>>
                    <label for="remember" class="text-sm text-gray-700">Lembrar dados de acesso</label>
                </div>
                <button type="submit" class="bg-blue-500 text-white p-2 w-full">Entrar</button>
            </form>
            <button onclick="window.location.href='first_access.php'" class="bg-gray-500 text-white p-2 w-full mt-2">Primeiro Acesso</button>
            <button onclick="window.location.href='recover_access.php'" class="bg-gray-500 text-white p-2 w-full mt-2">Recuperar Acesso</button>
        </section>
    </main>
</body>
</html>
