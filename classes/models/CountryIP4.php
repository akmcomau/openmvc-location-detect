<?php

namespace modules\location_detect\classes\models;

use core\classes\exceptions\RedirectException;
use core\classes\Hook;
use core\classes\Model;
use core\classes\Module;

class CountryIP4 extends Model {
	protected $table       = 'country_ip4';
	protected $primary_key = 'country_ip4_id';
	protected $columns     = [
		'country_ip4_id' => [
			'data_type'      => 'int',
			'auto_increment' => TRUE,
			'null_allowed'   => FALSE,
		],
		'country_id' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
		'country_ip4_start' => [
			'data_type'      => 'inet',
			'null_allowed'   => FALSE,
		],
		'country_ip4_end' => [
			'data_type'      => 'inet',
			'null_allowed'   => FALSE,
		],
	];

	protected $indexes = [
		'country_id',
		'country_ip4_start',
		'country_ip4_end',
	];

	public function findCountry($address) {
		$ip = $this->get([
			'start' => ['type' => '<=', 'value' => $address],
			'end'   => ['type' => '>=', 'value' => $address],
		]);

		if ($ip) {
			return $this->getModel('\core\classes\models\Country')->get([
				'id' => $ip->country_id
			]);
		}

		return NULL;
	}

	public function updateCountryIps($url) {
		set_time_limit(600);

		// Fetch all the countries
		$countries = [];
		$countries_db = $this->getModel('\core\classes\models\Country')->getMulti();
		foreach ($countries_db as $country) {
			$countries[strtoupper($country->code)] = $country;
		}

		$this->database->executeQuery('TRUNCATE country_ip4;');

		// download and decompress
		$contents = file_get_contents($url);
		$tmp_filename = '/tmp/location_detect_db.gz';
		file_put_contents($tmp_filename, $contents);
		exec('gzip -d '.$tmp_filename);

		$tmp_file = fopen('/tmp/location_detect_db', 'r');
		while (!feof($tmp_file)) {
			$line = fgets($tmp_file);
			$row = str_getcsv($line);
			if (!preg_match('/:/', $row[0]) && isset($row[2])) {
				if (isset($countries[$row[2]])) {
					$ip = $this->getModel('\modules\location_detect\classes\models\CountryIP4');
					$ip->start      = $row[0];
					$ip->end        = $row[1];
					$ip->country_id = $countries[strtoupper($row[2])]->id;
					$ip->insert();
				}
			}
		}
	}
}
