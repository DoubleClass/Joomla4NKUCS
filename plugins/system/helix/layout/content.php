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
?>
<div id="sp-maincol" class="clearfix">
	<?php
		$this->addModules('slide1'); //slide1
		$this->addModules('user-top', 'sp_xhtml'); //user-top 
		$this->addModules('content1, content2, content3, content4', 'sp_xhtml', 'sp-content-grid-top'); //content1-content4
	?>
	<div class="clr"></div>
	<?php $this->addModules('inset1', 'sp_xhtml'); //inset1 ?>
	<div id="inner_content" class="clearfix"> <!--Component Area-->
		<?php 
			$this->addModules('feature-top', 'sp_xhtml'); //feature-top	
			$this->addModules('feature1,feature2,feature3,feature4', 'sp_xhtml', 'sp-feature-grid-top'); //feature1-feature4
			$this->addModules('feature-mid', 'sp_xhtml'); //feature-mid
			$this->addModules('feature5,feature6,feature7,feature8', 'sp_xhtml', 'sp-feature-grid-bottom'); //feature5-feature8	
			$this->addModules('feature-bottom', 'sp_xhtml'); //feature-bottom
		?>		
		<?php if (!$this->hideItem()): ?>
			<div class="sp-component-area clearfix">
				<div class="sp-inner clearfix">
					<?php $this->addModules('pathway'); //pathway position ?>
					<jdoc:include type="message" />
					<div class="sp-component-area-inner clearfix">
						<jdoc:include type="component" />
					</div>	
				</div>
			</div>
		<?php endif; ?>
		<?php
			$this->addModules('utility-top', 'sp_xhtml'); //utility-top 
			$this->addModules('utility1,utility2,utility3,utility4', 'sp_xhtml', 'sp-utility'); //utility1-utility4
			$this->addModules('utility-bottom', 'sp_xhtml'); //utility-bottom 
		?>
	</div>
	<?php 
		$this->addModules('inset2', 'sp_xhtml'); //inset2
	?>
	<div class="clr"></div>
	<?php 
		$this->addModules('content5,content6,content7,content8', 'sp_xhtml', 'sp-content-grid-bottom'); //content5-content8	
		$this->addModules('user-bottom', 'sp_xhtml'); //user-bottom 
	?>
</div>