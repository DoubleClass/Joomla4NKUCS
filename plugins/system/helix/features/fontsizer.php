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
if ($this->getParam('fontsizer')) { ?>
<?php 
	$large = '14px';
	$small = '11px';
?>

<div class="font-sizer">
	<a class="btn_fs_big" title="Increase font size" rel="nofollow" href="#">A+</a>
	<a class="btn_fs_reset" title="Reset font size" rel="nofollow" href="#">R</a>
	<a class="btn_fs_small" title="Decrease font size" rel="nofollow" href="#">A-</a>
</div>
<?php
	$font_size  = isset($_COOKIE[$this->theme . '_font_size']) ? $_COOKIE[$this->theme . '_font_size'] : 'reset';
	if(isset($_GET['font_size'])) {
		setcookie($this->theme . '_font_size', $_GET['font_size'], time() + 3600*30, '/'); 
		$font_size = $_GET['font_size'];
	}
	if ($font_size=='big') {
		$this->API->addStyleDeclaration('body {font-size:' . $large . '}');	
	} elseif ($font_size=='small') {
		$this->API->addStyleDeclaration('body {font-size:' . $small . '}');	
	}
?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		document.getElement(".btn_fs_big").addEvent("click", function() {
			Cookie.write('<?php echo $this->theme . '_font_size' ?>', 'big'); 
			document.body.setStyle('font-size','<?php echo $large ?>');
		});
		document.getElement(".btn_fs_small").addEvent("click", function() {
			Cookie.write('<?php echo $this->theme . '_font_size' ?>', 'small'); 
			document.body.setStyle('font-size','<?php echo $small ?>');
		});
		document.getElement(".btn_fs_reset").addEvent("click", function() {
			Cookie.dispose('<?php echo $this->theme . '_font_size' ?>'); 
			document.body.setStyle('font-size','');
		});
	});
</script>
<?php }