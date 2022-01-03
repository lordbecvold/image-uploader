<?php 

    //Set page headers
    header("Content-type: text/html; charset=UTF-8");


    //Mysql config
    $mysqlIP = "localhost";
    $mysqlUser = "root";
    $mysqlPassword = "root";
    $mysqlDatabase = "ImageUploader";


    //Connection to mysql
    $connection = mysqli_connect($mysqlIP, $mysqlUser, $mysqlPassword, $mysqlDatabase);


    //Upload phase
    if (isset($_POST["submit"])) {
        
        $imgSpec = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 20);
        $image = base64_encode(file_get_contents($_FILES["imageFile"]["tmp_name"]));

        $query = mysqli_query($connection, "INSERT INTO `images`(`imgSpec`, `image`) VALUES ('$imgSpec', '$image')");
        if (!$query) {
            http_response_code(503);
            die('The service is currently unavailable due to the inability to send requests');
        }

        header("location: index.php?process=show&imgSpec=".$imgSpec);
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/main.css">
    <title>Image uploader</title>
</head>
<body>
    <script class="jsbin" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <header>
        <ul>
            <li><a href="index.php">Uploader</a></li>
        </ul>
    </header>
    <?php

        if (!empty($_GET["process"]) && $_GET["process"] == "show") {
            
            //get imgSpec image id
            $imgSpec = htmlspecialchars(mysqli_real_escape_string($connection, $_GET["imgSpec"]), ENT_QUOTES);

            //Check if if specified
            if (empty($imgSpec)) {
                die("Error image is not specified");
            } else {
                $image = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM images WHERE imgSpec='".$imgSpec."'"));
                echo '
                    <main>
                        <img src="data:image/jpeg;base64,'.$image["image"].'">
                    </main>
                ';
            }
        } else {
            echo '
                <main>
                    <form action="index.php" method="post" enctype="multipart/form-data">
                        <div class="file-upload">
                        <p class="formTtitle">Image upload</p>
                            <div class="image-upload-wrap">
                                <input class="file-upload-input" type="file" name="imageFile" onchange="readURL(this);" accept="image/*" />
                                <div class="drag-text">
                                    <h3>Drag and drop a file or select add Image</h3>
                                </div>
                            </div>
                            <div class="file-upload-content">
                                <img class="file-upload-image" src="#" alt="your image" />
                                <div class="image-title-wrap">
                                    <button type="button" onclick="removeUpload()" class="remove-image">Remove <span class="image-title">Uploaded Image</span></button>
                                </div>
                            </div><br>
                            <input class="file-upload-btn" type="submit" value="Upload Image" name="submit">
                        </div>
                    </form>
                </main>
            ';
        }
    ?>
    <footer>
        <p>Made with ❤️ By <a href="https://www.becvar.xyz">Lordbecvold</a></p>
    </footer>
    <script src="assets/main.js"></script>
</body>
</html>