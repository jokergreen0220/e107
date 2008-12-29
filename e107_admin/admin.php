<?php
/*
+ ----------------------------------------------------------------------------+
|     e107 website system
|
|     �Steve Dunstan 2001-2002
|     http://e107.org
|     jalist@e107.org
|
|     Released under the terms and conditions of the
|     GNU General Public License (http://gnu.org).
|
|     $Source: /cvs_backup/e107_0.8/e107_admin/admin.php,v $
|     $Revision: 1.8 $
|     $Date: 2008-12-29 16:11:02 $
|     $Author: secretr $
+----------------------------------------------------------------------------+
*/
require_once('../class2.php');
$e_sub_cat = 'main';
require_once('auth.php');
require_once(e_HANDLER.'admin_handler.php');
require_once(e_HANDLER.'upload_handler.php');

if (!isset($pref['adminstyle'])) $pref['adminstyle'] = 'classis';		// Shouldn't be needed - but just in case


// --- check for htmlarea.
if (is_dir(e_ADMIN.'htmlarea') || is_dir(e_HANDLER.'htmlarea')) 
{
	$text = ADLAN_ERR_2."<br /><br />
	<div style='text-align:center'>".$HANDLERS_DIRECTORY."htmlarea/<br />".$ADMIN_DIRECTORY."htmlarea/</div>";
	$ns -> tablerender(ADLAN_ERR_1, $text);
}

/* Not used in 0.8	
// check for old modules.
if(getperms('0') && isset($pref['modules']) && $pref['modules'] && $sql->db_Field("plugin",5) == "plugin_addons")
{
	$mods=explode(",", $pref['modules']);
	$thef = "e_module.php";
	foreach ($mods as $mod)
	{
		if (is_readable(e_PLUGIN."{$mod}/module.php"))
		{
			$mod_found[] = e_PLUGIN."{$mod}/module.php";
		}
	}

	if($mod_found)
	{
    	$text = ADLAN_ERR_5." <b>".$thef."</b>:<br /><br /><ul>";
		foreach($mod_found as $val){
			$text .= "<li>".str_replace("../","",$val)."</li>\n";
		}
		$text .="</ul><br />
		<form method='post' action='".e_ADMIN."db.php' id='upd'>
		<a href='#' onclick=\"document.getElementById('upd').submit()\">".ADLAN_ERR_6."</a>
		<input type='hidden' name='plugin_scan' value='1' />
		</form>";
		$ns -> tablerender(ADLAN_ERR_4,$text);
	}
}
*/

// check for file-types;
$allowed_types = get_filetypes();			// Get allowed types according to filetypes.xml or filetypes.php
if (count($allowed_types) == 0)
{
	echo "Setting default filetypes<br />";
	$allowed_types = array('zip' => 1, 'gz' => 1, 'jpg' => 1, 'png' => 1, 'gif' => 1);
}
//echo "Allowed filetypes = ".implode(', ',array_keys($allowed_types)).'<br />';
// avatar check.
$public = array(e_FILE.'public', e_FILE.'public/avatars');
foreach ($public as $dir) 
{
	if (is_dir($dir)) 
	{
		if ($dh = opendir($dir)) 
		{
			while (($file = readdir($dh)) !== false) 
			{
				if (is_dir($dir."/".$file) == FALSE && $file != '.' && $file != '..' && $file != '/' && $file != 'CVS' && $file != 'avatars' && $file != 'Thumbs.db' && $file !=".htaccess" && $file !="php.ini") 
				{
					$fext = substr(strrchr($file, "."), 1);
					if (!array_key_exists(strtolower($fext),$allowed_types) ) 
					{
						if ($file == 'index.html' || $file == "null.txt") 
						{
							if (filesize($dir.'/'.$file)) 
							{
								$potential[] = str_replace('../', '', $dir).'/'.$file;
							}
						} 
						else 
						{
							$potential[] = str_replace('../', '', $dir).'/'.$file;
						}
					}
				}
			}
			closedir($dh);
		}
	}
}

if (isset($potential)) 
{
	$text = ADLAN_ERR_3."<br /><br />";

	foreach ($potential as $p_file) 
	{
		$text .= $p_file.'<br />';
	}

	$ns -> tablerender(ADLAN_ERR_1, $text);
}


// ---------------------------------------------------------



// auto db update
if ('0' == ADMINPERMS) 
{
	require_once(e_ADMIN.'update_routines.php');
	update_check();
}
// end auto db update

/*
if (e_QUERY == 'purge' && getperms('0')) 
{
	$admin_log->purge_log_events(false);
}
*/

$td = 1;
if(!defined("ADLINK_COLS"))
{
	define("ADLINK_COLS",5);
}


function render_links($link, $title, $description, $perms, $icon = FALSE, $mode = FALSE) 
{
	global $td,$tp;
	$text = '';
	if (getperms($perms)) 
	{
		$description = strip_tags($description);
		if ($mode == 'adminb') 
		{
			$text = "<tr><td class='forumheader3'>
				<div class='td' style='text-align:left; vertical-align:top; width:100%'
				onmouseover=\"eover(this, 'forumheader5')\" onmouseout=\"eover(this, 'td')\" onclick=\"document.location.href='".$link."'\">
				".$icon." <b>".$title."</b> ".($description ? "[ <span class='smalltext'>".$description."</span> ]" : "")."</div></td></tr>";
		} 
		else 
		{
			if ($td == (ADLINK_COLS+1)) 
			{
				$text .= '</tr>';
				$td = 1;
			}
			if ($td == 1) 
			{
				$text .= '<tr>';
			}
			if ($mode == 'default') 
			{
				$text .= "<td class='td' style='text-align:left; vertical-align:top; width:20%; white-space:nowrap'
					onmouseover=\"eover(this, 'forumheader5')\" onmouseout=\"eover(this, 'td')\" onclick=\"document.location.href='".$link."'\">".$icon." ".$tp->toHTML($title,FALSE,"defs, emotes_off")."</td>";
			}
			elseif ($mode == 'classis') 
			{
				$text .= "<td style='text-align:center; vertical-align:top; width:20%'><a href='".$link."' title='{$description}'>".$icon."</a><br />
					<a href='".$link."' title='{$description}'><b>".$tp->toHTML($title,FALSE,"defs, emotes_off")."</b></a><br /><br /></td>";
			}
			elseif ($mode == 'beginner')
			{
                $text .= "<td style='text-align:center; vertical-align:top; width:20%' ><a href='".$link."' >".$icon."</a>
					<div style='padding:5px'>
					<a href='".$link."' title='".$description."' style='text-decoration:none'><b>".$tp->toHTML($title,FALSE,"defs, emotes_off")."</b></a></div><br /><br /><br /></td>";
			}
			$td++;
		}
	}
	return $text;
}


function render_clean() 
{
	global $td;
	while ($td <= ADLINK_COLS) {
		$text .= "<td class='td' style='width:20%;'></td>";
		$td++;
	}
	$text .= "</tr>";
	$td = 1;
	return $text;
}


$newarray = asortbyindex($array_functions, 1);


require_once(e_ADMIN.'includes/'.$pref['adminstyle'].'.php');

function admin_info() 
{
	global $tp;

	$width = (getperms('0')) ? "33%" : "50%";

	$ADMIN_INFO_TEMPLATE = "
	<div style='text-align:center'>
		<table style='width: 100%; border-collapse:collapse; border-spacing:0px;'>
		<tr>
			<td style='width: ".$width."; vertical-align: top'>
			{ADMIN_STATUS}
			</td>
			<td style='width:".$width."; vertical-align: top'>
			{ADMIN_LATEST}
			</td>";

    	if(getperms('0'))
		{
			$ADMIN_INFO_TEMPLATE .= "
			<td style='width:".$width."; vertical-align: top'>{ADMIN_LOG}</td>";
    	}

   	$ADMIN_INFO_TEMPLATE .= "
		</tr></table></div>";

	return $tp->parseTemplate($ADMIN_INFO_TEMPLATE);
}

function status_request() 
{
	global $pref;
	if ($pref['adminstyle'] == 'classis' || $pref['adminstyle'] == 'cascade' || $pref['adminstyle'] == 'beginner') {
		return TRUE;
	} else {
		return FALSE;
	}
}


function latest_request() 
{
	global $pref;
	if ($pref['adminstyle'] == 'classis' || $pref['adminstyle'] == 'cascade' || $pref['adminstyle'] == 'beginner') {
		return TRUE;
	} else {
		return FALSE;
	}
}

function log_request() 
{
	global $pref;
	if ($pref['adminstyle'] == 'classis' || $pref['adminstyle'] == 'cascade'|| $pref['adminstyle'] == 'beginner') {
		return TRUE;
	} else {
		return FALSE;
	}
}


// Function renders all the plugin links according to the required icon size and layout style
// - common to the various admin layouts.
function getPluginLinks($iconSize = E_16_PLUGMANAGER, $linkStyle = 'adminb')
{
	global $sql, $tp;

	$text = render_links(e_ADMIN."plugin.php", ADLAN_98, ADLAN_99, "Z", $iconSize, $linkStyle);

	require_once(e_HANDLER.'xml_class.php');
	$xml = new xmlClass;				// We're going to have some plugins with plugin.xml files, surely? So create XML object now
	$xml->filter = array('@attributes' => FALSE,'description'=>FALSE,'administration' => FALSE);	// .. and they're all going to need the same filter

	if ($sql->db_Select("plugin", "*", "plugin_installflag=1")) 
	{
		while ($row = $sql->db_Fetch()) 
		{
			extract($row);		//  plugin_id int(10) unsigned NOT NULL auto_increment,
								//	plugin_name varchar(100) NOT NULL default '',
								//	plugin_version varchar(10) NOT NULL default '',
								//	plugin_path varchar(100) NOT NULL default '',
								//	plugin_installflag tinyint(1) unsigned NOT NULL default '0',
								//	plugin_addons text NOT NULL,
 
			if (is_readable(e_PLUGIN.$plugin_path."/plugin.xml"))
			{
				$readFile = $xml->loadXMLfile(e_PLUGIN.$plugin_path.'/plugin.xml', true, true);
				if ($readFile === FALSE)
				{
					echo 'Error in file: '.e_PLUGIN.$plugin_path.'/plugin.xml'.'<br />';
				}
				else
				{
					loadLanFiles($plugin_path, 'admin');
					$eplug_name 		= $tp->toHTML($readFile['@attributes']['name'],FALSE,"defs, emotes_off");
					$eplug_conffile 	= $readFile['administration']['configFile'];
					$eplug_icon_small 	= $plugin_path.'/'.$readFile['administration']['iconSmall'];
					$eplug_icon 		= $plugin_path.'/'.$readFile['administration']['icon'];
					$eplug_caption 		= str_replace("'", '', $tp->toHTML($readFile['description'], FALSE, 'defs, emotes_off'));
				}
			}
			elseif (is_readable(e_PLUGIN.$plugin_path."/plugin.php"))
			{
				include(e_PLUGIN.$plugin_path."/plugin.php");
			}
			if ($eplug_conffile) 
			{
				$eplug_name = $tp->toHTML($eplug_name,FALSE,"defs, emotes_off");
				if ($iconSize == E_16_PLUGMANAGER)
				{
					$plugin_icon = $eplug_icon_small ? "<img src='".e_PLUGIN.$eplug_icon_small."' alt='' style='border:0px; vertical-align:bottom; width: 16px; height: 16px' />" : E_16_PLUGIN;
				}
				else
				{
					$plugin_icon = $eplug_icon ? "<img src='".e_PLUGIN.$eplug_icon."' alt='' style='border:0px; vertical-align:bottom; width: 32px; height: 32px' />" : E_32_PLUGIN;
				}
				$plugin_array[ucfirst($eplug_name)] = array('link' => e_PLUGIN.$plugin_path."/".$eplug_conffile, 'title' => $eplug_name, 'caption' => $eplug_caption, 'perms' => "P".$plugin_id, 'icon' => $plugin_icon);
			}
			unset($eplug_conffile, $eplug_name, $eplug_caption, $eplug_icon_small);
		}
	}

	ksort($plugin_array, SORT_STRING);
	foreach ($plugin_array as $plug_key => $plug_value) 
	{
		$text .= render_links($plug_value['link'], $plug_value['title'], $plug_value['caption'], $plug_value['perms'], $plug_value['icon'], $linkStyle);
	}
	return $text;
}


require_once("footer.php");

?>
