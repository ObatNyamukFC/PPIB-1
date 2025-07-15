<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['dokumen']) && $_SESSION['role'] === 'admin') {
    $file = $_FILES['dokumen'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = basename($file['name']);
        $target = __DIR__ . '/../files/' . $filename;
        if (file_exists($target)) {
            $filename = time() . '-' . $filename;
            $target = __DIR__ . '/../files/' . $filename;
        }
        if (move_uploaded_file($file['tmp_name'], $target)) {
            $fileurl = '../files/' . $filename;
            $userid = $_SESSION['user_id'];
            $koneksi->query("INSERT INTO files (filename, filepath, uploaded_by) VALUES ('$filename', '$fileurl', $userid)");
            $message = "File berhasil diupload.";
        } else {
            $message = "Gagal upload file.";
        }
    } else {
        $message = "Terjadi kesalahan upload.";
    }
}
$files = $koneksi->query("SELECT f.filename, f.filepath, f.uploaded_at, u.username FROM files f LEFT JOIN users u ON f.uploaded_by = u.id ORDER BY f.uploaded_at DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dokumen Publik</title>
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
    <main class="max-w-4xl mx-auto p-6 bg-white rounded-xl shadow-card mt-10 mb-20">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-primary">Dokumen Publik</h1>
            <div class="text-sm text-gray-700">
                Halo, <span class="font-semibold"><?= htmlspecialchars($_SESSION['username']) ?></span> (<?= htmlspecialchars($_SESSION['role']) ?>)
                <a href="logout.php" class="ml-4 text-red-600 hover:underline">Logout</a>
            </div>
        </div>
        <?php if ($message): ?>
            <div class="mb-4 text-green-600 font-semibold"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <form action="dokumen.php" method="post" enctype="multipart/form-data" class="mb-8 flex flex-col md:flex-row items-center gap-4">
                <input type="file" name="dokumen" required class="border rounded px-3 py-2" />
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-800 transition">Upload</button>
            </form>
        <?php endif; ?>
        <table class="min-w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2 text-left">Nama File</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Diunggah Oleh</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Tanggal Upload</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">Unduh</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($files->num_rows): ?>
                    <?php while ($file = $files->fetch_assoc()): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($file['filename']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($file['username'] ?? 'Unknown') ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($file['uploaded_at']) ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <a href="<?= htmlspecialchars($file['filepath']) ?>" target="_blank" class="text-blue-600 hover:underline">Download</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="border border-gray-300 px-4 py-2 text-center text-gray-500">Belum ada dokumen.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>