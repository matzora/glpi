<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2009 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Julien Dombre
// Purpose of file:
// ----------------------------------------------------------------------

define('GLPI_ROOT','..');
include (GLPI_ROOT."/inc/includes.php");
$AJAX_INCLUDE=1;
header("Content-Type: text/html; charset=UTF-8");
header_nocache();

checkRight("networking","w");

// Make a select box

if (isset($LINK_ID_TABLE[$_POST["type"]])&&isset($_POST["item"])){

	$table=$LINK_ID_TABLE[$_POST["type"]];

	$where="";		
	$where.=" AND $table.deleted=0 ";
	$where.=" AND $table.is_template='0' ";		

	$query =  "SELECT DISTINCT glpi_networkports_networkports.ID as WID, glpi_networkports.ID as DID, $table.name as CNAME, glpi_networkports.name  as NNAME, glpi_networkports.ifaddr as IP, glpi_networkports.ifmac as MAC";
	$query.= " FROM $table ";
	$query.= " LEFT JOIN glpi_networkports ON (glpi_networkports.items_id='".$_POST['item']."' AND glpi_networkports.itemtype='".$_POST["type"]."' AND glpi_networkports.items_id=$table.ID) "; 
	$query.= " LEFT JOIN glpi_networkports_networkports ON (glpi_networkports_networkports.end1=glpi_networkports.ID OR glpi_networkports_networkports.end2=glpi_networkports.ID)";
	$query.= " WHERE glpi_networkports_networkports.ID IS NULL AND glpi_networkports.ID IS NOT NULL AND glpi_networkports.ID <> '".$_POST['current']."' ";
	$query.= $where;
	$query.= " ORDER BY glpi_networkports.ID";
	$result = $DB->query($query);
	echo "<br>";
	echo "<select name=\"".$_POST['myname']."[".$_POST["current"]."]\" size='1'>";

	echo "<option value=\"0\">-----</option>";
	if ($DB->numrows($result)) {
		while ($data = $DB->fetch_array($result)) {
			$output = $data['CNAME'];
			$output_long="";
			if (!empty($data['IP'])) $output.= " - ".$data['IP'];
			if (!empty($data['MAC'])) $output_long.= " - ".$data['MAC'];
			if (!empty($data['NNAME'])) $output_long.= utf8_substr(" - ".$data['NNAME'],0,$_SESSION["glpidropdown_limit"]);
			$ID = $data['DID'];
			if (empty($data["IP"])) {
				$output.=$output_long;
				$output_long="";
			}
			if (empty($output)) $output="($ID)";
			echo "<option value=\"$ID\" title=\"".cleanInputText($output.$output_long)."\">".$output."</option>";
		}
	}
	echo "</select>";

	echo "<input type='submit' name='connect' value=\"".$LANG['buttons'][9]."\" class='submit'>";
}

?>