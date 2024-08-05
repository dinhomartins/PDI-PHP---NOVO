<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

include 'api/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $new_password = bin2hex(random_bytes(4)); // Gera uma nova senha aleatória
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $sql_update = "UPDATE users SET password='$hashed_password', password_set=1 WHERE email='$email'";
        if ($conn->query($sql_update) === TRUE) {
            // Enviar email com a nova senha
            $mail = new PHPMailer(true);
            try {
                // Configurações do servidor SMTP
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP do Gmail
                $mail->SMTPAuth   = true;
                $mail->Username   = 'seu_email@gmail.com'; // Seu endereço de email do Gmail
                $mail->Password   = 'sua_senha'; // Sua senha do Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Configurações do email
                $mail->setFrom('seu_email@gmail.com', 'Sistema PDI');
                $mail->addAddress($email); // Email do usuário
                $mail->Subject = 'Recuperação de Senha';
                $mail->Body    = "Sua nova senha é: $new_password";

                $mail->send();
                $success = "Senha enviada para o email cadastrado.";
            } catch (Exception $e) {
                $error = "Erro ao enviar o email. Erro do PHPMailer: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Erro ao redefinir a senha. Tente novamente.";
        }
    } else {
        $error = "Email não cadastrado no sistema. Entre em contato com o administrador.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Acesso</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.2.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-sm">
        <h2 class="text-2xl mb-4 text-center">Recuperar Acesso</h2>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="text-green-500 mb-4"><?= htmlspecialchars($success) ?></p>
            <p class="mt-4 text-center">
                <a href="login.php" class="bg-blue-500 text-white p-2 rounded">Voltar para Login</a>
            </p>
        <?php else: ?>
            <form method="POST" action="recover_access.php">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-bold mb-2">Email:</label>
                    <input type="email" id="email" name="email" class="border p-2 w-full" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white p-2 w-full rounded">Recuperar Senha</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
