<?php
session_start();
include "includes/config.php";

if(isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "Super admin"]) && isset($_GET["uid"])) {
    $uid = intval($_GET["uid"]);
    
    $stmt = $conn->prepare("UPDATE users SET u_role = ? WHERE uid = ?");
    $member_role = "member";
    $stmt->bind_param("si", $member_role, $uid);
    
    if($stmt->execute()) {
        echo "<script>alert('กู้คืนผู้ใช้งานสำเร็จ'); window.history.back();</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('คุณไม่มีสิทธิ์ในการดำเนินการนี้'); window.history.back();</script>";
}
?>