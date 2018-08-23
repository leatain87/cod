<?php
/**
 * @version		1.1.0 imgen $
 * @package		imgen
 * @copyright	Copyright © 2011 - All rights reserved.
 * @license		GNU/GPL
 * @author		Fiona Coulter
 * @author mail	joomla - at - iswebdesign.co.uk
 * @website		http://www.spiralscripts.co.uk
 *
 *

 */

// no direct access
defined('_JEXEC') or die('Restricted access');


if($this->caching)
{
	readfile($this->fileout); //Reads a file and writes it to the output buffer
}
else
{
	 //for png quality ranges from 0 (best) to 9 (worst)
	 $q = 10 - min(intval($this->quality/10),1);
	 
	// Turn off alpha blending and set alpha flag
	imagealphablending($this->image, false);
	imagesavealpha($this->image, true);
    if(imgenHelper::getParamValue('cacheImages','yes') == 'yes')
    {
        imagepng($this->image, $this->fileout, $q);	
    }
    imagepng($this->image, null, $q);
    imagedestroy( $this->image );
}



?>