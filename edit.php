<?php
require 'db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $class = trim($_POST['class'] ?? '');
    $profile_image = $student['profile_image'];

    // Validate
    if ($student_id === '') $errors[] = 'กรุณากรอกรหัสนักเรียน';
    if ($full_name === '') $errors[] = 'กรุณากรอกชื่อ-นามสกุล';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'กรุณากรอกอีเมลให้ถูกต้อง';
    if ($class === '') $errors[] = 'กรุณากรอกห้องเรียน';

    // อัปโหลดรูปถ้ามี
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            $errors[] = 'รองรับเฉพาะไฟล์รูปภาพ jpg, jpeg, png, gif เท่านั้น';
        } else {
            // ลบรูปเก่าถ้ามี
            if ($profile_image && file_exists('uploads/' . $profile_image)) {
                unlink('uploads/' . $profile_image);
            }
            $profile_image = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['profile_image']['tmp_name'], 'uploads/' . $profile_image);
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare("UPDATE students SET student_id = ?, full_name = ?, email = ?, class = ?, profile_image = ? WHERE id = ?");
        $stmt->execute([$student_id, $full_name, $email, $class, $profile_image, $id]);
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>แก้ไขข้อมูลนักเรียน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">
    <h1>แก้ไขข้อมูลนักเรียน</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label>รหัสนักเรียน</label>
            <input type="text" name="student_id" class="form-control" required value="<?= htmlspecialchars($_POST['student_id'] ?? $student['student_id']) ?>">
        </div>
        <div class="mb-3">
            <label>ชื่อ-นามสกุล</label>
            <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($_POST['full_name'] ?? $student['full_name']) ?>">
        </div>
        <div class="mb-3">
            <label>อีเมล</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? $student['email']) ?>">
        </div>
        <div class="mb-3">
            <label>ห้องเรียน</label>
            <input type="text" name="class" class="form-control" required value="<?= htmlspecialchars($_POST['class'] ?? $student['class']) ?>">
        </div>
        <div class="mb-3">
            <label>รูปโปรไฟล์</label><br>
            <?php if ($student['profile_image'] && file_exists('uploads/' . $student['profile_image'])): ?>
                <img src="uploads/<?= htmlspecialchars($student['profile_image']) ?>" alt="รูปโปรไฟล์" width="100" style="border-radius:50%; margin-bottom: 10px;">
            <?php else: ?>
                ไม่มีรูป
            <?php endif; ?>
            <input type="file" name="profile_image" accept="image/*" class="form-control mt-2">
            <small class="text-muted">ถ้าไม่เลือกจะใช้รูปเดิม</small>
        </div>
        <button type="submit" class="btn btn-success">บันทึกการแก้ไข</button>
        <a href="index.php" class="btn btn-secondary">ยกเลิก</a>
    </form>
</body>
</html>
