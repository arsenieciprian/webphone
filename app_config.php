<?php

	//defined
	if (!isset($apps) || !is_array($apps)) {
		$apps = array();
	}

	//application details
		$apps[$x]['name'] = "Webphone";
		$apps[$x]['uuid'] = "95a56762-632f-4888-9976-591783060a1e";
		$apps[$x]['category'] = "Applications";
		$apps[$x]['subcategory'] = "";
		$apps[$x]['version'] = "1.0";
		$apps[$x]['license'] = "Mozilla Public License 1.1";
		$apps[$x]['url'] = "http://www.fusionpbx.com";
		$apps[$x]['description']['en-us'] = "WebRTC Phone";
		$apps[$x]['description']['ro-ro'] = "Telefon WebRTC";

	//menu details
	// Includem fisierul inteligent de mai sus
		if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/app/webphone/app_menu.php")) {
			include $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/app/webphone/app_menu.php";
		}

	//permission details
		$y=0;
		$apps[$x]['permissions'][$y]['name'] = "webphone_view";
		$apps[$x]['permissions'][$y]['menu']['uuid'] = "c8d32b08-5d6c-4309-974d-19d85413247a";
		$apps[$x]['permissions'][$y]['groups'][] = "superadmin";
		$apps[$x]['permissions'][$y]['groups'][] = "admin";
		$apps[$x]['permissions'][$y]['groups'][] = "user";
		$apps[$x]['permissions'][$y]['groups'][] = "agent";

	//default settings
		$y=0;
		$apps[$x]['default_settings'][$y]['default_setting_uuid'] = "143493b2-652f-4c17-b6aa-55f63f53d463";
		$apps[$x]['default_settings'][$y]['default_setting_category'] = "webphone";
		$apps[$x]['default_settings'][$y]['default_setting_subcategory'] = "enabled";
		$apps[$x]['default_settings'][$y]['default_setting_name'] = "boolean";
		$apps[$x]['default_settings'][$y]['default_setting_value'] = "true";
		$apps[$x]['default_settings'][$y]['default_setting_enabled'] = "true";
		$apps[$x]['default_settings'][$y]['default_setting_description'] = "Enable Webphone.";

?>
