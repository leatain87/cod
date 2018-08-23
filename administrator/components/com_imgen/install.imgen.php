<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');

class com_imgenInstallerScript
{

  public function __constructor($adapter)
  {
	  
	  
  }
  

  public function preflight($route, $adapter)
  {
	  
	  
  }

  public function postflight($route, $adapter)
  {
	  
  }
  
  
  public function install(JAdapterInstance $adapter)
  {
		//creates the default cached images folder 
		
		$files = array("index.html","copyright-sample.png","restricted-image.png","blank.gif"); 
		$destpath = '';
		$component = "imgen";
		$sourcepath = '../components/com_'.$component.'/assets/images/';
		
		if(is_writable( "../images")) {
			$destpath = "../images/".$component.'/';
		}
		else
		{
		  echo 'Your images folder is not writable, so the image cache folder could not be created. No need to worry, you will just need to create it manually';
		  return;
		}
		
		//echo 'cwd'.getcwd() . "\n";
		
		if($destpath != '')
		{
			if( is_dir($destpath) || mkdir($destpath, 0755))
			{
			  echo "<ul>";
			  foreach ( $files as $f )
			  {
				  $file = $sourcepath.$f;
				  $newfile = $destpath.$f;
						  
				 // echo 'cwd'.getcwd() . "\n";
		
				  
				  if (!copy($file, $newfile)) {
					echo "<li>failed to copy $newfile</li>\n";
				  }
				  else
				  {
					echo "<li>successfully copied $newfile</li>\n";
				  }
		
			  
			  }
			  echo "</ul>";
		 
			}
			else
			{
			  echo "<p>Warning! Failed to create folder ".$destpath."</p>";
			}
		}
		else
		{
		   echo "<p>Warning! folder " . $destpath . " is not writable so could not copy images.</p>";
		
		}
		
	 }
	 
        public function update($adapter)
		{
			//creates the default cached images folder 
			
			$files = array("index.html","copyright-sample.png","restricted-image.png","blank.gif"); 
			$destpath = '';
			$component = "imgen";
			$sourcepath = '../components/com_'.$component.'/assets/images/';
			
			if(is_writable( "../images")) {
				$destpath = "../images/".$component.'/';
			}
			else
			{
			  echo 'Your images folder is not writable, so the image cache folder could not be created. No need to worry, you will just need to create it manually';
			  return;
			}
			
			
			if($destpath != '')
			{
				if( is_dir($destpath) || mkdir($destpath, 0755))
				{
				  echo "<ul>";
				  foreach ( $files as $f )
				  {
					  $file = $sourcepath.$f;
					  $newfile = $destpath.$f;
							  
					 if(file_exists($newfile))
					 {
						continue; 
					 }
			
					  
					  if (!copy($file, $newfile)) {
						echo "<li>failed to copy $newfile</li>\n";
					  }
					  else
					  {
						echo "<li>successfully copied $newfile</li>\n";
					  }
			
				  
				  }
				  echo "</ul>";
			 
				}
				else
				{
				  echo "<p>Warning! Failed to create folder ".$destpath."</p>";
				}
			}
			else
			{
			   echo "<p>Warning! folder " . $destpath . " is not writable so could not copy images.</p>";
			
			}
	}
		
	 public function uninstall($adapter)
	 {
		 
	 }

}
?>