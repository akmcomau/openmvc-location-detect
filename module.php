<?php
$_MODULE = [
	"name" => "Location Detect",
	"description" => "Detect the location of the user, based on IP address",
	"namespace" => "\\modules\\location_detect",
	"config_controller" => "administrator\\LocationDetect",
	"hooks" => [
		"request" => [
			"before_request" => "classes\\Hooks",
		]
	],
	"controllers" => [
		"administrator\\LocationDetect",
	],
	"default_config" => [
		'terms_url' => 'https://db-ip.com/db/download/country',
		"download_url" => "http://download.db-ip.com/free/dbip-country-2015-10.csv.gz",
		"allowed_currencies" => NULL,
		"allowed_countries" => NULL,
	]
];
