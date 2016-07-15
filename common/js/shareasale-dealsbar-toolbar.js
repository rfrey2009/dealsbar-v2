jQuery(document).ready(function() {

	if(dealsbarToolbarSettings.is_backend){ //check whether we're on the settings page or live blogger's frontend
		jQuery("#slider").on( "slide slidechange", function( event, ui ) {

			jQuery("#dealsbar-deals-toolbar").css('height',ui.value);
	    	jQuery("span#dealsbar-deal, div#dealsbar-toolbar-navi").css('font-size', ui.value / 2);

		});
		jQuery("#toolbar-bg-color, #toolbar-text-color").wpColorPicker("option","change",function(e, ui){
			var target = e.target.id;
	        var color = ui.color.toString();

	        if(target == "toolbar-bg-color")
	        	jQuery("#dealsbar-deals-toolbar").css('background-color',color);

	        else if(target == "toolbar-text-color")
	        	jQuery("#dealsbar-deals-toolbar").css('color',color);
	    });
	    jQuery('#toolbar-setting').click(function(){
		    if(!this.checked){
		    	jQuery("#dealsbar-deals-toolbar").hide();
		    }else if(this.checked){
		    	jQuery("#dealsbar-deals-toolbar").show();
		    }
	  	});
	  	jQuery('#toolbar-position-top, #toolbar-position-bottom').change(function(){

	  		if(this.value == 'top')
	  			jQuery('#dealsbar-deals-toolbar').css({top: 0, bottom: ''});

	  		else if(this.value == 'bottom')
	  			jQuery('#dealsbar-deals-toolbar').css({bottom: 0, top: ''});
	  	});
	  	jQuery('#toolbar-text').keyup(function(e){

	        var text = this.value;

	        jQuery('#dealsbar-deal-title').text(text);


	  	});
	}

	var btCurIndex = parseInt(dealsbarToolbarSettings.start_index); //parseInt required because https://core.trac.wordpress.org/ticket/25280
	jQuery('#dealsbar-toolbar-navi > i').click(function(e){	  		
		
		var target = e.target.id;
		var deals = dealsbarToolbarSettings.deals;	  		

		if(target == "dealsbar-toolbar-left")
			btCurIndex = (btCurIndex > 0 ? btCurIndex - 1 : deals.length - 1);

		if(target == "dealsbar-toolbar-right")
			btCurIndex = (btCurIndex == deals.length - 1 ? 0 : btCurIndex + 1);

		if(target == "dealsbar-toolbar-close")
			jQuery('#dealsbar-deals-toolbar').hide();

		var deal = deals[btCurIndex];	  		
		jQuery('a#dealsbar-deal-text').text(deal.description + ' - ' + deal.merchant).attr("href", deal.trackingurl);

	});	
});