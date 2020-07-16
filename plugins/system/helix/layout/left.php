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

if ($this->countModules ( 'left or left1 or left2 or left-mid or left3 or left4 or left-bottom' )) {
	define('_LEFT', 1);
	?>
	<div id="sp-leftcol" class="clearfix">
		<?php 
			$this->addModules('left','sp_xhtml');//position left
			$this->addModules('left1,left2','sp_xhtml','sp-left-gridtop');//position left1 and left2
			$this->addModules('left-mid','sp_xhtml');//position left-mid
			$this->addModules('left3,left4','sp_xhtml','sp-left-gridbottom');//position left3 and left4	
			$this->addModules('left-bottom','sp_xhtml');//position left-bottom 			
		?>
	</div>
	<?php
}