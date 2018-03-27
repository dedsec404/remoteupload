<?php
// maximum execution time in seconds
set_time_limit (0);
function progressCallback( $resource, $download_size, $downloaded_size, $upload_size, $uploaded_size )
{
	static $previousProgress = 0;
    
    if ( $download_size == 0 )
        $progress = 0;
    else
        $progress = round(($downloaded_size / $download_size) * 100,1) ;
    
    if ( $progress > $previousProgress)
    {
        $previousProgress = $progress;
        $fp = fopen( 'progress.txt', 'w' );
        fwrite( $fp, "$progress" );
        fclose( $fp );
    }
	
}
if (isset($_POST['act'])){
	$action = $_POST['act'];
	if($action == "sendlink"){
		// folder to save downloaded files to. must end with slash
		$destination_folder = 'files/';
		$url = $_POST['url'];
		$newfname = $destination_folder . basename($url);
		$targetFile = fopen( $newfname, 'w' );
		$ch = curl_init( $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
		curl_setopt( $ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback' );
		curl_setopt( $ch, CURLOPT_FILE, $targetFile );
		curl_exec( $ch );
		fclose( $targetFile );
		exit;
	}
	if($action == "getpgb"){
		$fp = fopen( 'progress.txt', 'r' );
		if(!$fp)die("0");
		$data = fread( $fp, 50 );
		echo $data;
		fclose( $fp );
		exit;
	}
}
?>
<head>
<style>
#myProgress {
    position: relative;
    width: 400px;
    height: 30px;
    background-color: grey;
}
#myBar {
    position: absolute;
    width: 0%;
    height: 100%;
    background-color: green;
}
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script>
function customRequest(u,d) {
   var promise = $.ajax({
     type: 'post',
     data: d,
     url: u
   })
   .done(function (responseData, status, xhr) {
       // preconfigured logic for success
   })
   .fail(function (xhr, status, err) {
      //predetermined logic for unsuccessful request
   });

   return promise;
}


function updateProgress(data) {
    var elem = document.getElementById("myBar"); 
	elem.style.width = data + '%'; 
}
var myCallback;
function StopProgressBar() {
    clearInterval(myCallback);
	alert("Download Complete");
	var elem = document.getElementById("myBar"); 
	elem.style.width = '0%'; 
}
function StartProgressBar() {
    myCallback = setInterval(function(){ 
	customRequest('index.php', {'act': 'getpgb'}).done(function (data) {
	   updateProgress(data);
	   if(data == "100")StopProgressBar();
	});
	}, 1000);
}
function StartUpload()
{
var url = document.getElementById("formpost").url.value;
StartProgressBar();
customRequest('index.php', {'act': 'sendlink', 'url': url}).done(function (data) {
	});
	return false;
}
</script>
</head>
<title>Priv8 Remote Upload</title>
<center>
<a href="http://fb.com/Akikazu.kun" title="Wellcome To this Site">
			
<img src="https://s32.postimg.org/z93ciflyd/Untitled.png" alt="Upload And Download">
		</a>
<div id="myProgress">
  <div id="myBar"></div>
</div>
<br>
</br</p></br</p><form id="formpost" method="post" action="index.php" onsubmit="return StartUpload()">
<input type="hidden" name="act" value="sendlink">
URL : <input name="url" /><br>
<input name="submit" type="submit" />
</form>

<h3 id="status"></h3>
<p id="total"></p>
<br>
<b>Remote Upload By:</b>
</p> Akikazu Ashida
</center>
