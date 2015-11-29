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
		$country = NULL;
		if ($this->request->requestParam('set_country')) {
			$country = $model->getModel('\core\classes\models\Country')->get([
				'code' => $this->request->requestParam('set_country')
			]);
			$this->request->session->set('site_country', $country->code);
		}
		elseif ($this->request->session->get('site_country')) {
			$model     = new Model($this->config, $this->database);
			$country = $model->getModel('\core\classes\models\Country')->get([
				'code' => $this->request->requestParam('set_country')
			]);
		}
		else {
			$remote_ip = $this->request->serverParam('REMOTE_ADDR');
			$model     = new Model($this->config, $this->database);
			$country   = $model->getModel('\modules\location_detect\classes\models\CountryIP4')->findCountry($remote_ip);
			$this->logger->info("Setting Locale from IP: $remote_ip => ".$country->code);
		}

		if ($country && $country->getLocale()) {
			$this->config->setLocale($country->getLocale());
			$this->logger->info('Setting Locale: '.$country->getLocale());
		}
	}
}
