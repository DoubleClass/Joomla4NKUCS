<?php
/*---------------------------------------------------------------
# Package - Helix Framework  
# ---------------------------------------------------------------
# Author - JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2013 JoomShaper.com. All Rights Reserved.
# license - PHP files are licensed under  GNU/GPL V2
# license - CSS  - JS - IMAGE files  are Copyrighted material 
# Websites: http://www.joomshaper.com
-----------------------------------------------------------------*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

if ($this->countModules ( 'right or right1 or right2 or right-mid or right3 or right4 or right-bottom' )) {
	define('_RIGHT', 1);
	
	?>
	<div id="sp-rightcol">
			<?php 
				$this->addModules('right','sp_xhtml');//position right
				$this->addModules('right1,right2','sp_xhtml','sp-right-gridtop');//position right1 and right2
				$this->addModules('right-mid','sp_xhtml');//position right-mid
				$this->addModules('right3,right4','sp_xhtml','sp-right-gridbottom');//position right3 and right4	
				$this->addModules('right-bottom','sp_xhtml');//position right-bottom 				
			?>
	</div>
	<?php
}