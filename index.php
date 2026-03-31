<?php
session_start();
require_once("includes/CONFIG.php");

/* -------------------------
   HANDLE PHOTO UPLOAD
-------------------------- */
if (isset($_POST['upload']) && isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];
    $uploadedFile = null;

    if (!empty($_FILES['photo']['name'])) {

        $file = $_FILES['photo']['name'];
        $tmp  = $_FILES['photo']['tmp_name'];

        $newName = time() . "_" . basename($file);
        move_uploaded_file($tmp, "uploads/" . $newName);

        $uploadedFile = $newName;
    }

    if ($uploadedFile) {
        $insert = "
            INSERT INTO photos (user_id, filename, uploaded_at)
            VALUES ('$user_id', '$uploadedFile', NOW())
        ";
        mysqli_query($con, $insert);
    }

    header("Location: index.php");
    exit();
}

/* -------------------------
   PHOTO FILTERING
-------------------------- */
if (isset($_GET['filter']) && $_GET['filter'] === 'mine' && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];

    $photos = mysqli_query($con, "
        SELECT p.photo_id, p.filename, p.user_id, u.username 
        FROM photos p
        JOIN users u ON p.user_id = u.user_id
        WHERE p.user_id = '$uid'
        ORDER BY p.photo_id DESC
    ");
} else {
    // Default: show all photos
    $photos = mysqli_query($con, "
        SELECT p.photo_id, p.filename, p.user_id, u.username 
        FROM photos p
        JOIN users u ON p.user_id = u.user_id
        ORDER BY p.photo_id DESC
    ");
}
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
<?php if (isset($_SESSION['username'])): ?>
    Hello, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
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

    <!-- FILTER BUTTONS -->
    <div class="photo-filters" style="margin-top:20px;">
        <a href="index.php" class="btn">All Photos</a>
        <a href="index.php?filter=mine" class="btn">My Photos</a>
    </div>

<?php else: ?>
    <a href="login.php">Log in</a> |
    <a href="register.php">Register</a>
<?php endif; ?>
</div>

<hr>

<!-- Show all photos -->
<div class="gallery">
<?php while ($p = mysqli_fetch_assoc($photos)): ?>
    <div class="photo-box">
        <img src="uploads/<?php echo htmlspecialchars($p['filename']); ?>" alt="Photo">
        <p>Uploaded by: <strong><?php echo htmlspecialchars($p['username']); ?></strong></p>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $p['user_id']): ?>
            <a href="delete_photo.php?id=<?php echo $p['photo_id']; ?>" 
               class="btn btn-danger btn-sm"
               onclick="return confirm('Do you really want to delete this photo?')">
                Delete
            </a>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
</div>

<script>
const photoInput = document.getElementById('photoInput');
const fileNameSpan = document.getElementById('fileName');

if (photoInput) {
    photoInput.addEventListener('change', function() {
        fileNameSpan.textContent = photoInput.files.length > 0
            ? photoInput.files[0].name
            : "No file chosen";
    });
}
</script>

</body>
</html>
