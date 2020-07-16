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
if ($this->getParam('bootstrap',0)){
	if (JVERSION>=3) {
		// Add JavaScript Frameworks
		JHtml::_('bootstrap.framework');		
		// Load optional rtl Bootstrap css and Bootstrap bugfixes
		JHtmlBootstrap::loadCss();
	} else {
		$this->addCSS('bootstrap.min.css,bootstrap-responsive.min.css,bootstrap-extended.css');
		$this->addJQuery();
		$this->addJS('bootstrap.min.js');
	}
}