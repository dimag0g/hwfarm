<!-- HW Farm project (c) Dmitry Grigoryev, 2020 -->
<!-- Released under the terms of GNU AGPLv3 -->
<?php
if(isset($_GET['sid'])) $sid = $_GET['sid'];
if(isset($_POST['sid'])) $sid = $_POST['sid'];
$sid = escapeshellarg($sid);

if(isset($sid)) {
    session_id($sid);
    session_start();
#$sid = session_id();

    $_SESSION['board'] = shell_exec("schroot -c hwfarm -d / -- readlink /sessions/$sid/Makefile | cut -d/ -f3 | tr -d '\n'");
    $_SESSION['sketch'] = shell_exec("schroot -c hwfarm -d / -- cat /sessions/$sid/sketch.ino");
    $_SESSION['input'] = shell_exec("schroot -c hwfarm -d / -- cat /sessions/$sid/input.exp");

    if(empty($_SESSION['board'])) {sleep(3); header("Location: select.php");}
    else if(empty($_SESSION['sketch'])) header("Location: sketch.php");
    else if(empty($_SESSION['input'])) header("Location: build.php");
    else header("Location: run.php");
} else {
    header("Location: select.php");
}
?>

