import React from 'react';
import ReactDOM from 'react-dom';
import Keywords from './Keywords';

ReactDOM.render(
	<Keywords
		dataRoute="https://wordpress-4-9-1.localhost/wp-admin/admin-ajax.php"
	/>,
	document.getElementById('wl-keywords-table')
);
