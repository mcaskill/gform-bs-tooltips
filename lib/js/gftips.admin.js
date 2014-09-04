//***************************************************************
// start the engine
//***************************************************************

jQuery( document ).ready( function($) {

	if ( typeof fieldSettings === 'undefined' ) {
		return;
	}

	// pull in our allowed types
	var allowed	= gftipsAdmin.fieldtypes;

	// loop through each possible field type and add our new field
	jQuery.each( fieldSettings, function( type, items ) {

		if( jQuery.inArray( type, allowed ) > -1 ) {
			fieldSettings[ type ] += ', .tooltip_title_setting, .tooltip_content_setting';
		}

	});

	// bind to the load field settings event to initialize the field
	jQuery( document ).bind( 'gform_load_field_settings', function( event, field, form ) {
		/*jshint sub: true */
		jQuery( '#tooltip_title' ).val( field['tooltipTitle'] === undefined ? '' : field['tooltipTitle'] );
		jQuery( '#tooltip_content' ).val( field['tooltipContent'] === undefined ? '' : field['tooltipContent'] );
	});

//***************************************************************
// You're still here? It's over. Go home.
//***************************************************************

});
