<?php
session_start();

// Change these variables according to your MySQL server configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'testrun';

// Connect to MySQL server
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to sanitize input data
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($input)));
}

// Function to hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to verify password
function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

// Check if email exists
function emailExists($email) {
    global $conn;
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result) > 0;
}

// Registration process for both student and vendor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $fname = sanitize($_POST['fname']);
    $mname = sanitize($_POST['mname']);
    $lname = sanitize($_POST['lname']);
    $email = sanitize($_POST['email']);
    $password = hashPassword(sanitize($_POST['password']));
    $registerAs = sanitize($_POST['register-as']);

    if (emailExists($email)) {
        echo "Email already exists. Please use a different email.";
    } else {
        $sql = "INSERT INTO users (first_name, middle_name, last_name, email, upass, register_as) VALUES ('$fname', '$mname', '$lname', '$email', '$password', '$registerAs')";
        if (mysqli_query($conn, $sql)) {
            $userId = mysqli_insert_id($conn);
            $_SESSION['user_id'] = $userId;

            if ($registerAs == 'student') {
                header("Location: ../4 Noncontact/desktop2.html");
            } elseif ($registerAs == 'vendor') {
                header("Location: ../12.9 Admin Profile/adminBlank.html");
            }
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

// Login process for both student and vendor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $registerAs = sanitize($_POST['register-as']);

    $sql = "SELECT * FROM users WHERE email='$email' AND register_as='$registerAs'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (verifyPassword($password, $row['upass'])) {
            $_SESSION['user_id'] = $row['id']; // Store user ID in session for further use
            if ($registerAs == 'student') {
                header("Location: ../4 Noncontact/desktop2.html"); // Redirect to student dashboard
            } elseif ($registerAs == 'vendor') {
                header("Location: ../12.9 Admin Profile/adminBlank.html"); // Redirect to vendor dashboard
            }
            exit();
        } else {
            echo "Incorrect email or password";
        }
    } else {
        echo "User not found or invalid registration type";
    }
}

// Close MySQL connection
mysqli_close($conn);
?>
