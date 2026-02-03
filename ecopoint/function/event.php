<?php
session_start();
include '../includes/config.php';

$uid = $_SESSION['uid'];
$ac_id = $_GET['ac_id'];
$point = $_GET['point'];

$check = $conn->prepare("SELECT * FROM record_ac WHERE uid = ? AND ac_id = ?");
$check->bind_param("ii", $uid, $ac_id);
$check->execute();
$result = $check->get_result();

if($result->num_rows > 0){
    echo "<script>
    alert('⚠️คุณเข้าร่วมกิจกรรมนี้แล้ว');
    window.location='../index.php?page=home';
    </script>";
    exit();
}

$stmt = $conn->prepare("INSERT INTO record_ac(ac_id, uid, ac_point) VALUES(?,?,?)");
$stmt->bind_param("iii", $ac_id, $uid, $point);
$stmt->execute();


$stmt1 = $conn->prepare("UPDATE activity SET ac_limit = ac_limit - 1 WHERE ac_id = ?");
$stmt1->bind_param("i", $ac_id);
if($stmt1->execute()){
    echo "<script>
    alert('ขอบคุณสำหรับการเข้าร่วมกิจกรรม✅รอการตอบรับจากผู้ดูแล');
    window.location='../index.php?page=home';
    </script>";
    exit();
}