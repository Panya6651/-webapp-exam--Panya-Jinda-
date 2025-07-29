<?php
require 'db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// หาไฟล์รูปโปรไฟล์ก่อนลบข้อมูล
$stmt = $pdo->prepare("SELECT profile_image FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($student) {
    if ($student['profile_image'] && file_exists('uploads/' . $student['profile_image'])) {
        unlink('uploads/' . $student['profile_image']);
    }
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: index.php');
exit;
?>
