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

defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');

class JFormFieldAsset extends JFormField
{
	protected	$type = 'Asset';
	
	protected function getInput() {
		$plg_path = JURI::root().'/plugins/system/helix/elements/';	

		if(JVERSION>2.5){
			?>
			<script type="text/javascript">
			jQuery(function($){
				setTimeout(function(){
					$('.chzn-done').css('display', '').next('.chzn-container').remove();
				}, 100);
			});

			</script>

			<?php
		}
		
		if($this->element['assettype'] == 'js') {

			if(version_compare(JVERSION,'3.2.0','ge')) {

			} else {
				JHtml::_('script', $plg_path . $this->element['filename'], false, true);
			}
			
		} else {
			JHtml::_('stylesheet', $plg_path . $this->element['filename'], false, true);
		}
		
	}
}