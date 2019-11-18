<?php
	$asset_types = array(
		"texture_id" => 0,
		"sound_id" => 1,
		//"callcard_id" => 2,
		"landmark_id" => 3,
		//"script_id" => 4,
		"clothing_id" => 5,
		//"object_id" => 6,
		//"notecard_id" => 7,
		//"category_id" => 8,
		//"lsltext_id" => 10,
		//"lslbyte_id" => 11,
		//"txtr_tga_id" => 12,
		"bodypart_id" => 13,
		//"snd_wav_id" => 17,
		//"img_tga_id" => 18,
		//"jpeg_id" => 19,
		"animatn_id" => 20,
		"gesture_id" => 21,
		"simstate_id" => 22,
		"link_id" => 24,
		"link_f_id" => 25,
		"mesh_id" => 49,
	);
	
	$asset_type_to_content_type = array(
		-1 => "application/octet-stream",
		 0 => "image/x-j2c",
		 1 => "audio/ogg",
		 2 => "application/vnd.ll.callingcard", 
		 3 => "application/vnd.ll.landmark", 
		 5 => "application/vnd.ll.clothing",
		 6 => "application/vnd.ll.primitive", 
		 7 => "application/vnd.ll.notecard",
		10 => "application/vnd.ll.lsltext",
		11 => "application/vnd.ll.lslbyte",
		12 => "image/tga",
		13 => "application/vnd.ll.bodypart",
		17 => "audio/x-wav",
		18 => "image/tga",
		19 => "image/jpeg", 
		20 => "application/vnd.ll.animation",
		21 => "application/vnd.ll.gesture",
		22 => "application/x-metaverse-simstate",
		24 => "application/vnd.ll.link",
		25 => "application/vnd.ll.linkfolder",
		49 => "application/vnd.ll.mesh",

		// Folders
		8 => "application/vnd.ll.folder",

		// OpenSim specific
		-2 => "application/llsd+xml"
	);
?>