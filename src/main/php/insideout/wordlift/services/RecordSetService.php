<?php

class WordLift_RecordSetService {

	const RESULT_NAME = "result";
	const VARIABLES_NAME = "variables";
	const ROWS_NAME = "rows";
	const CONTENT_TYPE = "Content-Type: application/json";

	public function write( &$recordset, $count = NULL, $offset = NULL ) {

		header( self::CONTENT_TYPE );

		if ( NULL !== $count) {
			echo "{\"total\": $count,";

			if ( NULL !== $offset )
				echo " \"offset\": $offset,";

			echo " \"content\": ";
		}

		$this->writeRecordSet( $recordset );

		if ( NULL !== $count) {
			echo "}";
		}

	}

	private function writeRecordSet( &$recordset ) {

		$result = &$recordset[ self::RESULT_NAME ];
		$variables = &$result[ self::VARIABLES_NAME ];
		$rows = &$result[ self::ROWS_NAME ];
		$variablesCount = count( $variables );
		$rowsCount = count( $rows );

		$rowsIndex = 0;

		echo "[";
		foreach ( $rows as &$row ) {

			echo "{";

			$variablesIndex = 0;
			foreach ( $variables as &$variable ) {

				echo json_encode( $variable );
				echo ": ";
				echo json_encode( $row[ $variable ] );

				if ( ++$variablesIndex !== $variablesCount )
					echo ",";
			}
			echo "}";

			if ( ++$rowsIndex !== $rowsCount )
				echo ",";
		}
		echo "]";

	}
	
}

?>