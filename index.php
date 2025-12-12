<?php
// 如果已安装，跳到后台；否则跳到安装页
if (file_exists('config/db.php')) {
    header("Location: admin/index.php");
} else {
    header("Location: install.php");
}
exit;
