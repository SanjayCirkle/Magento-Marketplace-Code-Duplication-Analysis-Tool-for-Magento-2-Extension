<?php 
define("MAGENTO_FOLDER", getcwd());

$printresult=0;
$iscode=0;
$iszip=0;
if(isset($_POST['submit']) && $_POST['submit']=='Begin Code Audit')
{
    if( (isset($_POST['code']) && !empty($_POST['code'])) || (isset($_FILES['ext_zip']['name']) && !empty($_FILES['ext_zip']['name'])) )
    {
        $path=$_POST['path'];
        $version=$_POST['mversion'];
       
       //******* Unzip selected magento version if not exists *******//
        $filename = $version.'.zip';
        $extract_folder = MAGENTO_FOLDER."/vendor/magento-".$version;
        $versions_folder = MAGENTO_FOLDER."/magento-versions/";
        if(!is_dir($extract_folder))
        {
            if (!mkdir( $extract_folder, 0777))
            {
                $status		= "error";
                $message	= "Server didn't send a response. Contact us for further assistance.1";
                goto skipcode;
            }
            
            $zip = new ZipArchive;
          
            if ($zip->open($versions_folder.$filename) === TRUE) {
              $zip->extractTo($extract_folder);
              $zip->close();
            }else{
                $status		= "error";
                $message	= "Server didn't send a response. Contact us for further assistance.2";
                goto skipcode;
            }
        }
        else{
            chmod($extract_folder, 0777);
        }
        //******* Unzip selected magento version if not exists *******//
        
        //******* Create our custom folder if not exists *******//
        $check_path=$extract_folder.'/mcs/';
        if(!is_dir($check_path))
        {
            if (!mkdir( $check_path, 0777))
            {
                $status		= "error";
                $message	= "Server didn't send a response. Contact us for further assistance.3";
                goto skipcode;
            }
        }else{
            chmod($check_path, 0777); 
        } 
        //******* Create our custom folder if not exists *******//
        
        if(isset($_FILES['ext_zip']['name']) && !empty($_FILES['ext_zip']['name']))
        {
           
            $iszip=1;
            $filename = strtotime(date('m/d/Y h:i:s a', time()));
            $reportfile=MAGENTO_FOLDER."/reports/".$filename.'_report.xml';
            if (!mkdir( $check_path.$filename, 0777))
            {
                $status		= "error";
                $message	= "Server didn't send a response. Contact us for further assistance.3";
                goto skipcode;
            }
            chmod($check_path.$filename, 0777); 
            
            $file_name = $_FILES['ext_zip']['name'];
            $file_ext=strtolower(end(explode('.',$_FILES['ext_zip']['name'])));
            $file_tmp = $_FILES['ext_zip']['tmp_name'];
            $expensions= array("zip");
            
            if(in_array($file_ext,$expensions)=== false){
                
                $status		= "error";
                $message	= "This file type cannot be uploaded. Only zip file allowed";
                goto skipcode;
            }
            $extract_uploadzip=$check_path.$filename.'/';
            $uploadzip=$check_path.$filename.'/'.$file_name;
            
            if (!move_uploaded_file($file_tmp,$uploadzip)){
            
                $status		= "error";
                $message	= "Ooops! Your upload can't not be complete. Please try again later";
                goto skipcode;
            }
            
            if(!file_exists($uploadzip))
            {
                $status		= "error";
                $message	= "Ooops! Your upload can't not be complete. Please try again later";
                goto skipcode;
            }
            else{
                chmod($uploadzip, 0777);
                $zip = new ZipArchive;
          
                if ($zip->open($uploadzip) === TRUE) {
                  $zip->extractTo($extract_uploadzip);
                  $zip->close();
                }else{
                    $status		= "error";
                    $message	= "Your upload zip file can't be extracted";
                    goto skipcode;
                }
            }
            $findfileinreport=$extract_uploadzip;
        }
        else{
            
            //******* Create file from code *******//
            $iscode=1;
            $code=$_POST['code'];
            $filename = strtotime(date('m/d/Y h:i:s a', time()));
            $reportfile=MAGENTO_FOLDER."/reports/".$filename.'_report.xml';
            $filename=$filename.".php";
            
            $code_file = fopen($check_path.$filename, "w");
                fwrite($code_file, $code);
            fclose($code_file);
            
            if(!file_exists($check_path.$filename))
            {
                $status		= "error";
                $message	= "Server didn't send a response. Contact us for further assistance.4";
                goto skipcode;
            }
            else{
                chmod($check_path.$filename, 0777);
            }
            $findfileinreport=$check_path.$filename;
            //******* Create file from code *******//
        }
        
        
        //******* Code Duplication checking *******//
        
        /*$output = shell_exec('php ../bin/magento dev:tests:run static');
        echo $command = 'php ../vendor/bin/phpcpd' . ' --log-pmd "'.$reportfile.'_report.xml"  --names "'.$filename.'"';
        //php vendor/bin/phpcpd --log-pmd "1501914239_report.xml" ./vendor/magento/ */

        $command = 'php ../vendor/bin/phpcpd' . ' --names-exclude "*Test.php" --min-lines 13 --min-tokens 20 --log-pmd "'.$reportfile.'"   ./vendor/magento-'.$version.'/';
        $output =shell_exec($command);
        //print_r($output);
        if($output){
            if(file_exists($reportfile)){
                
                chmod($reportfile, 0777); 
                try
                {
                    $string=file_get_contents($reportfile);
                    $xml = new SimpleXMLElement($string);
                    $xmlData=simplexml_load_string($xml);
                    //$result = $xml->xpath('duplication/file[@path="'.$findfileinreport.'"]/..');
                     $result = $xml->xpath('duplication/file[contains(@path,"'.$findfileinreport.'")]/..');
                    //echo "<pre>";print_r($xmlData);echo "</pre>";  
                    if(!empty($result))
                    {
                       $printresult=1;
                       $status		= "success";
                       $message	= "Code duplication found. Please check above Report!";
                       goto skipcode;
                    }
                    else{
                        $status		= "success";
                        $message	= "No code duplication found in your code";
                        goto skipcode;
                    }
                    
                  
                }
                catch (Exception $e) 
                {
                    $status		= "error";
                    $message	= $e->getMessage();
                    goto skipcode;
                }
                
            }else{
                $status		= "error";
                $message	= "Server didn't send a response. Contact us for further assistance.5";
                goto skipcode;
            }
            
        }else{
            $status		= "error";
            $message	= "Server didn't send a response. Contact us for further assistance.6";
            goto skipcode;
        }
    }
    else{
        $status		= "error";
        $message	= "Please enter your code OR upload extension zip";
        goto skipcode;
    }
}
//goto here 
skipcode:
function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
        foreach( $files as $file )
        {
            delete_files( $file );      
        }
        chmod($target, 0777);
        rmdir( $target );
    } elseif(is_file($target)) {
        chmod($target, 0777);
        @unlink( $target );  
    }
}
if(isset($findfileinreport) && !empty($findfileinreport))
{
    delete_files($findfileinreport);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Code Duplication Analysis Tool for Magento&reg; 2 by M-Connect Media</title>
    
    <meta property="og:url" content="http://codeanalysis.labs.mconnectmedia.com/" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Code Duplication Analysis Tool for Magento&reg; 2 by M-Connect Media" />
    <meta property="og:description" content="Magento 2 extension developers can get rid of code quality issues using code duplication analysis tool developed by M-Connect Media" />
    <meta property="og:image" content="http://codeanalysis.labs.mconnectmedia.com/images/banner.png" />
    
    <!-- simplesharebuttons.com/plus twitter share details -->
    <meta name="twitter:title" content="Code Duplication Analysis Tool for Magento&reg; 2 by M-Connect Media">
    <meta name="twitter:description" content="Magento 2 extension developers can get rid of code quality issues using code duplication analysis tool developed by M-Connect Media">
    <meta name="twitter:image:src" content="http://codeanalysis.labs.mconnectmedia.com/images/banner.png">
    
    <!-- simplesharebuttons.com/plus google+ share details -->
    <meta itemprop="name" content="Code Duplication Analysis Tool for Magento&reg; 2 by M-Connect Media">
    <meta itemprop="description" content="Magento 2 extension developers can get rid of code quality issues using code duplication analysis tool developed by M-Connect Media">
    <meta itemprop="image" content="http://codeanalysis.labs.mconnectmedia.com/images/banner.png">
    
    <link  rel="icon" type="image/x-icon" href="http://codeanalysis.labs.mconnectmedia.com/images/favicon-16x16.png" />


	<link href="assets/style.css" rel="stylesheet" type="text/css" >
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
	<script  type="text/javascript" src="assets/jquery-3.1.1.min.js"></script>
    <script  type="text/javascript" src="assets/jquery.validate.min.js"></script>
    <script  type="text/javascript" src="assets/additional-methods.min.js"></script>
    <script>
    $( document ).ready(function() {
        $("#cpdform").validate({
                rules: {
                    mversion: "required",
                    ext_zip: {
                        //extension: 'application/zip|application/octet-stream|application/x-zip|application/x-zip-compressed'
                        extension: 'zip'
                    }
                },
                messages: {
                    mversion: "Please select your magento version",
                    ext_zip: {
                        extension: "This file type cannot be uploaded. Only zip file allowed"
                    }
                },
                submitHandler: function(form) {
                    $(".loader").show();
                    form.submit();
                }
        }); 
    }); 
    </script>
	</head>
	<body>
		<div class="header-area">
			<div class="min-container">
				<div class="coded-header-left">
					<a target="_blank" href="https://www.mconnectmedia.com/"><img src="images/logo.png" width="230" height="39"></a>				
				</div>
				<div class="coded-header-right">
					<a  target="_blank" href="https://www.mconnectmedia.com/magento-2-development-solutions"> Magento 2 Solutions</a>
					<a target="_blank" href="https://www.mconnectmedia.com/ecommerce-consulting-services">eCommerce Services</a>
					<a target="_blank" href="https://www.mconnectmedia.com/magento-code-audit">Magento Code Audit</a>
					<a target="_blank" href="https://www.mconnectmedia.com/about-us">About Us</a>
					<a target="_blank" href="https://www.mconnectmedia.com/contact">Contact Us</a>				
				</div>
			</div>
		</div>
		<div class="loader"></div> 
        <section class="content-area">
            <?php 
            if($printresult==1 && !empty($result))
            {?>
                <div class="min-container">
                    <div class="responsive-tbl">
                        <table class="custom-table">
                            <thead>
                                <tr class="head-litle">
                                    <th colspan="2">Report</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach($result as $data)
                                {
                                    //echo '<pre>';print_r($data);echo '</pre>';
                                    foreach($data->file as $file)
                                    {
                                        if($iszip==1)
                                        {
                                            if(stripos($file['path'], $findfileinreport) === 0)
                                            {
                                                echo '<tr class="body-litle"><th>Your File Path</th><td>'.str_replace($findfileinreport,'',$file['path']).' - Line : ' .$file['line'].'</td></tr>';
                                            }
                                            else
                                            {
                                                echo '<tr class="body-litle"><th>Core File Path</th><td>'.str_replace(MAGENTO_FOLDER,'',str_replace("magento-".$version,'magento',$file['path'])).' - Line : ' .$file['line'].'</td></tr>';
                                            }                                                
                                        }else
                                        {
                                            if($file['path']!=$findfileinreport)
                                            {
                                                echo '<tr class="body-litle"><th>Core File Path</th><td>'.str_replace(MAGENTO_FOLDER,'',str_replace("magento-".$version,'magento',$file['path'])).' - Line : ' .$file['line'].'</td></tr>';
                                            }
                                        }                                            
                                    }
                                    echo '<tr class="body-litle"><th>Code Fragment</th><td><code>'.nl2br($data->codefragment).'</code></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            <?php } ?>
		</section>
		<section class="first-section-area">
			<div class="min-container">		
				<div class="first-left-content-area">
					<div class="first-left-content">
						<h2 class="title">Code Duplication Analysis Tool for Magento&reg; 2</h2>
                        
						<p class="title-text">Almost everybody aware of code duplication who wants to implement extension on Magento&reg; marketplace especially on Magento&reg; 2. This is an important part defined by Magento&reg; officials to prevent code duplication in any extension or template which are being requested to be uploaded on Magento&reg; marketplace or to be used any eCommerce shopping site</p>
						<p class="title-text">We found it important part of Magento&reg; extension approval process because our extension also rejected couple of time after lot of precaution. After multiple failure for same extension on marketplace with code duplication error (Code quality issues: CPD: This extension contains duplicated code), we made a decision to develop a tool that analyze Magento&reg; code snippets and module and find the duplicated code. We got it successful and very much excited to make it available for the Magento&reg;community for use.</p>
                        <p class="title-text">A question always blinks in, so what is the best use of this tool? As per the defined sections 3.1 and 9.1b of Magento&reg; agreement, we have to submit unique and errorless code on Magento&reg; marketplace where they analyzing the code to check duplication and if your extension or template fails to fulfills Magento&reg; guidelines, your extension will be rejected for further review or approval until you remove errors and duplications.</p>
						<p class="title-text">This tool check for duplicated code as per the Magento&reg; defined guidelines and if there is any duplication found it will show up in reports generated. It indexes the code snippets as well the entire code base od Magento&reg; extension and compares it with Magento&reg; standard core code to generate report for duplication issues.</p>
                        <p class="title-text">This is how this tool prevents the violation of Magento marketplace extension implementation Agreements Section 3.1 and 9.1b and saves the quality time spending on repeated upload and huge wait time of approval process.</p>
                        <p class="title-text"><strong>Team behind code duplication analysis program creation.</strong></p>
                        <div class="developers-area">
                            <ul class="developer-list">
                                <li>
									<img rel="nofollow" class="d-icon" src="images/YOGESH-TRIVEDI.png" width="60" height="60">
									<div class="d-content">
										<h4>YOGESH TRIVEDI<small>eCommerce Consultant </small></h4>
									</div>
								</li>
								<li>
									<img  rel="nofollow" class="d-icon" src="images/Jaimin-Sutariya.jpg" width="60" height="60">
									<div class="d-content">
										<h4>Jaimin Sutariya<small>Magento Certified Developer Plus</small></h4>
									</div>
								</li>
								<li>
									<img  rel="nofollow" class="d-icon" src="images/Nalin-Savaliya.jpg" width="60" height="60">
									<div class="d-content">
										<h4>Nalin Savaliya<small>Magento Certified Developer</small></h4>
									</div>
								</li>
								<li>
									<img rel="nofollow" class="d-icon" src="images/Ankur-Bhadania.jpg" width="60" height="60">
									<div class="d-content">
										<h4>Ankur Bhadania<small>Magento Developer</small></h4>
									</div>
								</li>
							</ul>
						</div>
						<!-- <div class="notes">
							<h4>NOTE:</h4>
							<ul class="notes-list">
								<li>Lorem Ipsum has been the industry's</li>
								<li>when an unknown printer</li>
								<li>It has survived not only five centuries</li>
							</ul>
						</div> -->
					</div>
				</div>
				<div class="first-right-area">
					<div class="first-right-form">
						<!--<h3>Code Duplication Investigator V1</h3>-->
						<form id="cpdform" action="" method="post" enctype="multipart/form-data">
                            <?php if(isset($message)): ?>
                                <div class="message alert-<?php echo $status; ?>">
                                    <?php echo $message; ?>
                                </div>
                            <?php endif; ?>
							<ul class="auditor-form">
								<li>
									<label for="">Select Your Magento version:</label>
									<select name="mversion" id="mversion">
										<!--<option value="2.0.0">2.0.0</option>
										<option value="2.0.1">2.0.1</option>-->
										<option value="2.0.2">2.0.2</option>
										<!--<option value="2.0.3">2.0.3</option>-->
										<option value="2.0.4">2.0.4</option>
										<option value="2.0.5">2.0.5</option>
										<option value="2.0.6">2.0.6</option>
										<option value="2.0.7">2.0.7</option>
										<option value="2.0.8">2.0.8</option>
										<option value="2.0.9">2.0.9</option>
										<option value="2.0.10">2.0.10</option>
										<option value="2.0.11">2.0.11</option>
										<option value="2.0.12">2.0.12</option>
										<option value="2.0.13">2.0.13</option>
										<option value="2.0.14">2.0.14</option>
										<option value="2.0.15">2.0.15</option>
										<option value="2.1.0">2.1.0</option>
										<option value="2.1.1">2.1.1</option>
										<option value="2.1.2">2.1.2</option>
										<option value="2.1.3">2.1.3</option>
										<option value="2.1.4">2.1.4</option>
										<option value="2.1.5">2.1.5</option>
										<option value="2.1.6">2.1.6</option>
										<option value="2.1.7">2.1.7</option>
										<option value="2.1.8">2.1.8</option>
									</select>
								</li>
								<li>
									<label for="">Upload extension zip:</label>
									<input type="file" name="ext_zip" id="ext_zip">
									
								</li>
								<li>
									<font class="or">OR</font>
								</li>
                                <li class="right-note">
									<h4>NOTE:</h4>
									<p> • Minimum number lines 13</p>
									<p> • Insert Starting php tag. ex-  	&lt;&#63;php</p>
									<p> • Only used PHP file. Not used for .phtml or .html</p>
                                    <p> • For accurate result please insert complete file code.</p>
                                    <p> • In some cases individual function may be not find.</p>
                                    <p> • For accurate result, We suggest to Upload extension zip.</p>
								</li>
								<li>
									<label for="">Enter Your Code:</label>
									<textarea id="code" name="code"  placeholder="Enter Your Code:" rows="8" cols="50"></textarea>
								</li>
								
                                <li>
								<input name="submit" type="submit" value="Begin Code Audit" class="new-btn">
                                </li>
                                <li class="right-note">
									<h4>Remember Please:</h4>
									<p> By using our tool, your code is safe because we have made provision to NOT keep your code or zip on our server.</p>
									
								</li>
							</ul>
                            <p class="title-text">Thank you for using our tool. </p>
                            <hr>
                            <p class="title-text"><strong>Have any query? </strong></p>
                            <p class="title-text">Please feel free to <a href="https://www.mconnectmedia.com/contact" target="_blank" >Contact Us</a> at any moment and from anywhere</p>
                            <div class="notes">
                                <h4>We Love to Share it with Magento&reg; Developer Community:</h4>
                                <ul class="notes-list">
                                    <li>We made this tool available for download on <a target="_blank" href="https://github.com/mconnectmedia/Code-Duplication-Analysis-Tool-for-Magento-2-Extension">Github</a> and also allocated a team to periodically check for any listed bug or issues from any individual or agency from Magento&reg; community. We’re best known for support and always ready for the same.</li>
                                </ul>
                            </div>
                            <p class="title-text"><strong>As said well, sharing is caring! </strong></p>
                            <p class="title-text lastp">Therefore, we humbly request you to share it on <!-- Twitter, Google+, LinkedIn, and Facebook --></p>
                            <div id="shareButtons">
                                <!-- Facebook -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u=http%3A//codeanalysis.labs.mconnectmedia.com/" target="_blank"><div class="icon Facebook"></div></a>
                                <!-- Google+ -->
                                <a href="https://plus.google.com/share?url=http://codeanalysis.labs.mconnectmedia.com/" target="_blank"><div class="icon Google"></div></a>
                                <!-- LinkedIn -->
                                <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=http://codeanalysis.labs.mconnectmedia.com/" target="_blank"><div class="icon LinkedIn"></div></a>
                                <!-- Twitter -->
                                <a href="https://twitter.com/share?url=http://codeanalysis.labs.mconnectmedia.com/&amp;text=Code Duplication Analysis Tool for Magento&reg; 2 by M-Connect Media" target="_blank"><div class="icon Twitter"></div></a>
                            </div>
						</form>
					</div>	
				</div>			
			</div>
		</div>
    </body>
</html>
