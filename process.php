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
                        $conString = "DefaultEndpointsProtocol=https;AccountName=".getenv('ACCOUNT_NAME').";AccountKey=".getenv('ACCOUNT_KEY');
                    $blobClient = BlobRestProxy::createBlobService($conString);
                    $containerName = "fileupload";

                    echo $containerName;
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
                            //echo "<script type='text/javascript'>alert(\"Please, Choose Your File!\")</script>";
                            echo "Please, Choose Your File";
                        }

                    }  
                ?>