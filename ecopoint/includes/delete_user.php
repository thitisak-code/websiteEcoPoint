<?php
session_start();
include "includes/config.php";

if(isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "Super admin"]) && isset($_GET["uid"])) {
    $uid = intval($_GET["uid"]);
    
    // ตรวจสอบก่อนว่าผู้ใช้นี้ไม่ใช่แอดมิน
    $checkStmt = $conn->prepare("SELECT u_role FROM users WHERE uid = ?");
    $checkStmt->bind_param("i", $uid);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if(!in_array($user["u_role"], ["admin", "Super admin"])) {
            $deleteStmt = $conn->prepare("DELETE FROM users WHERE uid = ?");
            $deleteStmt->bind_param("i", $uid);
            
            if($deleteStmt->execute()) {
                echo "<script>alert('ลบผู้ใช้งานสำเร็จ'); window.history.back();</script>";
            } else {
                echo "<script>alert('เกิดข้อผิดพลาด'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('ไม่สามารถลบผู้ดูแลระบบได้'); window.history.back();</script>";
        }
    }
} else {
    echo "<script>alert('คุณไม่มีสิทธิ์ในการดำเนินการนี้'); window.history.back();</script>";
}
?>