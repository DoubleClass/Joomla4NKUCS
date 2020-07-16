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

jimport('joomla.filesystem.file');
class helixHelper {
	var $API;
	var $helixPath;
	var $helixUrl;
	var $theme;
	var $themePath;
	var $themeUrl;
	var $basePath;
	var $baseUrl;
	var $style;
	var $direction;
	
	//initialize 
    function __construct($_this){
		$this->API=$_this;
		$this->theme=$_this->template;
		$this->basePath=JPATH_BASE;
		$this->baseUrl=$_this->baseurl;
		$this->themePath=$this->basePath .DIRECTORY_SEPARATOR. 'templates' .DIRECTORY_SEPARATOR. $this->theme;	
		$this->themeUrl=$this->baseUrl . '/templates/'. $this->theme;		
		$this->helixPath=JPATH_PLUGINS .DIRECTORY_SEPARATOR. 'system' .DIRECTORY_SEPARATOR.'helix';		
		$this->helixUrl= $this->baseUrl . '/plugins/system/helix';
    }
	
	//get parameter
	function getParam($paramName, $default=null){
		return $this->API->params->get($paramName, $default);
	}
	
	//set parameter
	function setParam($paramName, $paramValue){
		return $this->API->params->set($paramName, $paramValue);
	}
	
	function import($path) {//import file
		$path=str_replace( '.', DIRECTORY_SEPARATOR, $path );
		$filepath=$path . '.php';
		
		if(JFile::exists('templates'. DIRECTORY_SEPARATOR. $this->theme . DIRECTORY_SEPARATOR . $filepath)) { //cheack template path
            require_once('templates'. DIRECTORY_SEPARATOR. $this->theme . DIRECTORY_SEPARATOR . $filepath);
		} elseif(JFile::exists($this->helixPath . DIRECTORY_SEPARATOR . $filepath)) { //if not found, then check from helix path
           require_once($this->helixPath . DIRECTORY_SEPARATOR . $filepath);
		} 
	}
		
	function layout() {
		$layout_type  = isset($_COOKIE[$this->theme . '_layout']) ? $_COOKIE[$this->theme . '_layout'] : $this->API->params->get('layout_type');
		if(isset($_GET['layout']) AND is_numeric($_GET['layout'])) {
			setcookie($this->theme . '_layout', $_GET['layout'], time() + 3600, '/'); 
			$layout_type = $_GET['layout'];
		}
		return $layout_type;	
	}
	
	/**
	 * Load mainbody
	 *
	 * @since	1.0
	 * @update	1.9
	 */	
    function loadLayout() {//load mainbody layout
		$fixed_height 		= $this->getParam('fixed_column_height', 0);
		$width_unit 		= $this->getParam('width_unit', "percent");
		$left_width			= (int) $this->getParam('left_width',25);
		$right_width		= (int) $this->getParam('right_width',25);
		$main_width			= (int) $this->getParam('main_width',960);
		$inset1	 			= (int) $this->getParam('inset1_width',20);
		$inset2				= (int) $this->getParam('inset2_width',20);
		$unit 				= ($width_unit == "percent") ? "%" : "px"; 

		if ($this->layout()==2) {
			$this->import('layout.left');
			$this->import('layout.right');
			$this->import('layout.content');
		} elseif ($this->layout()==3) {
			$this->import('layout.content');	
			$this->import('layout.left');
			$this->import('layout.right');
		} else {
			$this->import('layout.left');
			$this->import('layout.content');
			$this->import('layout.right');	
		}
		
		$width = ($width_unit=="percent") ? 100 : $main_width;
		
		if (defined('_LEFT') && defined('_RIGHT')) {
			$content_width	= ($width -($left_width+$right_width));
		} elseif (defined('_LEFT') || defined('_RIGHT')) {
			$content_width	= ($width -(defined('_LEFT') ? $left_width : $right_width));
		} else {
			$content_width	= $width;
		}

		$c_width = ($width_unit=="percent") ? 100 : $content_width;

		//Calculate inset1 and inset2 width	
		if ($this->countModules('inset1 and inset2')) {
			$inner_width	= ($c_width -($inset1+$inset2));
		} elseif ($this->countModules('inset1 or inset2')) {
			$inner_width	= ($c_width -($this->countModules('inset1') ? $inset1 : $inset2));
		} else {
			$inner_width	= $c_width;
		}
		
		$this->API->addStyleDeclaration('.sp-wrap {width: ' . $main_width . 'px;}
		#sp-leftcol {width: ' . $left_width . $unit .'}
		#sp-rightcol { width: ' . $right_width . $unit .'}
		#sp-maincol {width:' . $content_width . $unit .'}');	
		$this->API->addStyleDeclaration('#inner_content {width: ' . $inner_width . $unit .';}
		#sp-inset1 {width: ' . $inset1 . $unit .'}#sp-inset2 { width: ' . $inset2 . $unit .'}');	
		
		//Equal Height - added version 1.9
		if ($fixed_height) {
			$this->addJS('equalheight.js'); 
			$this->addInlineJS('function fixHeight_main () {
				var equalizer_main = new Equalizer("#sp-leftcol, #sp-maincol, #sp-rightcol").equalize("height");
			}
			window.addEvent ("load", function () {
				fixHeight_main.delay (300);
			});
			window.addEvent ("resize", function () {
				fixHeight_main();
			});');
		}		
	
	}
	
	/**
	 * function to add multiple features
	 *
	 * @since	1.9
	 */	
	function addFeatures($features) {
		$features 	= explode(",",$features);
		foreach ($features as $feature) {
			$feature = trim($feature);//remove whitespace
			$this->import("features.$feature");
		}
	}
	
	/**
	 * Add feature function
	 *
	 * @since	1.0
	 */	
	function addFeature($feature) {
		$this->import("features.$feature");
	}	
	
	function getFonts () {//function to add web and normal type font
		if ($this->getParam('body_selectors')!='') {
			$body_font=explode('|',$this->getParam('body_font'));
			$body_selectors=$this->getParam('body_selectors');
			$body_font_type=$body_font[0];
			$body_sfont_family=$body_font[1];
			$body_gfont_family=$body_font[2];
			($body_font_type=='google') ? $this->addGoggleFonts($body_gfont_family,$body_selectors) : $this->addStandardFonts($body_sfont_family,$body_selectors);
		}

		if ($this->getParam('header_selectors')!='') {
			$header_font=explode('|',$this->getParam('header_font'));
			$header_selectors=$this->getParam('header_selectors');
			$header_font_type=$header_font[0];
			$header_sfont_family=$header_font[1];
			$header_gfont_family=$header_font[2];
			($header_font_type=='google') ? $this->addGoggleFonts($header_gfont_family,$header_selectors) : $this->addStandardFonts($header_sfont_family,$header_selectors);
		}

		if ($this->getParam('other_selectors')!='') {
			$other_font=explode('|',$this->getParam('other_font'));
			$other_selectors=$this->getParam('other_selectors');
			$other_font_type=$other_font[0];
			$other_sfont_family=$other_font[1];
			$other_gfont_family=$other_font[2];
			($other_font_type=='google') ? $this->addGoggleFonts($other_gfont_family,$other_selectors) : $this->addStandardFonts($other_sfont_family,$other_selectors);
		}
	}
	
	function addStandardFonts ($font_family,$selectors) {//standard fonts
		$this->API->addStyleDeclaration("$selectors { font-family: " . $font_family . " }");
	}

	function addGoggleFonts ($font_family,$selectors) {//Google fonts
		$this->API->addStyleSheet('http://fonts.googleapis.com/css?family='.str_replace(" ","+",$font_family));
		$googlefont = $font_family;
		if(stripos($googlefont, ':') !== FALSE) {
			$gfont_cut = stripos($googlefont, ':');
			$googlefont = substr($googlefont, 0, $gfont_cut);
		}
		$this->API->addStyleDeclaration("$selectors { font-family: '" . str_replace("+"," ",$googlefont) . "', 'Helvetica', arial, serif; }");
	}
	
	/**
	 * Add custom google font
	 *
	 * @since	1.9.1
	 */	
	function addGFonts($font_family) {
		$fonts = explode(',', $font_family);
		foreach ($fonts as $font) {
			$this->API->addStyleSheet('http://fonts.googleapis.com/css?family='.trim($font));
		}
	}
	
	function addJS ($srcs) {//add javascript
		$srcs = explode (',',$srcs);
		if (!is_array($srcs)) $srcs = array($srcs);   
		foreach ($srcs as $src) {
			if(JFile::exists($this->themePath .DIRECTORY_SEPARATOR. 'js' .DIRECTORY_SEPARATOR. $src)) { //cheack template path
				$this->API->addScript($this->themeUrl . '/js/' . $src);
			} elseif(JFile::exists($this->helixPath . '/js/' . $src)) { //if not found, then check from helix path
			   $this->API->addScript($this->helixUrl . '/js/' . $src);
			}		
		}
	}
	
	function addCSS ($srcs) {//Add stylesheets
		$srcs = explode (',',$srcs);
		if (!is_array($srcs)) $srcs = array($srcs);   
		foreach ($srcs as $src) {
			if(JFile::exists($this->themePath .DIRECTORY_SEPARATOR. 'css' .DIRECTORY_SEPARATOR. $src)) { //cheack template path
				$this->API->addStyleSheet($this->themeUrl . '/css/' . $src);
			} elseif(JFile::exists($this->helixPath . '/css/' . $src)) { //if not found, then check from helix path
			   $this->API->addStyleSheet($this->helixUrl . '/css/' . $src);
			}		
		}
	}	

	function addInlineJS($code) {//add inline js code
		$this->API->addScriptDeclaration($code); 
	}

	function addInlineCSS($code) {//add inline css code
		$this->API->addStyleDeclaration($code);	
	}
	
	function fixHeight ($cls,$id=1) {//equal height function
		$this->addJS('equalheight.js'); 
		$this->addInlineJS('function fixHeight' . $id . ' () {
			var equalizer'. $id .' = new Equalizer(".' . $cls . '").equalize("height");
		}
		window.addEvent ("load", function () {
			fixHeight'. $id .'.delay (300);
		});
		window.addEvent ("resize", function () {
			fixHeight'. $id .'();
		});');
	}	
	
	function hideItem() {//Itemid for hiding component area
		if ($this->getParam('hide_component')) {	
			$menu = new JSite;
			$menu = $menu->getMenu();
			if (isset($menu->getActive()->id)) {
				$Itemid = $menu->getActive()->id;//Detect active menu item
				$hide_menu_items = $this->getParam('hide_menu_items');//Itemid to hide component area
				if ($hide_menu_items=='') return;//empty item
				if (!is_array($hide_menu_items)) $hide_menu_items = array($hide_menu_items);
				if (in_array($Itemid, $hide_menu_items)) return true;
			} 
		}	
	}
    
	function getSiteName() {//get site name
        $app = JFactory::getApplication();
        return $app->getCfg('sitename');
    }
	
    function countModules($modules) {
        return $this->API->countModules($modules);//Count Modules
    }
	
	function renderModules($mods,$style='sp_raw',$class='') {//Load modules
		$output='';
		foreach($mods as $mod) {
			$output	.='<div style="width:' . $mod['width'] . '" class="sp-block '. $mod['sep'] . '">';
			$output	.='<div id="' . $mod['name'] . '" class="mod-block ' . $class . $mod['extra-css'] . '">';
			$output	.='<jdoc:include type="modules" name="' . $mod['name'] . '" style="' . $style . '" />';
			$output	.='</div>';	
			$output	.='</div>';		
		}	
		echo $output;
	}
	
	/**
	 * Function to load single ot multiple module
	 *
	 * @since	1.9
	 */
	function addModules($modPos, $style='sp_raw', $gridId='', $width='', $equalHeight = false, $fluid = false) {
		$output='';
		$mods = $this->getModules($modPos, $width);//get active modules
		$modCnt = count($mods);
		if ($mods) {
			if ($gridId) {//gridId
				$output .= '<div id="' . $gridId . '" class="sp-modCol' . $modCnt . ' clearfix">';
			}
			
			if ($fluid) {//full width
				$output .= '<div class="sp-wrap clearfix">';
			}
			
			if ($gridId || $fluid) {
				$output .= '<div class="sp-inner clearfix">';
			}
			
			foreach($mods as $mod) {
				if ($gridId) {
					$output	.= '<div style="width:' . $mod['width'] . '" class="sp-block '. $mod['sep'] . '">';
					$output	.= '<div id="sp-' . $mod['name'] . '" class="mod-block sp-' . $mod['name'] . ' ' . $mod['extra-css'] . '">';
				} else {
					$output	.= '<div id="sp-' . $mod['name'] . '" class="clearfix">';			
					$output	.= '<div class="sp-inner clearfix">';
				}
				$output	.= '<jdoc:include type="modules" name="' . $mod['name'] . '" style="' . $style . '" />';
				$output	.= '</div>';	
				$output	.= '</div>';		
			}	
			
			if ($gridId || $fluid) {
				$output .= '</div>';
			}			
			
			if ($fluid) {//full width
				$output .= '</div>';
			}	
			
			if ($gridId) {//gridId
				$output .= '</div>';
			}
			
			if ($equalHeight && $gridId) {//Equal height for module grid
				$eqCls = "";
				foreach ($mods as $mod) {
					$eqCls .= '.sp-' . $mod['name'] . ' > div > div,';
				}		
				$eqCls = rtrim($eqCls,',');	
				$gridId = str_replace("-","_",$gridId);
				$this->addJS('equalheight.js'); 
				$this->addInlineJS('function fixHeight_' . $gridId . ' () {
					var equalizer_'. $gridId .' = new Equalizer("' . $eqCls . '").equalize("height");
				}
				window.addEvent ("load", function () {
					fixHeight_'. $gridId .'.delay (300);
				});
				window.addEvent ("resize", function () {
					fixHeight_'. $gridId .'();
				});');			
			}	
			
			echo $output;
		}
		return false;
	}
	
	/**
	 * Check modules to load
	 *
	 * @since	1.0
	 */
    function hasModule($position){	
		$position = explode (',',$position);
		if (!is_array($position)) $position = array($position);           
		if(is_array($position)){    
			$modcount=0;
			$modules=array();
			foreach($position as $val){
				$val=trim($val);
				if($this->API->countModules($val)){
					array_push($modules,$val);
					$modcount++;
				} 
			}
			
			if($modcount==0){
				return false;
			}
			return $modules; 
		}
        
    }
	
	/**
	 * Get active modules
	 *
	 * @since	1.0
	 * @update	1.9
	 */
    function getModules($pos, $width=''){
		$activeMod	= $this->hasModule($pos);
		$max		= count($activeMod);
		
		//Added 1.9
		if ($width !="") {
			$width = explode (",", $width);
			$wcount = count ($width);
			
			if ($max > $wcount) {
				$ws = array();
				for ($i=$wcount - 1; $i<$max; $i++) {
					$ws[] .= round(($width[$wcount-1]/($max + 1 -$wcount)),3);
				} 				
				array_pop($width);//remove last value from the array
				
				foreach ($ws as $w) {
					array_push ($width, $w);//add new width
				}
								
			} else {
				$ws = "";
				for ($i=$max - 1; $i<$wcount; $i++) {
					$ws += $width [$i];
				} 
				for ($i=0; $i<($wcount - $max) + 1; $i++) {
					array_pop($width);
				}
				array_push ($width, $ws);
			}
			
		} else {
			$ws = array();
			for ($i=0; $i<$max; $i++) {
				$ws[] .= round((100/$max),3);	
			}	
			$width= $ws;
		}
		
        $count=1;
        if(is_array($activeMod)){
            $modules = array();
            foreach($activeMod as $id=>$val){
                if($count==1){
                    $modules[$count] = array(
                    'name'=>$val,
                    'width'=>$width[$id] . "%",
                    'extra-css'=> ($max==1) ? ' single' : ' first',
                    'sep'=> ($max==1) ? '' : ' separator'
                    );  
                }
                elseif($count==$max){
                    $modules[$count] = array(
                    'name'=>$val,
                    'width'=>$width[$id] . "%",
                    'extra-css'=> ' last',
                    'sep'=> ''
                    );  
                } 
                else{
                    $modules[$count] = array(
                    'name'=>$val,
                    'width'=>$width[$id] . "%",
                    'extra-css'=> '',
                    'sep'=> ' separator'
                    );  
                }
                
				$count++;
            }  
            return $modules;
        }
        return false;
    }
	
	/**
	 * Detect IE Version
	 *
	 * @since	1.0
	 */
	function isIE($version = false) {   
		$agent=$_SERVER['HTTP_USER_AGENT'];  
		$found = strpos($agent,'MSIE ');  
		if ($found) { 
				if ($version) {
					$ieversion = substr(substr($agent,$found+5),0,1);   
					if ($ieversion == $version) return true;
					else return false;
				} else {
					return true;
				}
				
			} else {
					return false;
			}
		if (stristr($agent, 'msie'.$ieversion)) return true;
		return false;        
	}

	/**
	 * Get menu type
	 *
	 * @since	1.0
	 */
	function getMenu() {
		$menutype  = isset($_COOKIE[$this->theme . '_menu']) ? $_COOKIE[$this->theme . '_menu'] : $this->API->params->get('spmenu','css');
		if(isset($_GET['menu'])) {
			setcookie($this->theme . '_menu', $_GET['menu'], time() + 3600, '/'); 
			$menutype = $_GET['menu'];
		}
		return $menutype;
	}	
	
	/**
	 * Get preset style
	 *
	 * @since	1.0
	 */
	function getStyle() {
		$style  = isset($_COOKIE[$this->theme . '_style']) ? $_COOKIE[$this->theme . '_style'] : $this->API->params->get('style','style1');
		if(isset($_GET['style'])) {
			setcookie($this->theme . '_style', $_GET['style'], time() + 3600, '/'); 
			$style = $_GET['style'];
		}
		$this->style = $style;
		$this->API->addStyleSheet($this->themeUrl . '/css/styles/'.$style.'.css');
	}
	
	/**
	 * Get language direction
	 *
	 * @since	1.0
	 */
	function getDirection() {
		$direction  = isset($_COOKIE[$this->theme . '_direction']) ? $_COOKIE[$this->theme . '_direction'] : $this->API->params->get('direction','ltr');
		if(isset($_GET['direction'])) {
			setcookie($this->theme . '_direction', $_GET['direction'], time() + 3600, '/'); 
			$direction = $_GET['direction'];
		}
		
		if(($direction=='rtl') || ($this->API->direction == 'rtl')) $direction='rtl';
			else $direction='ltr';
		
		$this->direction = $direction;
		
		return $direction;		
	}
	
    /**
     * Check RTL website
     *
     * @since	1.9
     */
    function isRTL()
    {
        if ($this->getDirection()=="rtl") {
			return true;
		}
		return false;
    }	

	/**
	 * Load Head
	 *
	 * @since	1.0
	 *
	 * @update	1.9.4
	 */	
	function loadHead() {
		echo '<jdoc:include type="head" />';
		$this->getDirection();//rtl or ltr
		JHtml::_('behavior.framework', true);
		$this->addFeatures('jquery,bootstrap');//bootstarp & jQuery since 1.9.5		
		$this->API->addStylesheet($this->baseUrl."/templates/system/css/system.css");
		$this->API->addStylesheet($this->baseUrl."/templates/system/css/general.css");
		$this->API->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
		//Since 1.9.2
		$this->addCSS('media_queries.css');
		echo '<link href="' . $this->themeUrl . '/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />';

	}
	
	/**
	 * add favicon
	 *
	 * @since	1.0
	 */
	function favicon($src='favicon.ico') {
		return;
	}
	
	/**
	 * Detect frontpage
	 *
	 * @since	1.0
	 */
    function isFrontPage(){
        return (JRequest::getVar( 'view' ) == 'featured');   
    }
	
	/**
	 * Load Menu
	 *
	 * @since	1.0
	 */
	function loadMenu () {
		require_once (dirname(__FILE__).DIRECTORY_SEPARATOR."class.menu.php");
		return new HelixMenu ($this->API->params,$this->getMenu());
	}
	
	/**
	 * Add jQuery
	 *
	 * @since	1.9.5
	 */	
	function addJQuery($usecdn=false) {
		if (JVERSION>=3) {
			JHtml::_('jquery.framework');
		} else {
			$scripts = (array) array_keys( $this->API->_scripts );
			$hasjquery=false;
			foreach($scripts as $script) {
				if (preg_match("/\b(jquery|jquery-latest).([0-9\.min|max]+).(.js)\b/i", $script)) {
					$hasjquery = true;
				}  
			}

			if( !$hasjquery ) {
				if( $usecdn ) $this->addJS( 'http://code.jquery.com/jquery-latest.min.js' );
				else $this->addJS( 'jquery.min.js' );
			}
			$this->addJS( 'jquery-noconflict.js' );
		}
	}	

	function compress () {//compress css and js files
		if ($this->getParam('compress_css')) $this->compressCSS();
		if ($this->getParam('compress_js')) $this->compressJS();		
	}
	
    function compressJS() {//function to compress js files
		require_once (dirname(__FILE__).DIRECTORY_SEPARATOR."class.jsmin.php");//include jsmin class
		$js_files = array();
		$cache_time = $this->getParam('cache_time');//Cache time in minute
		$diff=false;
		$helix_folder='helix_assets';//path of cache where to save
        $output = array();
        $md5sum = null;
		
        $scripts = $this->API->_scripts;//get all scripts
		
        foreach ($scripts as $fileSrc => $fileAttr) {//separate path from attribute
            $md5sum .= md5($fileSrc);
            $js_files[] = $fileSrc;
        }
		
        if (!is_writable(JPATH_CACHE)) {//check for cache path writable, if not return
            return;
        } 
		
		if (is_writable(JPATH_CACHE)) {//add helix_assets folder under cache directory
			if (!file_exists(JPATH_CACHE.DIRECTORY_SEPARATOR.$helix_folder)) mkdir (JPATH_CACHE.DIRECTORY_SEPARATOR.$helix_folder);
		}

        if (count($js_files) > 0) {//if any js file available
            $cache_name = md5($md5sum) . ".js";
            $cache_path = JPATH_CACHE . DIRECTORY_SEPARATOR . $helix_folder . DIRECTORY_SEPARATOR . $cache_name;


            //see if file is stale
            if (!file_exists($cache_path)) {
				$diff=true;   
            } elseif(filesize($cache_path) == 0 || ((filemtime($cache_path) + $cache_time * 60) < time())) {
				$diff=true; 
            }
			
			foreach ($js_files as $files) {
				unset($this->API->_scripts[$files]); //Remove js files from the header
			}
			
            if ($diff) {
                $output = '';
                foreach ($js_files as $files) {
					$filepath = $this->realPath($files);
                    if (JFile::exists($filepath)) {
                        $js = JSMin::minify(JFile::read($filepath));//read and compress js files
                        $output .= "/*------ " . $files . " ------*/\n" . $js . "\n\n";//add file name to compressed JS
                    }
                }
                JFile::write($cache_path, $output);//write cache to the joomla cache directory
            }
			
            $cache_url = $this->API->baseurl . "/cache/" . $helix_folder . '/' . $cache_name;//path of css cache to add as script
            $this->API->addScript($cache_url);//add script to the header
        }
    }

	function compressCSS() {//function to compress css files
		require_once (dirname(__FILE__).DIRECTORY_SEPARATOR."class.cssminify.php");//include cssminify class
		$css_files = array();
		$cache_time = $this->getParam('cache_time');//Cache time in minute
		$helix_folder='helix_assets';//path of cache where to save
        $output = array();
        $md5sum = null;
		
        $csss = $this->API->_styleSheets;//get all CSS
		
        foreach ($csss as $fileSrc => $fileAttr) {//separate path from attribute
            $md5sum .= md5($fileSrc);
            $css_files[] = $fileSrc;
        }
		
        if (!is_writable(JPATH_CACHE)) {//check for cache path writable, if not return
            return;
        } 
		
		if (is_writable(JPATH_CACHE)) {//add helix_assets folder under cache directory
			if (!file_exists(JPATH_CACHE.DIRECTORY_SEPARATOR.$helix_folder)) mkdir (JPATH_CACHE.DIRECTORY_SEPARATOR.$helix_folder);
		}

        if (count($css_files) > 0) {//if any css file available
            $cache_name = md5($md5sum) . ".css";
            $cache_path = JPATH_CACHE . DIRECTORY_SEPARATOR . $helix_folder . DIRECTORY_SEPARATOR . $cache_name;
			$diff=false;

            //see if file is stale
            if (!file_exists($cache_path)) {
				$diff=true;   
            } elseif(filesize($cache_path) == 0 || ((filemtime($cache_path) + $cache_time * 60) < time())) {
				$diff=true; 
            }

			foreach ($css_files as $files) {
				unset($this->API->_styleSheets[$files]); //Remove all css files from the header
			}
			
            if ($diff) {
                $output = '';
                foreach ($css_files as $files) {
					$filepath = $this->realPath($files);//convert to real url
					
				    global $absolute_url;
					$absolute_url = $files;//absoulte path of each css file
                    if (JFile::exists($filepath)) {
                        $css = CSSMinify::process(JFile::read($filepath));//read and compress css files
						
						$css=preg_replace_callback('/url\(([^\)]*)\)/', array($this, 'replaceUrl'), $css);//call replaceUrl function to set absolute value to the urls
						
                        $output .= "/*------ " . $files . " ------*/\n" . $css . "\n\n";//add file name to compressed css
                    }
                }
                JFile::write($cache_path, $output);//write cache to the joomla cache directory
            }
			
            $cache_url = $this->API->baseurl . "/cache/" . $helix_folder . '/' . $cache_name;//path of css cache to add as stylesheet
            $this->API->addStyleSheet($cache_url);//add stylesheet to the header
        }
    }
	
    function replaceUrl($matches) {//replace url with absolute path
        $url = str_replace(array('"', '\''), '', $matches[1]);
        $url = $this->fixUrl($url);
        return "url('$url')";
    }

    function fixUrl($url) {
	    global $absolute_url;
        $base = dirname($absolute_url);
        if (preg_match('/^(\/|http)/', $url))
            return $url;
        /*absolute or root*/
        while (preg_match('/^\.\.\//', $url)) {
            $base = dirname($base);
            $url = substr($url, 3);
        }

        $url = $base . '/' . $url;
        return $url;
    }	
	
	function realPath($strSrc) {//Real path of css or js file
		if (preg_match('/^https?\:/', $strSrc)) {
			if (!preg_match('#^' . preg_quote(JURI::base()) . '#', $strSrc)) return false; //external css
			$strSrc = str_replace(JURI::base(), '', $strSrc);
		} else {
			if (preg_match('/^\//', $strSrc)) {
				if (!preg_match('#^' . preg_quote(JURI::base(true)) . '#', $strSrc)) return false; //same server, but outsite website
				$strSrc = preg_replace('#^' . preg_quote(JURI::base(true)) . '#', '', $strSrc);
			}
		}
		$strSrc = str_replace('//', '/', $strSrc);
		$strSrc = preg_replace('/^\//', '', $strSrc);
		return $strSrc;
	}	
	
}