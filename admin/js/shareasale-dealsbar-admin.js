jQuery(document).ready( () => {
	(function(){
    	new Clipboard('button#shareasale_dealsbar-copy-button');
	})();
	jQuery('input.shareasale_dealsbar-input').on('keyup, keypress', e => e.which !== 13 );
	jQuery('button#shareasale_dealsbar-copy-button').click( e => { e.preventDefault(); } );
	jQuery('button#shareasale_dealsbar-create-link').click( e => {
		e.preventDefault();
		var destinationURL  = jQuery('input#shareasale_dealsbar-destination').val().replace(/^https?:\/\//,'');
		if (!destinationURL) { 
			jQuery('input#shareasale_dealsbar-result').css('background-color', 'rgba(255, 0, 0, 0.29)').val('').prop('placeholder','gotta choose a destination URL!');
			jQuery('input#shareasale_dealsbar-destination').focus();
			return;
		};

		var encodedURL      = encodeURIComponent(destinationURL);
		var afftrack        = jQuery('input#shareasale_dealsbar-afftrack').val().replace(/[|]/g,'');
		var affiliateID     = shareasale_dealsbar_data['Affiliate ID'];
		var customURL       = 'https://shareasale.com/r.cfm?b=166090&m=21395&u=' + affiliateID + '&afftrack=' + afftrack + '&urllink=' + encodedURL;
		jQuery('input#shareasale_dealsbar-result').css('background-color', 'white').val(customURL).prop('placeholder','').select();
	})
});