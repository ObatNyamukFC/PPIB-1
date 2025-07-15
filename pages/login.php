<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare statement untuk keamanan
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: dokumen.php');
        exit;
    } else {
        $message = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif']
                    },
                    colors: {
                        primary: '#1E3A8A',
                        secondary: '#047857',
                        accent: '#F59E0B'
                    },
                    boxShadow: {
                        'card': '0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06)'
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
</head>

<body class="bg-gray-50 font-sans text-gray-800">
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <main class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-xl shadow-card w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center text-primary">Login</h2>
            <?php if ($message): ?>
                <div class="mb-4 text-red-600"><?= $message ?></div>
            <?php endif; ?>
            <form method="post" class="space-y-4">
                <input type="text" name="username" placeholder="Username" required class="w-full border border-gray-300 rounded px-3 py-2" />
                <input type="password" name="password" placeholder="Password" required class="w-full border border-gray-300 rounded px-3 py-2" />
                <button type="submit" class="w-full bg-primary text-white py-2 rounded hover:bg-blue-800 transition">Login</button>
            </form>
            <p class="mt-4 text-center text-sm">
                Belum punya akun? <a href="register.php" class="text-blue-600 hover:underline">Daftar di sini</a>
            </p>
        </div>
    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>