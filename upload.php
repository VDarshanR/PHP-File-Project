<?php
if (isset($_POST['action']))
{   
  if($_POST["action"] == "uploadit")
  {
	if(empty($_FILES['file']['tmp_name'])) 
	{
		echo "<script>alert('Please select a file to upload.');</script>";
	}
	else
	{
		$fileTmpPath = $_FILES['file']['tmp_name']; //temporary path to the uploaded file.
		$fileName = $_FILES['file']['name']; //name of the uploaded file.
		$fileSize = $_FILES['file']['size']; //size of the uploaded file in bytes.
		$fileType = $_FILES['file']['type']; //Multipurpose Internet Mail Extensions(MIME)type of the uploaded file.
		$fileNameCmps = explode(".", $fileName); //an array created by exploding the $fileName on the "." character. This is done to get the extension of the file.
		$fileExtension = strtolower(end($fileNameCmps)); //extension of the uploaded file in lowercase. 	
		$uploadFileDir = './uploads/'; //directory where the file will be uploaded.
		$dest_path = $uploadFileDir . $fileName; //set to the full path of the uploaded file, which is a combination of $uploadFileDir and $fileName.
		$target_file = $uploadFileDir.basename($fileName); // set to the basename of the uploaded file, which is the same as $fileName.
		$allowedFileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'pdf', 'docx');
		
		if (in_array($fileExtension, $allowedFileExtensions))
		{
		  if(file_exists($target_file))
		  {
			echo "<script>alert('Already file exist.');</script>";
		  }
		  elseif($fileSize>50000000)
		  {
			echo "<script>alert('File is too large to upload.');</script>";
		  }       
		  elseif(move_uploaded_file($fileTmpPath, $dest_path))
		  {
			echo "<script>alert('File is successfully uploaded.');</script>";
		  }
		  else
		  {
			echo "<script>alert('Please make sure that the upload directory is writable by web server.');</script>";
		  }
		}
		else
		{
		  echo "<script>alert('Upload valid extension.');</script>";
		}
	}
  }
}
	//Fetch_files
	if($_POST["action"] == "fetchfiles")
	{
		$dirpath = "uploads";
		$files  = scandir($dirpath);
		$fileArr = array();
		foreach($files as $filename)
		{
			array_push($fileArr,array("name"=>basename($filename),"modified"=>date("F d Y H:i:s.",filemtime("uploads/".$filename)),"size"=>formatSizeUnits(filesize("uploads/".$filename))));
		}
		echo json_encode($fileArr);
	}
	
	//Update_file_names
	if($_POST["action"] == "update")
    {	
		$uploadFileDir = 'uploads/';
		$oldName = $uploadFileDir."/".$_POST["fname"];
		$newName = $uploadFileDir."/".$_POST["newname"];
		$oldNameCmps = explode(".",$_POST["fname"] );
        $oldFileExtension = strtolower(end($oldNameCmps));
		$newNameCmps = explode(".", $_POST["newname"]);
        $newFileExtension = strtolower(end($newNameCmps));	
		if($oldName == $newName) 
		{
			http_response_code(400);
			echo json_encode(array("message" => "Old and new file name are the same."));
        }
		else if(file_exists($newName)) 
		{
			http_response_code(400);
			echo json_encode(array("message" => "File already exists."));
		}
		else if($oldFileExtension != $newFileExtension)
		{
			http_response_code(400);
			echo json_encode(array("message" => "File extensions can't be renamed."));
		}
		else if(rename($oldName, $newName)) 
		{
			http_response_code(200);
			echo json_encode(array("message" => "File successfully updated."));
		}
    }
	
	//Delete_files
	if($_POST["action"] == "delete")
	{
		$uploadFileDir = 'uploads/'; 
		if (file_exists($uploadFileDir)) 
		{
		  unlink($uploadFileDir.$_POST['file']); 
		  echo json_encode(array("result" => "success"));
		} 
		else 
		{ 
		   echo json_encode(array("result" => "failure"));	   
		} 
	}
	
	//File_size_caluculation
	function formatSizeUnits($bytes)
	{
		if ($bytes >= 1073741824)
		{
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		}
		elseif ($bytes >= 1048576)
		{
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		}
		elseif ($bytes >= 1024)
		{
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		}
		elseif ($bytes > 1)
		{
			$bytes = $bytes . ' bytes';
		}
		elseif ($bytes == 1)
		{
			$bytes = $bytes . ' byte';
		}
		else
		{
			$bytes = '0 bytes';
		} 
		return $bytes;
	}
?>