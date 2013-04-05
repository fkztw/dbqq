<?php
session_start();
    require_once('../controllers/Session.php');
    require_once('../components/Mysqli.php');

    $id = $_SESSION['id'];
    $name = $_POST['name'];

    // you're not a professor!!!
    if ($_SESSION['perm'] != 'pro') {
        CheckPermAndRedirect($_SESSION['id'], "pro");
    }

    // update the data in the database
    $link = MysqliConnection('Write');
    $query = 'UPDATE Professor SET Name = ? WHERE ID = ?';
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, "ss", $name, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
    CheckPermAndRedirect($_SESSION['id'], "stu");
?>