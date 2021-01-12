<?php

namespace CodeClouds\Unify\Actions;

/**
 * Plugin's Assets.
 * @package CodeClouds\Unify
 */
class Assets
{

	/**
	 * Loads required CSS & JS in the Admin section.
	 */
	public static function load_admin_assets_unify_connections()
	{
		if (!empty($_GET['page']) && !empty(strrchr($_GET['page'], 'unify')))
		{
			wp_register_style('toolscss', plugins_url('/../assets/css/tools.css', __FILE__));
			wp_enqueue_style('toolscss');

			wp_register_style('aboutcss', plugins_url('/../assets/css/about.css', __FILE__));
			wp_enqueue_style('aboutcss');

			wp_register_style('gridcss', plugins_url('/../assets/css/grid.css', __FILE__));
			wp_enqueue_style('gridcss');

			wp_register_style('stylecss', plugins_url('/../assets/css/style.css', __FILE__), [], null);
			wp_enqueue_style('stylecss');

			wp_register_style('fontawesome', 'https://use.fontawesome.com/releases/v5.4.1/css/all.css');
			wp_enqueue_style('fontawesome');

			wp_register_style('googleRobotofonts', 'https://fonts.googleapis.com/css?family=Roboto:300,300i,400');
			wp_enqueue_style('googleRobotofonts');

			wp_enqueue_script('jquery');

			wp_register_script('validatejs', plugins_url('/../assets/js/jquery.validate.js', __FILE__),'','2.5.2');
			wp_enqueue_script('validatejs');
			
			wp_register_script('validation', plugins_url('/../assets/js/validation.js', __FILE__),'','2.5.2');
			wp_enqueue_script('validation');
			
			wp_register_script('commonjs', plugins_url('/../assets/js/common.js', __FILE__),'','2.5.2');
			wp_enqueue_script('commonjs');
			
			if (!empty($_GET['page']) && ($_GET['page'] == 'unify-tools'))
			{
				wp_register_script('toolsjs', plugins_url('/../assets/js/tools.js', __FILE__),'','2.5.2');
				wp_enqueue_script('toolsjs');
			}

//			wp_register_script('adminwcsettingsjs', plugins_url('/../assets/js/adminwcsettings.js', __FILE__));
//			wp_enqueue_script('adminwcsettingsjs');
			
			if (!empty($_GET['page']) && ($_GET['page'] == 'unify-connection') && !empty($_GET['section']) && ($_GET['section'] == 'create-connection'))
			{
				wp_register_script('addconnectionjs', plugins_url('/../assets/js/add-connection.js', __FILE__),'','2.5.2');
				wp_enqueue_script('addconnectionjs');
			}
			
			if (!empty($_GET['page']) && ($_GET['page'] == 'unify-settings'))
			{
				wp_register_script('settingsjs', plugins_url('/../assets/js/settings.js', __FILE__),'','2.5.2');
				wp_enqueue_script('settingsjs');
			}
			
			if (!empty($_GET['page']) && ($_GET['page'] == 'unify-connection'))
			{
				wp_register_script('connectionListjs', plugins_url('/../assets/js/connection-list.js', __FILE__),'','2.5.2');
				wp_enqueue_script('connectionListjs');
			}
			
			if (!empty($_GET['page']) && ($_GET['page'] == 'unify-upgrade-to-pro'))
			{
				wp_register_script('upgradetoprojs', plugins_url('/../assets/js/upgrade-to-pro.js', __FILE__),'','2.5.2');
				wp_enqueue_script('upgradetoprojs');
			}
		}
	}

}
