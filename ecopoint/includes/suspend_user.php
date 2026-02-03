<?php
session_start();
include "includes/config.php";

if(isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "Super admin"]) && isset($_GET["uid"])) {
    $uid = intval($_GET["uid"]);
    
    // ตรวจสอบว่าไม่ใช่การระงับตัวเอง
    if($_SESSION["uid"] != $uid) {
        $stmt = $conn->prepare("UPDATE users SET u_role = ? WHERE uid = ?");
        $suspend_role = "suspend";
        $stmt->bind_param("si", $suspend_role, $uid);
        
        if($stmt->execute()) {
            echo "<script>alert('ระงับผู้ใช้งานสำเร็จ'); window.history.back();</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('ไม่สามารถระงับบัญชีตัวเองได้'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('คุณไม่มีสิทธิ์ในการดำเนินการนี้'); window.history.back();</script>";
}
?>