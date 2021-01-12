<?php

namespace CodeClouds\Unify\Actions;

use CodeClouds\Unify\Service\Request;

class About
{
    /**
     * Plugin's copyright message.
     */
    public static function copyright_msg()
    {
        if(strpos($_SERVER['REQUEST_URI'],"unify") > 0)
		{
            include_once __DIR__ . '/../Templates/footer.php';
        }
    }
    
    /**
     * Plugin's about page.
     */
    public  static function about_page()
    {
        include_once __DIR__ . '/../Templates/about.php';
    }
}
