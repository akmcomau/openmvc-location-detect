<?php

namespace modules\location_detect;

use ErrorException;
use core\classes\Config;
use core\classes\Database;
use core\classes\Language;
use core\classes\Model;
use core\classes\Menu;

class Installer {
	protected $config;
	protected $database;

	public function __construct(Config $config, Database $database) {
		$this->config = $config;
		$this->database = $database;
	}

	public function install() {
		$model = new Model($this->config, $this->database);

		$table = $model->getModel('\\modules\\location_detect\\classes\\models\\CountryIP4');
		$table->createTable();
		$table->createIndexes();
		$table->createForeignKeys();
	}

	public function uninstall() {
		$model = new Model($this->config, $this->database);

		$table = $model->getModel('\\modules\\location_detect\\classes\\models\\CountryIP4');
		$table->dropTable();
	}

	public function enable() {
		$language = new Language($this->config);
		$language->loadLanguageFile('administrator/location_detect.php', DS.'modules'.DS.'location_detect');

		$layout_strings = $language->getFile('administrator/layout.php');
		$layout_strings['users_module_location_detect'] = $language->get('location_detect');
		$language->updateFile('administrator/layout.php', $layout_strings);

		$main_menu = new Menu($this->config, $language);
		$main_menu->loadMenu('menu_admin_main.php');
		$main_menu->insert_menu(['users', 'location_detect'], 'users_location_dectect', [
			'controller' => 'administrator/LocationDetect',
			'method' => 'index',
			'text_tag' => 'users_module_location_detect',
		]);
		$main_menu->update();
	}

	public function disable() {
		$language = new Language($this->config);

		$layout_strings = $language->getFile('administrator/layout.php');
		unset($layout_strings['users_module_location_detect']);
		$language->updateFile('administrator/layout.php', $layout_strings);

		// Remove some menu items to the admin menu
		$main_menu = new Menu($this->config, $language);
		$main_menu->loadMenu('menu_admin_main.php');
		$menu = $main_menu->getMenuData();
		unset($menu['users']['children']['location_detect']);
		$main_menu->setMenuData($menu);
		$main_menu->update();
	}
}
