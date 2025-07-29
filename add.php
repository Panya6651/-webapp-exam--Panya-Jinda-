<?php
require 'db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $class = trim($_POST['class'] ?? '');

    // ตรวจสอบข้อมูล
    if ($student_id === '') $errors[] = 'กรุณากรอกรหัสนักเรียน';
    if ($full_name === '') $errors[] = 'กรุณากรอกชื่อ-นามสกุล';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'กรุณากรอกอีเมลให้ถูกต้อง';
    if ($class === '') $errors[] = 'กรุณากรอกห้องเรียน';

    // รูปภาพ
    $profile_image = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'รูปภาพต้องเป็นไฟล์ jpg, jpeg, png หรือ gif เท่านั้น';
        } else {
            $profile_image = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['profile_image']['tmp_name'], 'uploads/' . $profile_image);
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO students (student_id, full_name, email, class, profile_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$student_id, $full_name, $email, $class, $profile_image]);
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>เพิ่มนักเรียน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">
    <h1>เพิ่มนักเรียน</h1>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
        </div>
    <?php endif; ?>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>รหัสนักเรียน</label>
            <input type="text" name="student_id" class="form-control" required value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>ชื่อ-นามสกุล</label>
            <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>อีเมล</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>ห้องเรียน</label>
            <input type="text" name="class" class="form-control" required value="<?= htmlspecialchars($_POST['class'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label>รูปโปรไฟล์</label>
            <input type="file" name="profile_image" accept="image/*" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">บันทึก</button>
        <a href="index.php" class="btn btn-secondary">ยกเลิก</a>
    </form>
</body>
</html>
