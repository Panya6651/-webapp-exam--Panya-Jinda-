<?php
require 'db.php';

$stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>รายชื่อนักเรียน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">
    <h1>รายชื่อนักเรียน</h1>
    <a href="add.php" class="btn btn-primary mb-3">เพิ่มนักเรียน</a>
    <table class="table table-bordered">
        <thead class="table-dark text-center">
            <tr>
                <th>ลำดับ</th>
                <th>รูปโปรไฟล์</th>
                <th>รหัสนักเรียน</th>
                <th>ชื่อ-นามสกุล</th>
                <th>อีเมล</th>
                <th>ห้องเรียน</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$students): ?>
                <tr><td colspan="7" class="text-center">ไม่มีข้อมูล</td></tr>
            <?php else: foreach($students as $i => $student): ?>
                <tr>
                    <td class="text-center"><?= $i+1 ?></td>
                    <td class="text-center">
                        <?php if ($student['profile_image'] && file_exists('uploads/' . $student['profile_image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($student['profile_image']) ?>" alt="รูปโปรไฟล์" style="width:80px; height:auto; border-radius:50%;">
                        <?php else: ?>
                            ไม่มีรูป
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= htmlspecialchars($student['student_id']) ?></td>
                    <td><?= htmlspecialchars($student['full_name']) ?></td>
                    <td><?= htmlspecialchars($student['email']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($student['class']) ?></td>
                    <td class="text-center">
                        <a href="edit.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                        <a href="delete.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('ลบนักเรียนคนนี้?')">ลบ</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</body>
</html>
