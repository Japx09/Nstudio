<?php

function hash_password($passw)
{
    $options = [
        'cost' => 12
    ];
    $hashedPassword = password_hash($passw, PASSWORD_BCRYPT, $options);

    return $hashedPassword;
}

function loginAuth($username, $password)
{
    require "connection.php";

    $sql = "SELECT * FROM site_user WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row["password"])) {
            $_SESSION['id'] = $row['id'];
            return true;
        } else {
            session_destroy();
            return false;
        }
    } else {
        session_destroy();
        return false;
    }
}

function signUpAuth($fname, $lname, $contact, $username, $password)
{
    require "connection.php";

    $sql = "SELECT * FROM site_user WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        return false;
    }

    $hashedPass = hash_password($password);

    $insertSql = "INSERT INTO site_user VALUES (DEFAULT, ?, ?, ?, ?, ?)";
    $query = $conn->prepare($insertSql);
    $query->bind_param("sssss", $fname, $lname, $contact, $username, $hashedPass);
    $query->execute();

    if ($query->affected_rows > 0) {
        return true;
    } else {
        return false;
    }
}