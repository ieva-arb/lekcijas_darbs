<?php


session_start();
require_once("includes/CONFIG.php");

/* checks if all necessary data is selected so it can upload correctly (only use if nothing is uploading or error)
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";*/



//   HANDLE PHOTO UPLOAD

if (isset($_POST['upload']) && isset($_SESSION['user_id'])) {
        echo "UPLOAD STARTED";

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

    /* Save to DB */
    if ($uploadedFile) {
        $insert = "INSERT INTO photos (user_id, filename, uploaded_at)
                   VALUES ('$user_id', '$uploadedFile', NOW())";
        mysqli_query($con, $insert);
    }

    header("Location: index.php");
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
    <link rel="stylesheet" href="css/style_index.css">

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
        <label class="file-upload">
            Choose File
            <input type="file" name="photo" id="photoInput">
        </label>

        <span id="fileName">No file chosen</span>

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

<script>
const photoInput = document.getElementById('photoInput');
const fileNameSpan = document.getElementById('fileName');

photoInput.addEventListener('change', function() {
    if (photoInput.files.length > 0) {
        fileNameSpan.textContent = photoInput.files[0].name;
    } else {
        fileNameSpan.textContent = "No file chosen";
    }
});
</script>

</body>
</html>
