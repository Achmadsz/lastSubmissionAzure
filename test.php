<!doctype>
<htmL>
<head>
	<title>Testing PHP</title>
</head>
<body>
<script type="text/javascript">
        function processImage(urlFile) {
            // **********************************************
            // *** Update or verify the following values. ***
            // **********************************************
    
            // Replace <Subscription Key> with your valid subscription key.
            var subscriptionKey = "d7b49d561ef342f6be3214ba8bcbee47";
    
            // You must use the same Azure region in your REST API method as you used to
            // get your subscription keys. For example, if you got your subscription keys
            // from the West US region, replace "westcentralus" in the URL
            // below with "westus".
            //
            // Free trial subscription keys are generated in the "westus" region.
            // If you use a free trial subscription key, you shouldn't need to change
            // this region.
            var uriBase =
                "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
    
            // Request parameters.
            var params = {
                "visualFeatures": "Categories,Description,Color",
                "details": "",
                "language": "en",
            };
    
            // Display the image.
            document.querySelector("#sourceImage").src = urlFile;
    
            // Make the REST API call.
            $.ajax({
                url: uriBase + "?" + $.param(params),
    
                // Request headers.
                beforeSend: function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader(
                        "Ocp-Apim-Subscription-Key", subscriptionKey);
                },
    
                type: "POST",
    
                // Request body.
                data: '{"url": ' + '"' + urlFile + '"}',
            })
    
            .done(function(data) {
                // Show formatted JSON on webpage.
               //$("#responseTextArea").val(JSON.stringify(data, null, 2));

               $("#responseTextArea").val(JSON.stringify(data["description"]["captions"][0].text,null,2));
            })
    
            .fail(function(jqXHR, textStatus, errorThrown) {
                // Display error message.
                var errorString = (errorThrown === "") ? "Error. " :
                    errorThrown + " (" + jqXHR.status + "): ";
                errorString += (jqXHR.responseText === "") ? "" :
                    jQuery.parseJSON(jqXHR.responseText).message;
                alert(errorString);
            });
        };
    </script>
	 <form action="process.php" method="POST" enctype="multipart/form-data">
                <div>Pilih gambar : <input type="file" name="fileToUpload" ></div>
                <br>
                <div>
                    <input type="submit" name="Upload" value="Upload" class="button" >
                    <input type="submit" name="ShowData" value="Show All Data" class="button">
                    <input type="submit" name="Clear" value="Clear ALL Data" class="button">
                <div>
            </form>
</body>
</htmL>