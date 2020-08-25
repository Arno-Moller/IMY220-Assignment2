<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;
	$uID = isset($_POST["userID"]) ? $_POST["userID"] : false;

    $target_file = isset($_FILES["picToUpload"]["name"]) ? basename($_FILES["picToUpload"]["name"]) : false;
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Arno Möller">

</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form method='post' action='login.php' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>
									<input type='hidden' name='userID' value='". $row['user_id'] ."'/>
									<input type='hidden' name='loginEmail' value='". $row['email'] ."'/>
									<input type='hidden' name='loginPass' value='". $row['password'] ."'/>
									<input type='submit' class='btn btn-secondary' value='Upload Image' name='submit' />
								</div>
						  	</form>";


//					Make a SQL query to select all the images stored for the user
                    $sql = "SELECT filename FROM tbgallery WHERE user_id = " . $row['user_id'] . ";";
                    $result = $mysqli->query($sql);

//                    Add divs for the images to be displayed in
                    echo '<h2>Image Gallery</h2>
                              <div class="row imageGallery">';

//                    If the user has Images saved
                    if ($result->num_rows > 0) {

//                        Add the images to the gallery div
                        while($row = $result->fetch_assoc()) {
                            echo '<div class="col-3 " style="background-image: url(gallery/'. $row['filename'] .')"></div>';
                        }


                    }

//                    If the user has clicked the submit button and user_ID is set
                    if($uID)
                    {
                        if(isset($_POST["submit"])) {

//                            Get the name of the file as well as the file's path
                            $file = $_FILES["picToUpload"]["name"];
                            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

//                            Check if the images are either jpg or jpeg
                            if($imageFileType == 'jpg' || $imageFileType == 'jpeg')
                            {
//                                If a 'gallery' folder does not exist, create one
                                if (!file_exists('./gallery')) {
                                    mkdir('./gallery', 0777, true);
                                }

//                                Get the size of the image
                                $check = getimagesize($_FILES["picToUpload"]["tmp_name"]);

//                                If the size is obtained
                                if($check !== false) {
                                    $filepath = "./gallery/" . $file;

//                                    If the move of the submitted image to the folder called 'gallery' is successful && its size is less than 1MB
                                    if($_FILES["picToUpload"]["size"] < 1048576 && move_uploaded_file($_FILES["picToUpload"]["tmp_name"], $filepath))
                                    {
                                        $query = "SELECT * FROM tbgallery WHERE filename = '". $file ."' and user_id = '". $uID ."'";
                                        $res = $mysqli->query($query);

//                                        Add image to database
                                        if(mysqli_fetch_array($res) == false){

                                            $query = "INSERT INTO tbgallery (user_id, filename) VALUES ('". $uID ."', '". $file ."');";
                                            $res = $mysqli->query($query);
                                            if($res){
                                                echo '<div class="col-3 " style="background-image: url(gallery/'. $file .')"></div>';
                                            }
                                        }
                                    }
                                    else
                                    {
                                        echo "An error has occurred";
                                    }
                                }

                            } else {
                                echo "File is not an supported.";
                                $uploadOk = 0;
                            }

                        }
                    }
                    echo '</div>';
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			}

			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>