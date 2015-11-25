<?php


class WL_Metabox_Field_date extends WL_Metabox_Field {
	
	/*
	 * Class attribute to distinguish between date formats, inferred from the schema property export type
	 */
	private $date_format;
	
	public function __construct($args) {
		
		// Call parent constructor
		parent::__construct($args);
		
		// Distinguish between date and datetime
		$this->date_format = 'Y/m/d H:i';		// Default is datetime
		if( isset( $this->raw_custom_field['export_type'] ) ) {
			
			if( $this->raw_custom_field['export_type'] == 'xsd:date'){
				$this->date_format = 'Y/m/d';	// Date without time
			}
			
			// Future formats can be managed here
		}
	}
    
    public function html_input( $date ) {
        
        $pickerDate  = '';
        if( !empty( $date ) ){
            $pickerDate = date( $this->date_format, strtotime( $date ) );
        }
        $pickerDate = esc_attr( $pickerDate );
		
		$meta_name		  = $this->meta_name;
		$meta_name_hidden = $this->meta_name . '_hidden';
        
        $html = <<<EOF
			<div class="wl-input-wrapper">
				<input type="text" class="$meta_name" value="$pickerDate" style="width:88%" />
				<input type="hidden" class="$meta_name_hidden" name="wl_metaboxes[$meta_name][]" value="$date" />      
				<button class="button wl-remove-input" type="button" style="width:10%">Remove</button>
			</div>
EOF;
		
        return $html;

    }
    
    public function html_wrapper_close(){
		
		$meta_name		  = $this->meta_name;
		$meta_name_hidden = $this->meta_name . '_hidden';
		
		// Should the widget include time picker?
		if( strpos( $this->date_format, 'H:i') !== false ) {
			$timepicker = 'true';
		} else {
			$timepicker = 'false';
		}
          
        $html = <<<EOF
			<script type='text/javascript'>
			$ = jQuery;
			$(document).ready(function() {

				$('.$meta_name').datetimepicker({
					format: '$this->date_format',
					timepicker:$timepicker,
					onChangeDateTime:function(dp, input){
						// format must be: 'YYYY-MM-DDTHH:MM:SSZ' from '2014/11/21 04:00'
						var currentDate = input.val();
						currentDate = currentDate.replace(/(\d{4})\/(\d{2})\/(\d{2}) (\d{2}):(\d{2})/,'$1-$2-$3T$4:$5:00Z')
						// store value to save in the hidden input field
						$('.$meta_name_hidden').val( currentDate );
					}
				});
			});
			</script>
EOF;
        
        $html .= parent::html_wrapper_close();
        
        return $html;
    }
}

