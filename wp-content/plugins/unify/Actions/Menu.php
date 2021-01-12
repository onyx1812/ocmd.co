<?php

namespace CodeClouds\Unify\Actions;

/**
 * Menu handler.
 * @package CodeClouds\Unify
 */
class Menu
{
    /**
     * Add settings to the menu.
     */
    public static function add_settings_to_menu()
    {
		add_menu_page(
			'Dashboard', 
			'Dashboard',
			'manage_options',
			'unify-dashboard', 
			['CodeClouds\Unify\Actions\Dashboard', 'dashboard_page'],
			plugins_url('/unify/assets/images/unify-white-icon.svg'),
			2 
		);
		

        add_submenu_page(
            'unify-dashboard',
            __('Connectivity', 'unify-connection'),
            __('Connectivity', 'unify-connection'),
            'manage_options',
            'unify-connection',
            ['CodeClouds\Unify\Actions\Connection', 'connection_page']
        );
		
        add_submenu_page(
            'unify-dashboard',
            __('Tools', 'unify-tools-new'),
            __('Tools', 'unify-tools-new'),
            'manage_options',
            'unify-tools',
            ['CodeClouds\Unify\Actions\Tools', 'tools_page']
        );		
        
//        add_submenu_page(
//            'unify-dashboard',
//            __('About', 'unify-about'),
//            __('About', 'unify-about'),
//            'manage_options',
//            'unify-about',
//            ['CodeClouds\Unify\Actions\About', 'about_page']
//        );

        add_submenu_page(
            'unify-dashboard',
            __('Settings', 'unify-settings'),
            __('Settings', 'unify-settings'),
            'manage_options',
            'unify-settings',
            ['CodeClouds\Unify\Actions\Settings', 'setting']
        );

        add_submenu_page(
            'unify-dashboard',
            __('Upgrade to Pro', 'unify-upgrade-to-pro'),
            __('Upgrade to Pro', 'unify-upgrade-to-pro'),
            'manage_options',
            'unify-upgrade-to-pro',
            ['CodeClouds\Unify\Actions\Dashboard', 'unify_upgrade_to_pro']
        );
    }
	
	public static function alter_menu_label()
	{
		global $menu;
		foreach ($menu as $key => $m)
		{
			if (!empty($m[2]) && $m[2] == 'unify-dashboard')
			{
				$menu[$key][0] = 'Unify'; //changing the Menu Label
				break;
			}
		}
	}

}
