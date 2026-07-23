/**
 * Login/Register — Split: focus the username field on load for faster entry.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var field = document.getElementById( 'username' );
		if ( field ) {
			field.focus();
		}
	} );
} )();
