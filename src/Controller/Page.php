<?php

namespace App\Controller;

class Page
{
    public static $context = 'Body page of site<br>';

    public static function footer()
    {
        return 'Footer page of site<br>';
    }

    public static function header()
    {
        return 'Header page of site<br>';
    }

    public static function site()
    {
        echo self::header().self::$context.self::footer();
    }
}
