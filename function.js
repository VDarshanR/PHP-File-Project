$(document).ready(function()
{
    fetchFiles();
})

function fetchFiles()
{
	$.ajax({
		type:'post',
		url:'upload.php',
		data:"action=fetchfiles",
		cache:false,
		async:true,
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
	if (res.length <= 2) { // Check if there are no files uploaded
        $("#table").hide(); // Hide the table if no files uploaded
        return;
    }
	var tbData = "<table class='table-bordered' width=60% style='background:#191970; color:#ffffff'>";
	tbData  += "<tr style= 'background-color: #000000'><th>Name</th><th>Modified</th><th>Size</th><th>Delete</th><th>Edit</th></tr>";		
	for(var i=2;i<res.length;i++) //index starts from 2 because the res array contains two items at the beginning. These two items are the root folder (..) and the current folder (.). 
	{
		tbData +=  "<tr align=center><td>"+res[i].name+"</td><td>"+res[i].modified+"</td><td>"+res[i].size+"</td><td><i class='fas fa-trash' onclick='todelete(\""+res[i].name+"\" )' style=cursor:pointer;'></i></td><td><i class='fas fa-edit' onclick='toupdate(\""+res[i].name+"\")' style=cursor:pointer;'></i></td></tr>";
	}
	tbData += "</table>";
	$("#table").html(tbData);
}

function upload(res)
{
	var form_data = new FormData();
	form_data.append("action","uploadit");
	form_data.append("file",document.getElementById('file-upload').files[0]);
	$.ajax
	({
		url: "upload.php", 
		type: "POST",
		data:  form_data,
		contentType: false,
		cache: false,
		processData:false,
		success: function(result)
		{
			$('#message').html(result);
			$('form').trigger('reset');
			fetchFiles();
			location.reload();
		}
	});
	$(document).on('click','#btn_close', function()
	{
		$('form').trigger('reset');
		$('#message').html('');
	})	
}

function todelete(res)
{
	$('#Deletemodal').modal('show');
	$("#filename1").val(res);
	$("#hide").val(res);
	$(document).on('click','#btn_close',function()
	{
		$("#Deletemodal").modal('hide');
		$(".modal-backdrop").remove();
	})		
}
function deletee()
{
    $.ajax({
        url: 'upload.php',
        method: 'post',
        data:'action=delete&file='+$("#filename1").val(),
        success:function(data)
        {
            if(JSON.parse(data).result == "success")
            {  
                alert("Successfully deleted.");
                $('#Deletemodal').modal('hide');
                $('.modal-backdrop').remove();
                fetchFiles();
            }
			else{
				alert("Can't delete file.");
                $('#Deletemodal').modal('hide');
				$('.modal-backdrop').remove();
                fetchFiles();
			}
        }
    })
}

function toupdate(res)
{
	$('#Updatemodal').modal('show');
	$("#filename").val(res);
	$("#hide").val(res);
	$(document).on('click','#btn_close',function()
	{
		$("#Updatemodal").modal('hide');
		$(".modal-backdrop").remove();
	})
}
function update()
{
	$.ajax({
		url: 'upload.php',
		method: 'post',
		data:'action=update&fname='+$("#hide").val()+'&newname='+$("#filename").val(),
		success:function(data, status, xhr)
        {
			if(xhr.status === 200)
			{
				var response = JSON.parse(data);
				alert(response.message);
				$('#Updatemodal').modal('hide');
				$('.modal-backdrop').remove();
				fetchFiles();
			}
		},
		error: function(xhr, status, error) 
		{
			if (xhr.status === 400) 
			{
				var response = JSON.parse(xhr.responseText);
                alert(response.message);
                $('#Updatemodal').modal('hide');
				$('.modal-backdrop').remove();
                fetchFiles();
			}
		}
	})
}