<?php
session_start();

if(!isset($_SESSION['auth'])){
    session_unset();
    header("Location: ../index.php");
}


$oldPassword = $_REQUEST['oldPassword'];
$password = $_REQUEST['password'];
$confirmPassword = $_REQUEST['confirmPassword'];
$oldEncPassword = $_SESSION['auth']['password'];
$errors  = [];






//* VALIDATIONS


if(empty($oldPassword)){
    $errors['old_error'] = "Please enter your old psk";
} else if(!password_verify($oldPassword, $oldEncPassword)){
    $errors['old_error'] = "Old password did not match!";

}

//* PASSWORD VALIDATION
if(empty($password)){
    $errors['password_error'] = 'Password is missing!';
} else if (strlen($password) < 8){
    $errors['password_error'] = 'Password should be greater or equal to 8 char!';
} else if($password !== $confirmPassword){
    $errors['password_error'] = 'Password and Confirm Password do not match!';
}



if(count($errors) > 0){
    $_SESSION['errors'] = $errors;
    header("Location: ../dashboard/profile.php");
} else {
    //* UPDATE PASSWORD
    include "../database/env.php";
    $id = $_SESSION['auth']['id'];
    $encPassword = password_hash($password, PASSWORD_BCRYPT);
    $query = "UPDATE users SET password='$encPassword' WHERE id='$id'";
    $res = mysqli_query($conn, $query);
   
    if($res){
        $_SESSION['auth']['password'] = $encPassword;
        $_SESSION['success'] = true;
        header("Location: ../dashboard/profile.php");
    }
}


