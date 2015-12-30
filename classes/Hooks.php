<?php

namespace modules\location_detect\classes;

use core\classes\exceptions\RedirsectException;
use core\classes\Hook;
use core\classes\Model;
use core\classes\Authentication;
use core\classes\models\Customer;
use core\classes\models\Administrator;

class Hooks extends Hook {
	public function before_request() {
		// Do not change if an admin is logged in
		if ($this->request->getAuthentication()->administratorLoggedIn()) {
			return;
		}

		$module_config = $this->config->moduleConfig('\modules\location_detect');

		$country = NULL;
		$model     = new Model($this->config, $this->database);
		if ($this->request->requestParam('set_country')) {
			$country = $model->getModel('\core\classes\models\Country')->get([
				'code' => $this->request->requestParam('set_country')
			]);
			$this->request->session->set('site_country', $country->code);
			$this->logger->info("Setting Locale from Request: ".$country->code);
		}
		elseif ($this->request->session->get('site_country')) {
			$country = $model->getModel('\core\classes\models\Country')->get([
				'code' => $this->request->session->get('site_country')
			]);
		}
		else {
			$remote_ip = $this->request->serverParam('REMOTE_ADDR');
			$country   = $model->getModel('\modules\location_detect\classes\models\CountryIP4')->findCountry($remote_ip);
			$this->logger->info("Setting Locale from IP: $remote_ip => ".$country->code);
		}

		// Is the country valid
		if ($country && $module_config->allowed_countries) {
			if (!in_array($country->code, $module_config->allowed_countries)) {
				$country = NULL;
			}
		}

		// Is the currency valid
		if ($country && $module_config->allowed_currencies) {
			if (!in_array($country->currency, $module_config->allowed_currencies)) {
				$country = NULL;
			}
		}

		// set the locale
		if ($country && $country->getLocale()) {
			$this->config->setLocale($country->getLocale());
			$this->logger->info('Setting Locale: '.$country->getLocale());
		}
	}
}
