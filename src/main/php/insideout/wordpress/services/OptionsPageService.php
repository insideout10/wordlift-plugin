<?php

class OptionsPageService {
    
    const SERVICE = "service";
    const TITLE = "title";
    const MENU = "menu";
    const CAPABILITY = "capability";
    const SLUG = "slug";
    const CALLBACK = "callback";
    
    const OPTIONS_PAGE = "options_page";
    
    public static function load($xRayClass) {
        
        if (false === is_array($xRayClass))
            $xRayClass = XRayService::scan($xRayClass);
            
        $className = key($xRayClass);
        
        $descriptors = &$xRayClass[$className][XRayService::DESCRIPTORS];

        $service = $title = $menu = $capability = $slug = null;
        $callback = "display";
        
        foreach ($descriptors as $descriptor) {
            $key = $descriptor[XRayService::KEY];
            $value = $descriptor[XRayService::VALUE];
            
            switch ($key) {
                case self::SERVICE:
                    $service = $value;
                    break;

                case self::TITLE:
                    $title = $value;
                    break;

                case self::MENU:
                    $menu = $value;
                    break;

                case self::CAPABILITY:
                    $capability = $value;
                    break;

                case self::SLUG:
                    $slug = $value;
                    break;

                case self::CALLBACK:
                    $callback = $value;
                    break;
            }
        }
        
        if (self::OPTIONS_PAGE !== $service)
            return;
            
		add_options_page( $title, $menu, $capability, $slug, array(new $className(), $callback));
        
    }
    
}

?>