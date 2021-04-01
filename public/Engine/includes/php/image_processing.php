<?php
/**
*	ImageHandler - ResizeToDimension()
*
* 	Resizes an image to fit into a specifie dimension
*
* 	EXAMPLE USAGE:
*
* 	$ImageHandler->ResizeToDimension(200, "file.jpg", "png", "images");
*
*	@param	int			$dimension - dimension to fit into
*	@param	string		$source - image source
*	@param	string		$extension - image source file type
*	@param	string		$destination - destination directory
*
*/

function ResizeToDimension($dimension, $source, $extension, $destination)
{

	//get the image size
	$size = getimagesize($source);

	//determine dimensions
	$width = $size[0];
	$height = $size[1];

	//determine what the file extension of the source
	//image is
	switch($extension)
	{

		//its a gif
		case 'gif': case 'GIF':
			//create a gif from the source
			$sourceImage = imagecreatefromgif($source);
			break;
		case 'jpg': case 'JPG': case 'jpeg':
			//create a jpg from the source
			$sourceImage = imagecreatefromjpeg($source);
			break;
		case 'png': case 'PNG':
			//create a png from the source
			$sourceImage = imagecreatefrompng($source);
			break;

	}

	// find the largest dimension of the image
	// then calculate the resize perc based upon that dimension
	$percentage = ( $width >= $height ) ? 100 / $width * $dimension : 100 / $height * $dimension;

	// define new width / height
	$newWidth = $width / 100 * $percentage;
	$newHeight = $height / 100 * $percentage;

	// create a new image
	$destinationImage = imagecreatetruecolor($newWidth, $newHeight);

	// copy resampled
	imagecopyresampled($destinationImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    //exif only supports jpg in our supported file types
    if ($extension == "jpg" || $extension == "jpeg")
	{

		//fix photos taken on cameras that have incorrect
		//dimensions
		$exif = exif_read_data($source);

		//get the orientation
		
		$ort = (ISSET($exif['Orientation'])) ? $exif['Orientation'] : 1;

		//determine what oreientation the image was taken at
		switch($ort)
	    {

	        case 2: // horizontal flip

	            $this->ImageFlip($dimg);

	        	break;

	        case 3: // 180 rotate left

	            $destinationImage = imagerotate($destinationImage, 180, -1);

	        	break;

	        case 4: // vertical flip

	            $this->ImageFlip($dimg);

	       		break;

	        case 5: // vertical flip + 90 rotate right

	            $this->ImageFlip($destinationImage);

	            $destinationImage = imagerotate($destinationImage, -90, -1);

	        	break;

	        case 6: // 90 rotate right

	            $destinationImage = imagerotate($destinationImage, -90, -1);

	        	break;

	        case 7: // horizontal flip + 90 rotate right

	            $this->ImageFlip($destinationImage);

	            $destinationImage = imagerotate($destinationImage, -90, -1);

	        	break;

	        case 8: // 90 rotate left

	            $destinationImage = imagerotate($destinationImage, 90, -1);

	        	break;

	    }

	}

	// create the jpeg
	return imagejpeg($destinationImage, $destination, 100);

}

/**
*	ImageHandler - ImageFlip()
*
* 	Resizes an image to set width and height
*
* 	EXAMPLE USAGE:
*
* 	$ImageHandler->Resize(200, "file.jpg", "png", "images");
*
*	@param	string		$image (image to flip)
*	@param	int			$x
*	@param	int			$y
*	@param	int			$width
*	@param	int			$height
*
*/
//an odd error occured beacuse of this function shutting the system down
/*function ImageFlip(&$image, $x = 0, $y = 0, $width = null, $height = null)
{

    if ($width  < 1) $width  = imagesx($image);
    if ($height < 1) $height = imagesy($image);

    // Truecolor provides better results, if possible.
    if (function_exists('imageistruecolor') && imageistruecolor($image))
    {

        $tmp = imagecreatetruecolor(1, $height);

    }
    else
    {

        $tmp = imagecreate(1, $height);

    }

    $x2 = $x + $width - 1;

    for ($i = (int)floor(($width - 1) / 2); $i >= 0; $i--)
    {

        // Backup right stripe.
        imagecopy($tmp, $image, 0, 0, $x2 - $i, $y, 1, $height);

        // Copy left stripe to the right.
        imagecopy($image, $image, $x2 - $i, $y, $x + $i, $y, 1, $height);

        // Copy backuped right stripe to the left.
        imagecopy($image, $tmp, $x + $i,  $y, 0, 0, 1, $height);

    }

    imagedestroy($tmp);

    return true;

}*/
function make_thumbnail($new_file_path, $thumb_file_path, $thumb_size, $resize_dimension = 'width')
{
	//return some image data, like size
	
	//we need to send resizeToDimesion the largest size....
	//get the image size
	$size = getimagesize($new_file_path);

	//determine dimensions
	$width = $size[0];
	$height = $size[1];
	
	/*ex
	200x300 image
	
	resize to 100 width for a 100x150 image
	
	300 x 200
	
	resize to 100 width for a 100x67 image
	*/
	
	if($resize_dimension == 'width')
	{
		if( $width >= $height ) 
		{
			$scale = (100 / $width) *$thumb_size;
			$resize_value = $thumb_size; //($width/100) *$scale; 
			$return_data['width'] = ($width/100) *$scale;
			$return_data['height'] =  ($height/100) *$scale;
		}
		else
		{
		 	$scale = (100 / $width) *$thumb_size; 
			$resize_value = ($height/100) *$scale; 

		}
	}
	else
	{
		//assuming height
		if( $height >= $width ) 
		{
			$scale = (100 / $height) *$thumb_size; 
			$resize_value = $thumb_size; 
		}
		else
		{
		 	$scale = (100 / $height) *$thumb_size; 
			$resize_value = ($width/100) *$scale; 

		}
	}
	$return_data['width'] = round(($width/100) *$scale,0);
	$return_data['height'] =  round(($height/100) *$scale,0);

	
	ResizeToDimension($resize_value, $new_file_path, "jpg", $thumb_file_path);
	

	return $return_data;
	
}


    /************************************************************\
    
        IPTC EASY 1.0 - IPTC data manipulator for JPEG images
            
        All reserved www.image-host-script.com
        
        Sep 15, 2008
    
    \************************************************************/

/*A class I recently wrote for manipulating IPTC data in an jpeg images. It does the job for editing exisiting data too, in easy manner. It is just a compilation of examples into single class.*/

    
   /*Example read copyright string:

$i = new iptc("test.jpg");
echo $i->get(IPTC_COPYRIGHT_STRING); 

Update copyright statement:
$i = new iptc("test.jpg");
echo $i->set(IPTC_COPYRIGHT_STRING,"Here goes the new data"); 
$i->write();

NOTE1: Data may be anything, even a binary file. I have so far tested and embedded an MS-Excel file directly to jpeg and it worked just perfect.

NOTE2: The writing purpose, it uses GD Library.

*/ 

    DEFINE('IPTC_OBJECT_NAME', '005');
    DEFINE('IPTC_EDIT_STATUS', '007');
    DEFINE('IPTC_PRIORITY', '010');
    DEFINE('IPTC_CATEGORY', '015');
    DEFINE('IPTC_SUPPLEMENTAL_CATEGORY', '020');
    DEFINE('IPTC_FIXTURE_IDENTIFIER', '022');
    DEFINE('IPTC_KEYWORDS', '025');
    DEFINE('IPTC_RELEASE_DATE', '030');
    DEFINE('IPTC_RELEASE_TIME', '035');
    DEFINE('IPTC_SPECIAL_INSTRUCTIONS', '040');
    DEFINE('IPTC_REFERENCE_SERVICE', '045');
    DEFINE('IPTC_REFERENCE_DATE', '047');
    DEFINE('IPTC_REFERENCE_NUMBER', '050');
    DEFINE('IPTC_CREATED_DATE', '055');
    DEFINE('IPTC_CREATED_TIME', '060');
    DEFINE('IPTC_ORIGINATING_PROGRAM', '065');
    DEFINE('IPTC_PROGRAM_VERSION', '070');
    DEFINE('IPTC_OBJECT_CYCLE', '075');
    DEFINE('IPTC_BYLINE', '080');
    DEFINE('IPTC_BYLINE_TITLE', '085');
    DEFINE('IPTC_CITY', '090');
    DEFINE('IPTC_PROVINCE_STATE', '095');
    DEFINE('IPTC_COUNTRY_CODE', '100');
    DEFINE('IPTC_COUNTRY', '101');
    DEFINE('IPTC_ORIGINAL_TRANSMISSION_REFERENCE',     '103');
    DEFINE('IPTC_HEADLINE', '105');
    DEFINE('IPTC_CREDIT', '110');
    DEFINE('IPTC_SOURCE', '115');
    DEFINE('IPTC_COPYRIGHT_STRING', '116');
    DEFINE('IPTC_CAPTION', '120');
    DEFINE('IPTC_LOCAL_CAPTION', '121');

    class iptc222 {
        var $meta=Array();
        var $hasmeta=false;
        var $file=false;
        
        
        function iptc($filename) {
            $size = getimagesize($filename,$info);
            $this->hasmeta = isset($info["APP13"]);
            if($this->hasmeta)
                $this->meta = iptcparse ($info["APP13"]);
            $this->file = $filename;
        }
        function set($tag, $data) {
            $this->meta ["2#$tag"]= Array( $data );
            $this->hasmeta=true;
        }
        function get($tag) {
            return isset($this->meta["2#$tag"]) ? $this->meta["2#$tag"][0] : false;
        }
        
        function dump() {
            print_r($this->meta);
        }
        function binary() {
            $iptc_new = '';
            foreach (array_keys($this->meta) as $s) {
                $tag = str_replace("2#", "", $s);
                $iptc_new .= $this->iptc_maketag(2, $tag, $this->meta[$s][0]);
            }        
            return $iptc_new;    
        }
        function iptc_maketag($rec,$dat,$val) {
            $len = strlen($val);
            if ($len < 0x8000) {
                   return chr(0x1c).chr($rec).chr($dat).
                   chr($len >> 8).
                   chr($len & 0xff).
                   $val;
            } else {
                   return chr(0x1c).chr($rec).chr($dat).
                   chr(0x80).chr(0x04).
                   chr(($len >> 24) & 0xff).
                   chr(($len >> 16) & 0xff).
                   chr(($len >> 8 ) & 0xff).
                   chr(($len ) & 0xff).
                   $val;
                   
            }
        }    
        function write() {
            if(!function_exists('iptcembed')) return false;
            $mode = 0;
            $content = iptcembed($this->binary(), $this->file, $mode);    
            $filename = $this->file;
                
            @unlink($filename); #delete if exists
            
            $fp = fopen($filename, "w");
            fwrite($fp, $content);
            fclose($fp);
        }    
        
        #requires GD library installed
        function removeAllTags() {
            $this->hasmeta=false;
            $this->meta=Array();
            $img = imagecreatefromstring(implode(file($this->file)));
            @unlink($this->file); #delete if exists
            imagejpeg($img,$this->file,100);
        }
    };

function tag_image($file_name)
{
	//$iptc = new iptc($new_file_path);
	//echo $iptc->get(IPTC_COPYRIGHT_STRING); 

	//Update copyright statement:
	$iptc = new iptc($file_name);
	//echo $iptc->set(IPTC_COPYRIGHT_STRING,"Here goes the new data"); 
	

	
	$iptc->set(IPTC_CITY,"Pittsford"); 
	$iptc->set(IPTC_PROVINCE_STATE,"New York"); 
	$iptc->set(IPTC_COUNTRY,"USA"); 
	$iptc->set(IPTC_BYLINE_TITLE,"Images by Embrasse-moi.com"); 
	$iptc->set(IPTC_HEADLINE,"Photography by Embrasse-moi.com"); 
	$iptc->set(IPTC_CREDIT,"Craig Iannazzi 1 N Main Street, Pittsford, NY 14534"); 
	$iptc->set(IPTC_KEYWORDS,"embrasse-moi bras clothing footwear swimwear"); 
	
	$iptc->set(IPTC_CAPTION,"Embrasse-moi.com"); 
	$iptc->set(IPTC_SOURCE,"Craig Iannazzi"); 
	$iptc->set(IPTC_COPYRIGHT_STRING,"Copyright Craig and Kristine Iannazzi, Embrasse-Moi 1 N Main Street Pittsford, NY 14534. All rights reserved."); 

	$iptc->write();
}
?>