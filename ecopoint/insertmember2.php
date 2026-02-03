<?php
session_start();
include 'includes/config.php';

if(isset($_SESSION['username']) && in_array($_SESSION['role'],['Super admin'])){
    $insert = $conn->prepare("INSERT INTO users (firstname, lastname, username, password, phone, email, image, u_total_point, u_role, u_deta) VALUES
('พงศกร', 'ใจกว้าง', 'pongsakorn051', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0920222333', 'pongsakorn051@email.com', 'profile051.jpg', 185, 'member', '2026-01-30 09:50:00'),
('วิมล', 'ศรีชัย', 'wimon052', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0930333444', 'wimon052@email.com', 'profile052.jpg', 350, 'member', '2026-01-30 09:51:00'),
('เกียรติศักดิ์', 'ทองประเสริฐ', 'kiattisak053', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0940444555', 'kiattisak053@email.com', 'profile053.jpg', 115, 'member', '2026-01-30 09:52:00'),
('ธัญลักษณ์', 'รัศมี', 'thanyalak054', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0950555666', 'thanyalak054@email.com', 'profile054.jpg', 265, 'member', '2026-01-30 09:53:00'),
('ณัฏฐ์', 'วิสุทธิ์', 'nat055', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0960666777', 'nat055@email.com', 'profile055.jpg', 400, 'member', '2026-01-30 09:54:00'),
('อำพร', 'สุขสำราญ', 'amporn056', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0970777888', 'amporn056@email.com', 'profile056.jpg', 155, 'member', '2026-01-30 09:55:00'),
('อนุสรณ์', 'ธรรมรงค์', 'anusorn057', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0980888999', 'anusorn057@email.com', 'profile057.jpg', 220, 'member', '2026-01-30 09:56:00'),
('กัลยกร', 'เพชรประดับ', 'kanyakorn058', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0990999000', 'kanyakorn058@email.com', 'profile058.jpg', 335, 'member', '2026-01-30 09:57:00'),
('วีรยุทธ', 'ศรีสวัสดิ์', 'weerayut059', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0811000111', 'weerayut059@email.com', 'profile059.jpg', 80, 'member', '2026-01-30 09:58:00'),
('อรทัย', 'ทองมาก', 'orthai060', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0821111222', 'orthai060@email.com', 'profile060.jpg', 285, 'member', '2026-01-30 09:59:00'),
('ศรัณย์', 'แก้วใส', 'saran061', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0831222333', 'saran061@email.com', 'profile061.jpg', 170, 'member', '2026-01-30 10:00:00'),
('ณัฐริกา', 'สุขศรี', 'nattarika062', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0841333444', 'nattarika062@email.com', 'profile062.jpg', 390, 'member', '2026-01-30 10:01:00'),
('ปิยะ', 'มงคลกุล', 'piya063', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0851444555', 'piya063@email.com', 'profile063.jpg', 125, 'member', '2026-01-30 10:02:00'),
('สุกัญญา', 'รัตนประทีป', 'sukanya064', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0861555666', 'sukanya064@email.com', 'profile064.jpg', 310, 'member', '2026-01-30 10:03:00'),
('ธนกฤต', 'ศรีธัญญา', 'thanakrit065', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0871666777', 'thanakrit065@email.com', 'profile065.jpg', 245, 'member', '2026-01-30 10:04:00'),
('อัมพร', 'วิเชียร', 'amporn066', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0881777888', 'amporn066@email.com', 'profile066.jpg', 175, 'member', '2026-01-30 10:05:00'),
('รพี', 'ทองสุทธิ์', 'rapee067', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0891888999', 'rapee067@email.com', 'profile067.jpg', 420, 'member', '2026-01-30 10:06:00'),
('กฤตยา', 'ศรีโสภณ', 'krittaya068', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0901999000', 'krittaya068@email.com', 'profile068.jpg', 95, 'member', '2026-01-30 10:07:00'),
('สราวุธ', 'เกษมสันต์', 'sarawut069', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0912000111', 'sarawut069@email.com', 'profile069.jpg', 360, 'member', '2026-01-30 10:08:00'),
('อรอนงค์', 'รัตนวรรณ', 'on-anong070', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0922111222', 'on-anong070@email.com', 'profile070.jpg', 200, 'member', '2026-01-30 10:09:00'),
('ธีรพงศ์', 'ศรีประดิษฐ์', 'theeraphong071', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0932222333', 'theeraphong071@email.com', 'profile071.jpg', 275, 'member', '2026-01-30 10:10:00'),
('วรรณพร', 'ทองแท้จริง', 'wannaporn072', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0942333444', 'wannaporn072@email.com', 'profile072.jpg', 140, 'member', '2026-01-30 10:11:00'),
('วิศรุต', 'แก้วกัลยา', 'wisarut073', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0952444555', 'wisarut073@email.com', 'profile073.jpg', 380, 'member', '2026-01-30 10:12:00'),
('อรุณ', 'ศรีมาลา', 'arun074', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0962555666', 'arun074@email.com', 'profile074.jpg', 215, 'member', '2026-01-30 10:13:00'),
('กฤษณ์', 'รัตนไชย', 'krit075', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0972666777', 'krit075@email.com', 'profile075.jpg', 325, 'member', '2026-01-30 10:14:00'),
('วิไลลักษณ์', 'สุขเกษมสุข', 'wilailak076', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0982777888', 'wilailak076@email.com', 'profile076.jpg', 105, 'member', '2026-01-30 10:15:00'),
('พีรพล', 'ทองประทีป', 'peerapol077', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0992888999', 'peerapol077@email.com', 'profile077.jpg', 290, 'member', '2026-01-30 10:16:00'),
('สิรินาถ', 'ศรีวิไล', 'sirinat078', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0812999000', 'sirinat078@email.com', 'profile078.jpg', 185, 'member', '2026-01-30 10:17:00'),
('อธิษฐ์', 'รัตนพล', 'athit079', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0823000111', 'athit079@email.com', 'profile079.jpg', 410, 'member', '2026-01-30 10:18:00'),
('กาญจนี', 'ทองสุขสม', 'kanchanee080', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0833111222', 'kanchanee080@email.com', 'profile080.jpg', 160, 'member', '2026-01-30 10:19:00'),
('ณรงค์', 'ศรีสุนทร', 'narong081', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0843222333', 'narong081@email.com', 'profile081.jpg', 235, 'member', '2026-01-30 10:20:00'),
('อรสา', 'รัตนไพบูลย์', 'ornsra082', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0853333444', 'ornsra082@email.com', 'profile082.jpg', 345, 'member', '2026-01-30 10:21:00'),
('วีระศักดิ์', 'แก้ววิเชียร', 'weerasak083', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0863444555', 'weerasak083@email.com', 'profile083.jpg', 120, 'member', '2026-01-30 10:22:00'),
('ภัทรวดี', 'สุขเกษมศรี', 'pattrawadee084', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0873555666', 'pattrawadee084@email.com', 'profile084.jpg', 280, 'member', '2026-01-30 10:23:00'),
('ศุภชัย', 'ทองโอสถ', 'suppachai085', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0883666777', 'suppachai085@email.com', 'profile085.jpg', 195, 'member', '2026-01-30 10:24:00'),
('อัญชลี', 'ศรีประภา', 'anchalee086', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0893777888', 'anchalee086@email.com', 'profile086.jpg', 375, 'member', '2026-01-30 10:25:00'),
('ธนากร', 'รัตนศรี', 'thanakorn087', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0903888999', 'thanakorn087@email.com', 'profile087.jpg', 145, 'member', '2026-01-30 10:26:00'),
('อริสรา', 'ทองประดิษฐ์', 'arisra088', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0913999000', 'arisra088@email.com', 'profile088.jpg', 260, 'member', '2026-01-30 10:27:00'),
('ณัชชา', 'ศรีสุขสันต์', 'natcha089', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0924000111', 'natcha089@email.com', 'profile089.jpg', 330, 'member', '2026-01-30 10:28:00'),
('พงษ์ศักดิ์', 'แก้วมณี', 'pongsak090', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0934111222', 'pongsak090@email.com', 'profile090.jpg', 210, 'member', '2026-01-30 10:29:00'),
('อรุณศรี', 'รัตนากร', 'arunsri091', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0944222333', 'arunsri091@email.com', 'profile091.jpg', 395, 'member', '2026-01-30 10:30:00'),
('กิตติพงษ์', 'ทองสุวรรณ', 'kittipong092', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0954333444', 'kittipong092@email.com', 'profile092.jpg', 180, 'member', '2026-01-30 10:31:00'),
('วาสนา', 'ศรีธาดา', 'wasana093', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0964444555', 'wasana093@email.com', 'profile093.jpg', 305, 'member', '2026-01-30 10:32:00'),
('อำนาจ', 'รัตนไชยวงศ์', 'amnat094', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0974555666', 'amnat094@email.com', 'profile094.jpg', 135, 'member', '2026-01-30 10:33:00'),
('อุไรวรรณ', 'ทองวัฒนะ', 'uraiwan095', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0984666777', 'uraiwan095@email.com', 'profile095.jpg', 250, 'member', '2026-01-30 10:34:00'),
('ศิริพงษ์', 'แก้ววิเศษ', 'siripong096', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0994777888', 'siripong096@email.com', 'profile096.jpg', 370, 'member', '2026-01-30 10:35:00'),
('กนกพร', 'รัตนศรีวิไล', 'kanokporn097', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0814888999', 'kanokporn097@email.com', 'profile097.jpg', 115, 'member', '2026-01-30 10:36:00'),
('อนุพงษ์', 'ทองประเสริฐสุด', 'anupong098', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0824999000', 'anupong098@email.com', 'profile098.jpg', 290, 'member', '2026-01-30 10:37:00'),
('สุธาสินี', 'ศรีสมบัติ', 'suthasinee099', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0835000111', 'suthasinee099@email.com', 'profile099.jpg', 225, 'member', '2026-01-30 10:38:00'),
('จักรกฤษณ์', 'รัตนเมธี', 'jakkrit100', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0845111222', 'jakkrit100@email.com', 'profile100.jpg', 350, 'member', '2026-01-30 10:39:00');");

if($insert->execute()){
    echo '<script>alert("success"); window.location="index.php?page=home";</script>';
    exit();
}
}
?>