<?php
require_once dirname(dirname(__FILE__)).'/model/Property.php';

class PropertyService {
	
	public static function create(&$property) {
		$comment = $property->getDocComment();
		$name = $property->getName();
		return self::create_from_comment($comment,$name);
	}
	
	public static function create_from_comment(&$comment,$default_property_name){
		$matches = array();
		$pattern = "/@(.*?)\s(.*)/i";
		$results_count = preg_match_all($pattern, $comment, $matches);
		
		if (0 == $results_count) return;
		
		$meta = array();
		
// 		echo $comment.'<br/>';
// 		echo var_export($matches, true).'<br/>';
		
		for ($i = 0; $i < $results_count; $i++) {
// 			echo $i.': '.var_export($matches[$i], true).'<br/>';
// 			echo '&quot;'.$matches[1][$i].'&quot; => &quot;'.$matches[2][$i].'&quot;<br/>';
			$meta[$matches[1][$i]] = $matches[2][$i];
		}
		
// 		echo var_export($meta,true).'<br/>';
		
		return self::create_from_array($meta,$default_property_name);
	}

	public static function create_from_array(&$array,$default_property_name) {
		// name, type, size
		$property = new Property();
		$property->name = self::get_property_value_from_array('name',$array,$default_property_name);
		$property->type = self::get_property_value_from_array('type',$array,NULL);
		$property->size = self::get_property_value_from_array('size',$array,NULL);
		$property->description = self::get_property_value_from_array('description',$array,NULL);
		$property->multiline = self::get_property_value_from_array('multiline',$array,false);
		
		return $property;
	}
	
	private static function get_property_value_from_array($property, &$array, $default_value) {
		if (false == array_key_exists($property,$array)) return $default_value;
		
		return $array[$property];
	}
	
}

?>