<!doctype>
<htmL>
<head>
	<title>Testing PHP</title>
</head>
<body>
	 <form action="" method="POST" enctype="multipart/form-data">
                <div>Pilih gambar : <input type="file" name="fileToUpload" ></div>
                <br>
                <div>
                    <input type="submit" name="Upload" value="Upload" class="button" >
                    <input type="submit" name="ShowData" value="Show All Data" class="button">
                    <input type="submit" name="Clear" value="Clear ALL Data" class="button">
                <div>
            </form>
			<?php 
					if($_GET){
                    if (isset($_POST['Upload'])) {
						echo "No button press Upload.";
                    }elseif(isset($_POST['Clear'])) {
						echo "No button press Clear.";
                    }elseif(isset($_POST['ShowData'])) {
						echo "No button press ShowData.";
					}
				}else{
					echo "No button press.";
				}
                    
 
                ?>
</body>
</htmL>