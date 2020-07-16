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
	if (($menu = $this->loadMenu())) 
	echo '<div class="clr"></div><div id="hornav" class="clearfix">';
		$menu->showMenu ();						
	echo '</div>';

	if (($this->getMenu()=='split') && $menu->hasSub() || $this->getMenu()=='drop') {	
		echo '<div class="clr"></div><div id="sublevel" class="clearfix">';
			$menu->showMenu (1);
		echo '</div>';			
	}

	if (($this->getMenu()=='split' && $menu->hasSub()) || $this->getMenu()=='drop') {
		$sublevel=1;
	} else {
		$sublevel=0;
	}	

	$this->addCSS('menu.css');
	if ($this->getMenu()=='drop') {
		$this->addInlineCSS('#sublevel ul.level-1 {display:none}');
		$this->addJS('dropline.js');
	}
	$this->addJS('menu.js');
?>
<script type="text/javascript">
//<![CDATA[
	window.addEvent('domready',function(){
		<?php
			if ($this->getParam('wrapper_limit','body')=='body') {
				$limit = "$(document.body)";
			} else {
				$limit	= "document.getElement('.sp-wrap')";
			}
		?>
		var	limits = <?php echo $limit ?>;
		items_v = [], items_h = [];

		$$('div.submenu').each(function (el) {
			if (el.getParent().getParent().hasClass('level-<?php echo $sublevel ?>')) {
				items_v.push(el);
			} else {
				items_h.push(el);
			}
		});

		new SPMenu(items_v, {
			direction: '<?php echo strtoupper($this->getDirection()) ?>',
			bound: limits,
			fxOptions: {
				transition: Fx.Transitions.<?php echo $this->getParam('menu_transition','Sine.easeOut') ?>,
				duration: <?php echo $this->getParam('menu_duration',400) ?>
			},
			animation: '<?php echo $this->getParam('menu_animation','slide') ?>',
			mode: 'vertical',
			offset:{x:<?php echo $this->getParam('init_x',0) ?>, y: <?php echo $this->getParam('init_y',0) ?>}
		});

		new SPMenu(items_h, {
			direction: '<?php echo strtoupper($this->getDirection()) ?>',
			bound: limits,
			fxOptions: {
				transition: Fx.Transitions.<?php echo $this->getParam('menu_transition','Sine.easeOut') ?>,
				duration: <?php echo $this->getParam('menu_duration',400) ?>
			},
			animation: '<?php echo $this->getParam('menu_animation','slide') ?>',
			mode: 'horizontal',
			offset: {x: <?php echo $this->getParam('sub_x',0) ?>, y: <?php echo $this->getParam('sub_y',0) ?>}
		});
	});
//]]>
</script>