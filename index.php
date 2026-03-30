<?php
session_start();
require_once("../includes/CONFIG.php");


//   HANDLE PHOTO UPLOAD

if (isset($_POST['upload']) && isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];
    $uploadedFile = null;

    /* 1. Upload from computer */
    if (!empty($_FILES['photo']['name'])) {

        $file = $_FILES['photo']['name'];
        $tmp = $_FILES['photo']['tmp_name'];

        $newName = time() . "_" . basename($file);
        move_uploaded_file($tmp, "uploads/" . $newName);

        $uploadedFile = $newName;
    }


    /* 2. Upload from URL */
    elseif (!empty($_POST['photo_url'])) {

        $url = trim($_POST['photo_url']);

        if (filter_var($url, FILTER_VALIDATE_URL)) {

            // Get extension
            $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array(strtolower($ext), $allowed)) {

                $newName = time() . "_url." . $ext;
                $imageData = @file_get_contents($url);

                if ($imageData !== false) {
                    file_put_contents("uploads/" . $newName, $imageData);
                    $uploadedFile = $newName;
                }
            }
        }
    }

    /* Save to DB */
    if ($uploadedFile) {
        $insert = "INSERT INTO photos (user_id, filename, uploaded_at)
                   VALUES ('$user_id', '$uploadedFile', NOW())";
        mysqli_query($con, $insert);
    }

    header("Location: gallery.php");
    exit();
}


//   GET ALL PHOTOS

$photos = mysqli_query($con, "
    SELECT p.filename, u.username 
    FROM photos p
    JOIN users u ON p.user_id = u.user_id
    ORDER BY p.photo_id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Photo Gallery</title>
    <style>
        body { font-family: Arial; }
        .gallery { display: flex; flex-wrap: wrap; gap: 20px; }
        .photo-box { border: 1px solid #ccc; padding: 10px; width: 220px; }
        img { width: 200px; height: auto; display: block; }
    </style>
</head>
<body>

<h1>Photo Gallery</h1>

<div>
<?php if(isset($_SESSION['username'])): ?>
    Hello, <strong><?php echo $_SESSION['username']; ?></strong>
    <a href="logout.php">Logout</a>

    <h3>Upload a Photo</h3>

    <form method="POST" enctype="multipart/form-data">
        <p><strong>Upload from your device:</strong></p>
        <input type="file" name="photo">

        <p><strong>Or upload from URL:</strong></p>
        <input type="text" name="photo_url" placeholder="https://example.com/image.jpg" style="width:300px;">

        <br><br>
        <button name="upload">Upload Photo</button>
    </form>

<?php else: ?>
    <a href="login.php">Log in</a> |
    <a href="register.php">Register</a>
<?php endif; ?>
</div>

<hr>

<!-- Show all photos -->
<div class="gallery">
<?php while($p = mysqli_fetch_assoc($photos)): ?>
    <div class="photo-box">
        <img src="uploads/<?php echo $p['filename']; ?>">
        <p>Uploaded by: <strong><?php echo $p['username']; ?></strong></p>
    </div>
<?php endwhile; ?>
</div>

</body>
</html>
