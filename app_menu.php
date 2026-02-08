<?php

	$y=0;
	
	// -------------------------------------------------------------------------
	// LOGICA DINAMICA: Cautam UUID-ul parintelui (Applications)
	// -------------------------------------------------------------------------
	$parent_uuid = "";

	if (class_exists('database')) {
		// Folosim un nume de variabila UNIC ($db_temp) ca sa nu stergem conexiunea globala
		$db_temp = new database;
		$sql = "select menu_item_uuid from v_menu_items ";
		$sql .= "where menu_item_title = 'Applications' ";
		$sql .= "order by menu_item_order asc limit 1";
		
		$parent_uuid = $db_temp->select($sql, null, 'column');
		unset($db_temp); // Acum stergem doar variabila noastra temporara
	}

	// Fallback la standard
	if (empty($parent_uuid)) {
		$parent_uuid = "fd29e060-4b23-4554-8e74-388edcf1f7d2";
	}
	// -------------------------------------------------------------------------

	$apps[$x]['menu'][$y]['title']['en-us'] = "Webphone";
	$apps[$x]['menu'][$y]['title']['ro-ro'] = "Webphone";
	$apps[$x]['menu'][$y]['uuid'] = "c8d32b08-5d6c-4309-974d-19d85413247a";
	$apps[$x]['menu'][$y]['parent_uuid'] = $parent_uuid;
	$apps[$x]['menu'][$y]['category'] = "internal";
	$apps[$x]['menu'][$y]['path'] = "/app/webphone/index.php";
	$apps[$x]['menu'][$y]['order'] = "50";
	$apps[$x]['menu'][$y]['groups'][] = "superadmin";
	$apps[$x]['menu'][$y]['groups'][] = "admin";
	$apps[$x]['menu'][$y]['groups'][] = "user";
	$apps[$x]['menu'][$y]['groups'][] = "agent";

?>
