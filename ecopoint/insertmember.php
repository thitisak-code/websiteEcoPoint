<?php
session_start();
include 'includes/config.php';

if(isset($_SESSION['username']) && in_array($_SESSION['role'],['Super admin'])){
    $insert = $conn->prepare("INSERT INTO users (firstname, lastname, username, password, phone, email, image, u_total_point, u_role, u_deta) VALUES
('สมชาย', 'ใจดี', 'somchai001', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0812345678', 'somchai001@email.com', 'profile001.jpg', 150, 'member', '2026-01-30 09:00:00'),
('สุนิสา', 'ศรีสุข', 'sunisa002', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0898765432', 'sunisa002@email.com', 'profile002.jpg', 230, 'member', '2026-01-30 09:01:00'),
('กมล', 'รัตนะ', 'kamon003', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0823456789', 'kamon003@email.com', 'profile003.jpg', 75, 'member', '2026-01-30 09:02:00'),
('ธนวัฒน์', 'ทองคำ', 'thanawat004', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0834567890', 'thanawat004@email.com', 'profile004.jpg', 420, 'member', '2026-01-30 09:03:00'),
('เพชร', 'วิเศษ', 'petch005', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0845678901', 'petch005@email.com', 'profile005.jpg', 95, 'member', '2026-01-30 09:04:00'),
('อรุณี', 'แสงจันทร์', 'arunee006', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0856789012', 'arunee006@email.com', 'profile006.jpg', 310, 'member', '2026-01-30 09:05:00'),
('วีระ', 'มหาชัย', 'weera007', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0867890123', 'weera007@email.com', 'profile007.jpg', 180, 'member', '2026-01-30 09:06:00'),
('วรรณา', 'ดอกบัว', 'wanna008', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0878901234', 'wanna008@email.com', 'profile008.jpg', 260, 'member', '2026-01-30 09:07:00'),
('อนุชา', 'ภูเขา', 'anucha009', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0889012345', 'anucha009@email.com', 'profile009.jpg', 50, 'member', '2026-01-30 09:08:00'),
('พิมพา', 'มณีรัตน์', 'pimpa010', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0890123456', 'pimpa010@email.com', 'profile010.jpg', 430, 'member', '2026-01-30 09:09:00'),
('ชัยวัฒน์', 'สุวรรณ', 'chaiwat011', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0901234567', 'chaiwat011@email.com', 'profile011.jpg', 120, 'member', '2026-01-30 09:10:00'),
('กัญญา', 'รัตนมณี', 'kanya012', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0912345678', 'kanya012@email.com', 'profile012.jpg', 340, 'member', '2026-01-30 09:11:00'),
('อานนท์', 'ศิริพร', 'anon013', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0923456789', 'anon013@email.com', 'profile013.jpg', 85, 'member', '2026-01-30 09:12:00'),
('รุ่งรวี', 'แก้วมรกต', 'rungrawee014', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0934567890', 'rungrawee014@email.com', 'profile014.jpg', 270, 'member', '2026-01-30 09:13:00'),
('วัฒนา', 'สุขเกษม', 'watthana015', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0945678901', 'watthana015@email.com', 'profile015.jpg', 190, 'member', '2026-01-30 09:14:00'),
('เบญจมาศ', 'ไพศาล', 'benjamat016', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0956789012', 'benjamat016@email.com', 'profile016.jpg', 380, 'member', '2026-01-30 09:15:00'),
('สมศักดิ์', 'ทองแท้', 'somsak017', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0967890123', 'somsak017@email.com', 'profile017.jpg', 65, 'member', '2026-01-30 09:16:00'),
('กฤติยา', 'ทรัพย์สิน', 'krittiya018', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0978901234', 'krittiya018@email.com', 'profile018.jpg', 410, 'member', '2026-01-30 09:17:00'),
('ประเสริฐ', 'ศรีเมือง', 'prasert019', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0989012345', 'prasert019@email.com', 'profile019.jpg', 140, 'member', '2026-01-30 09:18:00'),
('มณฑา', 'สุขสม', 'monthaa020', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0990123456', 'monthaa020@email.com', 'profile020.jpg', 320, 'member', '2026-01-30 09:19:00'),
('วิทยา', 'มงคล', 'wittaya021', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0811122333', 'wittaya021@email.com', 'profile021.jpg', 210, 'member', '2026-01-30 09:20:00'),
('ศิริลักษณ์', 'แก้วคำ', 'sirilak022', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0822233444', 'sirilak022@email.com', 'profile022.jpg', 90, 'member', '2026-01-30 09:21:00'),
('ณัฐพล', 'เทพสถิต', 'nattapol023', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0833344555', 'nattapol023@email.com', 'profile023.jpg', 360, 'member', '2026-01-30 09:22:00'),
('อรวรรณ', 'สิงห์ทอง', 'orawan024', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0844455666', 'orawan024@email.com', 'profile024.jpg', 125, 'member', '2026-01-30 09:23:00'),
('ธีรภัทร', 'วิไล', 'theerapat025', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0855566777', 'theerapat025@email.com', 'profile025.jpg', 280, 'member', '2026-01-30 09:24:00'),
('กัลยาณี', 'เพชรรัตน์', 'kalyani026', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0866677888', 'kalyani026@email.com', 'profile026.jpg', 195, 'member', '2026-01-30 09:25:00'),
('ยุทธนา', 'ศรีธน', 'yuttana027', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0877788999', 'yuttana027@email.com', 'profile027.jpg', 440, 'member', '2026-01-30 09:26:00'),
('วิภาวดี', 'รัตนพร', 'wiphawadee028', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0888899000', 'wiphawadee028@email.com', 'profile028.jpg', 110, 'member', '2026-01-30 09:27:00'),
('ภานุวัฒน์', 'แสงสุรีย์', 'panuwat029', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0899900111', 'panuwat029@email.com', 'profile029.jpg', 370, 'member', '2026-01-30 09:28:00'),
('สาวิตรี', 'ทองสุข', 'sawitree030', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0900011222', 'sawitree030@email.com', 'profile030.jpg', 155, 'member', '2026-01-30 09:29:00'),
('อัครพล', 'มงคลศิลป์', 'akkharaphol031', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0911122333', 'akkharaphol031@email.com', 'profile031.jpg', 225, 'member', '2026-01-30 09:30:00'),
('กนกวรรณ', 'ศรีประเสริฐ', 'kanokwan032', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0922233444', 'kanokwan032@email.com', 'profile032.jpg', 395, 'member', '2026-01-30 09:31:00'),
('ไพศาล', 'สุขเกษม', 'paisan033', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0933344555', 'paisan033@email.com', 'profile033.jpg', 70, 'member', '2026-01-30 09:32:00'),
('รัตนา', 'ทองแท้', 'rattana034', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0944455666', 'rattana034@email.com', 'profile034.jpg', 305, 'member', '2026-01-30 09:33:00'),
('ศุภกฤต', 'มณีรัตน์', 'suppakit035', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0955566777', 'suppakit035@email.com', 'profile035.jpg', 180, 'member', '2026-01-30 09:34:00'),
('กาญจนา', 'ศรีเมือง', 'kanchana036', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0966677888', 'kanchana036@email.com', 'profile036.jpg', 250, 'member', '2026-01-30 09:35:00'),
('ธีระ', 'สุขสม', 'theera037', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0977788999', 'theera037@email.com', 'profile037.jpg', 420, 'member', '2026-01-30 09:36:00'),
('วิไล', 'แก้วมรกต', 'wilai038', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0988899000', 'wilai038@email.com', 'profile038.jpg', 135, 'member', '2026-01-30 09:37:00'),
('ณัฐวุฒิ', 'ทรัพย์สิน', 'nattawut039', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0999900111', 'nattawut039@email.com', 'profile039.jpg', 290, 'member', '2026-01-30 09:38:00'),
('สุวรรณา', 'ไพศาล', 'suwanna040', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0810011222', 'suwanna040@email.com', 'profile040.jpg', 165, 'member', '2026-01-30 09:39:00'),
('ปรีชา', 'เทพสถิต', 'preecha041', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0820022333', 'preecha041@email.com', 'profile041.jpg', 380, 'member', '2026-01-30 09:40:00'),
('อัจฉรา', 'รัตนะ', 'atchara042', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0830033444', 'atchara042@email.com', 'profile042.jpg', 95, 'member', '2026-01-30 09:41:00'),
('อนุรักษ์', 'ศรีธน', 'anurak043', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0840044555', 'anurak043@email.com', 'profile043.jpg', 330, 'member', '2026-01-30 09:42:00'),
('กมลวรรณ', 'แสงจันทร์', 'kamolwan044', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6Sabcdefghijkl', '0850055666', 'kamolwan044@email.com', 'profile044.jpg', 205, 'member', '2026-01-30 09:43:00'),
('วีรศักดิ์', 'ทองสุข', 'weerasak045', '$2y$10$oe84D0pDmeZpsUOf/WFX6en59UeULRx.Kzui/VYInwUklmnopqrstuv', '0860066777', 'weerasak045@email.com', 'profile045.jpg', 270, 'member', '2026-01-30 09:44:00'),
('ธิดา', 'มงคล', 'thida046', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEEckkWolX69rxgTewxyz0123456', '0870077888', 'thida046@email.com', 'profile046.jpg', 145, 'member', '2026-01-30 09:45:00'),
('อภิชาติ', 'ศรีประเสริฐ', 'apichat047', '$2y$10$0ZtDDfcsx0S77Ju/abGa6SMCwuJvdQZTZmz.5EP0GCabcdefghijklm', '0880088999', 'apichat047@email.com', 'profile047.jpg', 410, 'member', '2026-01-30 09:46:00'),
('รัชนี', 'สุขเกษม', 'ratanee048', '$2y$10$erqGtobno872McqhVL29OQ1MEnJ4bhMP84RAtwR4DCnopqrstuvwxy', '0890099000', 'ratanee048@email.com', 'profile048.jpg', 175, 'member', '2026-01-30 09:47:00'),
('ศักดิ์สิทธิ์', 'ทองแท้', 'saksit049', '$2y$10$cWmNhwdaVGNf4WJR5uCzuu9CZHQT5IVFeaQTIpc0QK0z123456789ab', '0900100111', 'saksit049@email.com', 'profile049.jpg', 295, 'member', '2026-01-30 09:48:00'),
('กนิษฐา', 'มณีรัตน์', 'kanitha050', '$2y$10$9HUyqAc9l7Ce0xoDDoMqm.FWINBPrNt/9OT7zadb9WxYZ123456789', '0910111222', 'kanitha050@email.com', 'profile050.jpg', 235, 'member', '2026-01-30 09:49:00');");

if($insert->execute()){
    echo '<script>alert("success"); window.location="index.php?page=home";</script>';
    exit();
}
}
?>