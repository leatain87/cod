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
   if(imgenHelper::getParamValue('cacheImages','yes') == 'yes')
   {
      imagejpeg($this->image, $this->fileout, $this->quality);	
   }
   imagejpeg($this->image, null, $this->quality);
   imagedestroy( $this->image );
}

?>