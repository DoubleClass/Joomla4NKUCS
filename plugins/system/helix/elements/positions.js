/*---------------------------------------------------------------
# Package - Helix Framework  
# ---------------------------------------------------------------
# Author - JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2013 JoomShaper.com. All Rights Reserved.
# license - PHP files are licensed under  GNU/GPL V2
# license - CSS  - JS - IMAGE files  are Copyrighted material 
# Websites: http://www.joomshaper.com
-----------------------------------------------------------------*/
window.addEvent("domready",function(){
	var posID=document.id('jform_position');
	var sp_pos = document.id('sp_pos');
	
	//Inject Template and Position dropdown list
	sp_pos.injectAfter(posID.getParent()).style.display=null;
	
	//Change module position
	sp_pos.addEvents({
		change: function(){
			posID.value=sp_pos.value;
		},
		keyup: function(){
			posID.value=sp_pos.value;
		}
	});
});