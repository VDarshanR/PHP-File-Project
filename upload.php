<?php
	$response = new stdClass();
	if($_POST["action"] == "uploadit")
	{
		if(empty($_FILES['file']['tmp_name'])) 
		{
			$response->status = "error";
			$response->message =  "Please select a file to upload";
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
			$allowedFileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'pdf', 'docx', 'jpeg', 'doc');
			
			if (in_array($fileExtension, $allowedFileExtensions))
			{
				if(file_exists($target_file))
				{
				$response->status = "error";
				$response->message = "File already exist";
				}
				elseif($fileSize>50000000)
				{
				$response->status = "error";
				$response->message  =  "File is too large to upload; size limit is 50MB";
				}       
				elseif(move_uploaded_file($fileTmpPath, $dest_path))
				{
				$response->status = "success";
				$response->message  = "File is successfully uploaded";
				}
				else
				{
				$response->status = "error";
				$response->message  = "Please make sure that the upload directory is writable by web server";
				}
			}
			else
			{
				$response->status = "error";
				$response->message  = "Please upload valid extension";
			}
		}
		echo (json_encode($response));
	}

	//Fetch_files
	if($_POST["action"] == "fetchfiles")
	{
		$dirpath = "uploads";
		$files  = scandir($dirpath);
		$fileArr = array();
		foreach($files as $filename)
		{
			if($filename != "." && $filename != "..") {
				array_push($fileArr,array("name"=>basename($filename),"modified"=>date("F d Y H:i:s",filemtime("uploads/".$filename)),"size"=>formatSizeUnits(filesize("uploads/".$filename))));
			}
		}
		echo (json_encode($fileArr));
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
		
		if(file_exists($newName) && $oldName != $newName) 
		{
			$response->status = "error";
			$response->message = "File already exist with the same name";
		}
		else if($oldFileExtension != $newFileExtension)
		{
			$response->status = "error";
			$response->message = "File extensions can't be renamed";
		}
		else if(rename($oldName, $newName)) 
		{
			$response->status = "success";
			$response->message = "File successfully updated";
		}
		echo (json_encode($response));
	}
		
	//Delete_files
	if($_POST["action"] == "delete")
	{
		$uploadFileDir = 'uploads/'; 
		if (file_exists($uploadFileDir)) 
		{
			unlink($uploadFileDir.$_POST['file']);
			$response->status = "success"; 
			$response->message = "File successfully deleted";
		}
		echo json_encode($response); 
	}

	//Download_files
	if($_POST["action"] == "download") {
		$filename = $_POST['filename'];
		$extension = $_POST['extension'];
		$filepath = 'uploads/' . $filename;
	
		if (file_exists($filepath)) {
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $filename);
			header('Content-Length: ' . filesize($filepath));
			echo file_get_contents($filepath);
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