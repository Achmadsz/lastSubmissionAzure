<!DOCTYPE html>
<html>
    <head>
        <title>Storage And Cognitive</title>
        <script src="jquery.min.js"></script>
        <style>
            table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            }

            td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            }

            button:hover{
                background-color:blue;
            }

            .button {
                background-color: #4CAF50;
                border: none;
                color: white;
                padding: 10px 22px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                border-radius: 10px;
                
            }

            .button:hover{
                background-color: #3e8e41;
                box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
            }
            
        </style>
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
        <div class="header">
            <p>
                <h1>Upload dan Analisa Gambar dengan Microsoft Azure</h1>
            </p>
        </div>
        <div class="body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div>Pilih gambar : <input type="file" name="fileToUpload" ></div>
                <br>
                <div>
                    <input type="submit" name="Upload" value="Upload" class="button" >
                    <input type="submit" name="ShowData" value="Show All Data" class="button">
                    <input type="submit" name="Clear" value="Clear ALL Data" class="button">
                <div>
            </form>
            <br>
            <div id="wrapper" style="float:left;margin-right:20px" >
                <div>
                    Source image:
                    <br>
                    <img id="sourceImage" width="350" height="250" style="border: solid 2px"/>
                </div>
                <div>
                    <textarea id="responseTextArea" class="UIInput"
                  style="width:350px; height:100px;"></textarea>
                </div>
            </div>
                <?php 
                    require_once 'vendor/autoload.php';

                    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
                    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
                    use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
                    use MicrosoftAzure\Storage\Blob\Models\ListContainersOptions;
                    use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
                    use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
                    use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;        
                    
                    
                    

                    try{
                    //$conString = "DefaultEndpointsProtocol=https;AccountName=".getenv('ACCOUNT_NAME').";AccountKey=".getenv('ACCOUNT_KEY');
                    $conString = "DefaultEndpointsProtocol=https;AccountName=azurestoragesubmission;AccountKey=H0k1TWoNmvBLFipekVMoGj+5uA3exe+0HtATahMmG/if+g3arDg6i1u2u5PG6cXhSbeTHfW8Y5MomEDrd/yUGA==;EndpointSuffix=core.windows.net";
                    $blobClient = BlobRestProxy::createBlobService($conString);
                    $containerName = "fileupload";
                    }
                    catch(ServiceException $e){
                        // Handle exception based on error codes and messages.
                        // Error codes and messages are here:
                        // http://msdn.microsoft.com/library/azure/dd179439.aspx
                        $code = $e->getCode();
                        $error_message = $e->getMessage();
                        echo $code.": ".$error_message."<br />";
                    }


                    if (isset($_POST['Upload'])) {
                        upload($blobClient,$containerName);
                    }elseif(isset($_POST['Clear'])) {
                        clear($blobClient,$containerName);
                    }elseif(isset($_POST['ShowData'])) {
                        showListDataInStorage($blobClient,$containerName,"");
                    }
                    

                    function clear($blobClient,$containerName){
                        try{
                            // Delete container.
                            $checkCountainer = checkContainerIfNotExists($blobClient,$containerName);
                            if($checkCountainer){
                                echo "<script type='text/javascript'>alert(\"All Data Deleted Successfully\")</script>";
                                $blobClient->deleteContainer($containerName);
                            }else{
                                echo "<script type='text/javascript'>alert(\"No Data On Server\")</script>";
                            }
                        }
                        catch(ServiceException $e){
                            // Handle exception based on error codes and messages.
                            // Error codes and messages are here:
                            // http://msdn.microsoft.com/library/azure/dd179439.aspx
                            $code = $e->getCode();
                            $error_message = $e->getMessage();
                            echo $code.": ".$error_message."<br />";
                        }
                    }

                    function checkContainerIfNotExists($blobClient,$containerName){
                        // See if the container already exists.
                        $listContainersOptions = new ListContainersOptions();
                        $listContainersOptions->setPrefix($containerName);
                        $listContainersResult = $blobClient->listContainers($listContainersOptions);
                        $containerExists = false;
                        foreach ($listContainersResult->getContainers() as $container)
                        {
                            if ($container->getName() == $containerName)
                            {
                                // The container exists.
                                $containerExists = true;
                                break;
                            }
                        }

                        return $containerExists;
                        
                    }

                    function showListDataInStorage($blobClient,$containerName,$fileName){
                        
                         // List blobs.
                         $checkCountainer = checkContainerIfNotExists($blobClient,$containerName);
                         if($checkCountainer){
                            $listBlobsOptions = new ListBlobsOptions();
                            if($fileName != ""){
                                $listBlobsOptions->setPrefix($fileName);
                            }
                            echo "<table>";
                            echo "<tr><th>Image</th><th>Name</th><th>Action</th></tr>";
                            do{
                                $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
                                foreach ($result->getBlobs() as $blob)
                                {
                                    //echo $blob->getName().": ".$blob->getUrl()."<br />";
                                    //echo "<img src=".$blob->getUrl()." alt=".$blob->getName()." style='width:350px;hight:100'><br />";
                                    echo "<tr><td><img src=".$blob->getUrl()." alt=".$blob->getName()." style='width:100px;hight:70px'></td>";
                                    echo "<td>".$blob->getName()."</td>";
                                    echo '<td><button class="button" onclick="processImage(\''.$blob->getUrl().'\');return false;">Analyze image</button></tr>';
                                }
                            
                                $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                            } while($result->getContinuationToken());
                            echo "</table>";
                        }else{
                            echo "<script type='text/javascript'>alert(\"No Data On Server\")</script>";
                        }
                    }
                        
                    function upload($blobClient,$containerName){
                        $fileName = $_FILES['fileToUpload']['name'];
                        $filetemp = $_FILES['fileToUpload']['tmp_name'];
                        $target_file = $filetemp . basename($fileName);
                        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                        
                        if($filetemp != ""){
                            if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg"
                            || $imageFileType == "gif"){
                                // Create container options object.
                                $createContainerOptions = new CreateContainerOptions();
                            
                                $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
                            
                                // Set container metadata.
                                $createContainerOptions->addMetaData("key1", "value1");
                                $createContainerOptions->addMetaData("key2", "value2");
                            
                                try {
                                    // Chcek and Create container.
                                    $checkContainer = checkContainerIfNotExists($blobClient,$containerName);
                                    if (!$checkContainer){
                                        $blobClient->createContainer($containerName, $createContainerOptions);
                                    }
                                    //$blobClient->createContainer($containerName, $createContainerOptions);

                                    // Getting local file so that we can upload it to Azure
                                    $myfile = fopen($filetemp, "r") or die("Unable to open file!");
                                    
                                    //Upload blob
                                    $blobClient->createBlockBlob($containerName, $fileName, $myfile);

                                    echo "<script type='text/javascript'>alert(\"Your Data Uploaded Successfully\")</script>";

                                }
                                catch(ServiceException $e){
                                    // Handle exception based on error codes and messages.
                                    // Error codes and messages are here:
                                    // http://msdn.microsoft.com/library/azure/dd179439.aspx
                                    $code = $e->getCode();
                                    $error_message = $e->getMessage();
                                    echo $code.": ".$error_message."<br />";
                                }
                                catch(InvalidArgumentTypeException $e){
                                    // Handle exception based on error codes and messages.
                                    // Error codes and messages are here:
                                    // http://msdn.microsoft.com/library/azure/dd179439.aspx
                                    $code = $e->getCode();
                                    $error_message = $e->getMessage();
                                    echo $code.": ".$error_message."<br />";
                                }
                            }else{
                                echo "<script type='text/javascript'>alert(\"Type File Should Be JPG, PNG, JPEG and GIF\")</script>";
                            }
                        }else{
                            echo "<script type='text/javascript'>alert(\"Please, Choose Your File!\")</script>";
                        }

                    }  
                ?>
        </div>
    </body>
</html>