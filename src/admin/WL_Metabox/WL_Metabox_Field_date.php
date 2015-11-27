<?php


class WL_Metabox_Field_date extends WL_Metabox_Field {
	
	/*
	 * Attribute to distinguish between date formats, inferred from the schema property export type
	 * 
	 * @since 3.2.0
	 */
	private $date_format;
	
	/*
	 * Boolean flag to decide if the calendar should include time or not
	 * 
	 * @since 3.2.0
	 */
	private $timepicker;
	
	public function __construct( $args ) {
		
		// Call parent constructor
		parent::__construct( $args );
		
		// Distinguish between date and datetime	
		$this->date_format = 'Y/m/d';		// Default is date
		if( isset( $this->raw_custom_field['export_type'] ) && 'xsd:datetime' === $this->raw_custom_field['export_type'] ) {
			$this->date_format .= ' H:i';
		}
		$this->timepicker = ( strpos( $this->date_format, 'H:i') !== false );
	}
    
    public function html_input( $date ) {
        
		$pickerDate = ( empty( $date ) ? '' :  esc_attr( date( $this->date_format, strtotime( $date ) ) ) );
		
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
    
    public function html_wrapper_close() {
		
		$meta_name		  = $this->meta_name;
		$meta_name_hidden = $this->meta_name . '_hidden';
		
		// Should the widget include time picker?
		$timepicker = json_encode( $this->timepicker );
          
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

