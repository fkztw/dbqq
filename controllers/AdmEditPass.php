<?php
session_start();
    require_once('../controllers/Session.php');
    require_once('../models/User.php');

    $id = $_POST['account'];
    $new_passwd = $_POST['new_passwd'];
    $confirm_passwd = $_POST['confirm_passwd'];
    $perm = $_POST['perm'];

    // you're not a administrator, go away!
    if ($_SESSION['perm'] !== 'adm') {
        CheckPermAndRedirect($_SESSION['perm'], 'adm');
    }
    
    require_once('../components/Mysqli.php');
    $errmsg = '';
    if (($perm == 'stu') and (!CheckStudentIDExist($id)))
        $errmsg = "Student id: $id not found.";
    elseif (($perm == 'pro') and (!CheckProfessorIDExist($id)))
        $errmsg = "Professor id: $id not found.";
    elseif (!CheckPasswd($new_passwd))
       $errmsg = 'You have to enter your new password.';
    elseif (!CheckPasswd($confirm_passwd))
       $errmsg = 'You have to enter your password confirm.';
    elseif ($new_passwd !== $confirm_passwd)
        $errmsg = 'two passwords not match';
    elseif(strlen($new_passwd) > 10)
        $errmsg = '密碼最大長度為10碼';
    else {
        require_once('../components/utility.php');
        $new_passwd = salted($new_passwd);

        // update the data in the database
        $link = MysqliConnection('Write');

        if ($perm == 'stu')
            $query = 'UPDATE Student SET Password = SHA1(?) WHERE ID = ?';
        elseif ($perm == 'pro')
            $query = 'UPDATE Professor SET Password = SHA1(?) WHERE ID = ?';
        else {
            $errmsg = '權限有誤';
            $_SESSION['errmsg'] = $errmsg;
            CheckPermAndRedirect($_SESSION['perm'], "adm");
        }
            
        $stmt = mysqli_stmt_init($link);
        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, "ss", $new_passwd, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
        $errmsg = '您已成功修改密碼';
    }

    $_SESSION['errmsg'] = $errmsg;
    RedirectByPerm($_SESSION['perm']);
?>
