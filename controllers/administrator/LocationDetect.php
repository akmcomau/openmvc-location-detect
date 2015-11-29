<?php

namespace modules\location_detect\controllers\administrator;

use core\classes\exceptions\RedirectException;
use core\classes\exceptions\SoftRedirectException;
use core\classes\renderable\Controller;
use core\classes\Model;
use core\classes\Pagination;
use core\classes\FormValidator;

class LocationDetect extends Controller {

	protected $show_admin_layout = TRUE;

	protected $permissions = [
		'config' => ['administrator'],
		'index' => ['administrator'],
		'add' => ['administrator'],
		'brands' => ['administrator'],
		'categories' => ['administrator'],
		'attributes' => ['administrator'],
	];

	public function config() {
		$this->language->loadLanguageFile('administrator/location_detect.php', 'modules'.DS.'location_detect');
		$module_config = $this->config->moduleConfig('\modules\location_detect');
		$form = $this->getConfigForm();

		if ($form->validate()) {
			$values = $form->getSubmittedValues();
			$model = (new Model($this->config, $this->database))
				->getModel('\modules\location_detect\classes\models\CountryIP4')
				->updateCountryIps($values['url']);
		}

		$data = [
			'terms_url' => $module_config->terms_url,
			'download_url' => $module_config->download_url,
		];

		$template = $this->getTemplate('pages/administrator/config.php', $data, 'modules'.DS.'location_detect');
		$this->response->setContent($template->render());
	}

	protected function getConfigForm() {
		$inputs = [
			'url' => [
				'type' => 'string',
				'required' => TRUE,
				'message' => $this->language->get('error_url'),
			],
		];

		return new FormValidator($this->request, 'form-location-detect', $inputs);
	}
}
