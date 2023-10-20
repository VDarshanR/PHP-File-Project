$(document).ready(function()
{
    fetchFiles();
	$('#Deletemodal').modal({backdrop: 'static',keyboard: false});
    $('#Updatemodal').modal({backdrop: 'static',keyboard: false});
});

//Upload
function upload()
{
	var form_data = new FormData();
	form_data.append("action","uploadit");
	form_data.append("file",document.getElementById('file-upload').files[0]);
	$.ajax ({
		url: "upload.php", 
		type: "POST",
		data:  form_data,
		contentType: false, //important to tell jQuery not to set the Content-Type header, allowing the browser to set it automatically based on the form data.
        processData: false, //important to tell jQuery not to process the data, as jQuery tries to process it as a query string by default.
		success: function(result)
		{
			var response = JSON.parse(result);
			if (response.status === "error") {
				$('#UploadMessage').html('<p class="text-danger">' + response.message + '</p>');
            } else if (response.status === "success") {
				$('#UploadMessage').html('<p class="text-success">' + response.message + '</p>');
                setTimeout(function() {
                    $('#Uploadmodal').find('[data-dismiss="modal"]').trigger('click');
					$('#UploadMessage').html('');
                }, 700);
				$('form').trigger('reset');
				fetchFiles();
			}
		}
	});
	$(document).on('click','#uploadbtn_close', function()
	{
		$('form').trigger('reset');
		$('#UploadMessage').html('');
	})	
}

//Fetch
function fetchFiles()
{
	$.ajax({
		type:'post',
		url:'upload.php',
		data:"action=fetchfiles",
		success: function(result)
		{
			if(result != null)
			{
				showfetchFiles(JSON.parse(result));
			}
		}
	});
}
function showfetchFiles(res)
{
	if (res.length == 0) { // Check if there are no files uploaded
        $("#table").hide(); // Hide the table if no files uploaded
        return;
    }
	else{
		$("#table").show();
	}
	var tbData = "<table class='table-bordered' width=60%>";
	tbData  += "<tr style= 'background-color: #000000; color: #ffffff;'><th>Name</th><th>Modified</th><th>Size</th><th>Delete</th><th>Edit</th><th>Download</th></tr>";		
	for(var i=0;i<res.length;i++)
	{
		tbData +=  "<tr class='table-row-hover' style='background:#ffffff; color:#000000;' align=center><td>"+res[i].name+"</td><td>"+res[i].modified+"</td><td>"+res[i].size+"</td><td><i class='fas fa-trash' onclick='todelete(\""+res[i].name+"\" )' style=cursor:pointer;'></i></td><td><i class='fas fa-edit' onclick='toupdate(\""+res[i].name+"\")' style=cursor:pointer;'></i></td><td><i class='fa fa-download' onclick='downloadFile(\"" + res[i].name + "\", \"" + getFileExtension(res[i].name) + "\")' style='cursor:pointer;'></i></td></tr>";
	}
	tbData += "</table>";
	$("#table").html(tbData); 
}

//Update
function toupdate(res)
{
	$('#Updatemodal').modal('show');
	$("#updatefilename").val(res);
	$("#hide").val(res);
}
function update()
{
	$.ajax({
		url: 'upload.php',
		method: 'post',
		data:'action=update&fname='+$("#hide").val()+'&newname='+$("#updatefilename").val(),
		success:function(result)
        {
			var response = JSON.parse(result);
			if (response.status === "error") {
				 $('#UpdateConfirmationMessage').hide();
				 $('#UpdateMessage').html('<p class="text-danger">' + response.message + '</p>');
			} else if (response.status === "success") {
				 $('#UpdateConfirmationMessage').hide();
				 $('#UpdateMessage').html('<p class="text-success">' + response.message + '</p>');
				setTimeout(function() {
					$('#Updatemodal').modal('hide');
					$('.modal-backdrop').remove();
					$('#UpdateConfirmationMessage').show();
					$("#UpdateMessage").html('');
                }, 700);
				fetchFiles();
			}
		}
	});
}
$(document).on('click','#updatebtn_close',function()
{
	$("#Updatemodal").modal('hide');
	$(".modal-backdrop").remove();
	$("#UpdateMessage").html('');
	$('#UpdateConfirmationMessage').show();
		
});

//Delete
function todelete(res)
{
	$('#Deletemodal').modal('show');
	$("#deletefilename").val(res);
	$("#hide").val(res);
}
function deletee()
{
    $.ajax({
        url: 'upload.php',
        method: 'post',
        data:'action=delete&file='+$("#deletefilename").val(),
        success:function(result)
        {
			var response = JSON.parse(result);
            if(response.status === "success")
            {
				$('#DeleteConfirmationMessage').hide();
                $("#DeleteMessage").html('<p class="text-success">' + response.message + '</p>')
				setTimeout(function() {
					$('#Deletemodal').modal('hide');
                	$('.modal-backdrop').remove();
					$('#DeleteConfirmationMessage').show();
					$('#DeleteMessage').html('');
				}, 700);
                fetchFiles();
            }
        }
    })
}
$(document).on('click','#deletebtn_close',function()
{
	$("#Deletemodal").modal('hide');
	$(".modal-backdrop").remove();
})		

//Download
function getFileExtension(filename) {
    return filename.split('.').pop().toLowerCase();
}
function downloadFile(filename, extension) {
    $.ajax({
        type: 'POST',
        url: 'upload.php',
        data: {
            action: 'download',
            filename: filename,
            extension: extension
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(response, status, xhr) {
            var contentType = xhr.getResponseHeader("content-type") || "application/octet-stream";
            var blob = new Blob([response], { type: contentType });
            var url = window.URL.createObjectURL(blob);
            var link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.click();
        },
    });
}