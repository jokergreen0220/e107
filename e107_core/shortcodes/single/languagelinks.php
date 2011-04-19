<?php 
/*
 * e107 website system
 *
 * Copyright (C) 2008-2011 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Core Language links shortcode
 *
 * $URL$
 * $Id$
 */

 /**
 * @package e107
 * @subpackage shortcodes
 * @version $Id$
 *
 * Render language links navigation
 */

/**
 * Example usage:
 * <code>
 * <?php 
 * $SOME_TEMPLATE = '{LANGUAGELINKS}'; // render custom lan list, include current query string
 * </code>
 * 
 * <code>
 * <?php 
 * $SOME_TEMPLATE = '{LANGUAGELINKS=English,Bulgarian}'; // render custom lan list, include current query string
 * </code>
 * 
 * <code>
 * <?php
 * $SOME_TEMPLATE = '{LANGUAGELINKS=English,Bulgarian|noquery}'; // render custom lan list, exclude query
 * </code>
 * 
 * <code>
 * <?php
 * $SOME_TEMPLATE = '{LANGUAGELINKS=|home}'; // render default (available) lan list, point always to site index
 * </code>
 * 
 * @param string $parm
 */
function languagelinks_shortcode($parm = '')
{
	if(!defined('LANGLINKS_SEPARATOR'))
	{
		define('LANGLINKS_SEPARATOR', '&nbsp;|&nbsp;');
	}

	$tmp = explode('|', $parm, 2);
	$parm = $tmp[0];
	$parms = array();
	if(isset($tmp[1])) parse_str($tmp[1], $parms);
	
	// ignore Query string if required by parms or external code, false by default
	if(!defined('LANGLINKS_NOQUERY'))
	{
		define('LANGLINKS_NOQUERY', isset($parms['noquery']));
	}
	
	if(!defined('LANGLINKS_HOME'))
	{
		define('LANGLINKS_HOME', isset($parms['home']));
	}
	
	/*require_once(e_HANDLER.'language_class.php');
	$slng = new language;*/
	$slng = e107::getLanguage();
	
	if(!empty($parm))
	{
		$languageList = explode(',', $parm);
	}
	else
	{
		$languageList = $slng->installed();
		sort($languageList);
	}
	
	if(count($languageList) < 2)
	{
		return;
	}
	
	foreach($languageList as $languageFolder)
	{
		$code = $slng->convert($languageFolder);
		$name = $slng->toNative($languageFolder);
		//$subdom = (isset($cursub[2])) ? $cursub[0] : '';
	
		if(e107::getPref('multilanguage_subdomain'))
		{
			$code = ($languageFolder == e107::getPref('sitelanguage')) ? 'www.' : $code;
			if(LANGLINKS_HOME) 
				$link = str_replace($_SERVER['HTTP_HOST'], $code.'.'.e_DOMAIN, SITEURL);
			else 
				$link = (!LANGLINKS_NOQUERY)
			        ? str_replace($_SERVER['HTTP_HOST'], $code.'.'.e_DOMAIN, e_REQUEST_URL) // includes query string
			        : str_replace($_SERVER['HTTP_HOST'], $code.'.'.e_DOMAIN, e_REQUEST_SELF); // excludes query string
		}
		else
		{
			// TODO - switch to elan=Language query when possible (now it'll break the old DOT query string format)
			if(LANGLINKS_HOME) 
				$link = SITEURL.'?elan='.$languageFolder;
			else 
				$link = (!LANGLINKS_NOQUERY) ? e_REQUEST_SELF.'?['.$code.']'.e_QUERY : e_REQUEST_SELF.'?elan='.$languageFolder;
		}
		$class = ($languageFolder == e_LANGUAGE) ? 'languagelink_active' : 'languagelink'; e_dump($languageFolder, $class);
		$ret[] =  "\n<a class='{$class}' href='{$link}'>{$name}</a>";
	}
	
	return implode(LANGLINKS_SEPARATOR, $ret);
}