<?php
include_once "config.php";
include_once "image.php";

if(isset($_POST['btnAdd']))
{
$myFile = $_FILES['fileField']['name']; // Storing name into variable

//====If you want to change the name of the File====
$anyNum = rand(20,500789000); //Will generate a random number between 20and 500789000

$newFileName = $anyNum.$myFile;//===New string is concatenated====
//===Following Function will check if the File already exists========

if (file_exists("upload/".$newFileName))
{
echo $newFileName." already exists. ";
}
//======If file already exists in your Folder, It will return zero and Will not take any action===
//======Otherwise File will be stored in your given directory and Will store its name in Database===
else
{

$query = "insert into tblfileupload(file_name) values ('$newFileName')";
echo $query;
//$res = mysql_query($query);

copy($_FILES['fileField']['tmp_name'],'upload/'.$newFileName);
//===Copy File Into your given Directory,copy(Source,Destination)

if($res&gt;0)
{
//====$res will be greater than 0 only when File is uploaded Successfully====:)
echo 'You have Successfulluy Uploaded File';
}
}
}
?>

<html>
<head>
<title>Upload File Form</title>
</head>

<body>

<form id="form1" name="form1" enctype="multipart/form-data" method="post" action="upload.php">
<h2>PHP Script to Upload File</h2>
Upload File:
<input type="file" name="fileField" id="fileField" />

<input type="submit" name="btnAdd" id="btnAdd" value="Upload" />
</form>
</body>
</html>
