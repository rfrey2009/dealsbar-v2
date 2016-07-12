jQuery(document).ready(function() {

	if(dealsbar_toolbar_settings.is_backend){ //check whether we're on the settings page or live blogger's front-end
		jQuery("#slider").on( "slide slidechange", function( event, ui ) {

			jQuery("#dealsbar_deals_toolbar").css('height',ui.value);
	    	jQuery("span#dealsbar_deal, div#dealsbar_toolbar_navi").css('font-size', ui.value / 2);

		});
		jQuery("#toolbar-bg-color, #toolbar-text-color").wpColorPicker("option","change",function(e, ui){
			var target = e.target.id;
	        var color = ui.color.toString();

	        if(target == "toolbar-bg-color")
	        	jQuery("#dealsbar_deals_toolbar").css('background-color',color);

	        else if(target == "toolbar-text-color")
	        	jQuery("#dealsbar_deals_toolbar").css('color',color);
	    });
	    jQuery('#toolbar-setting').click(function(){
		    if(!this.checked){
		    	jQuery("#dealsbar_deals_toolbar").hide();
		    }else if(this.checked){
		    	jQuery("#dealsbar_deals_toolbar").show();
		    }
	  	});
	  	jQuery('#toolbar-position-top, #toolbar-position-bottom').change(function(){

	  		if(this.value == 'top')
	  			jQuery('#dealsbar_deals_toolbar').css({top: 0, bottom: ''});

	  		else if(this.value == 'bottom')
	  			jQuery('#dealsbar_deals_toolbar').css({bottom: 0, top: ''});
	  	});
	  	jQuery('#toolbar-text').keyup(function(e){

	        var text = this.value;

	        jQuery('#dealsbar_deal_title').text(text);


	  	});
	}

	var btCurIndex = parseInt(dealsbar_toolbar_settings.start_index); //parseInt required because https://core.trac.wordpress.org/ticket/25280
	jQuery('#dealsbar_toolbar_navi > i').click(function(e){	  		
		
		var target = e.target.id;
		var deals = dealsbar_toolbar_settings.deals;	  		

		if(target == "dealsbar_toolbar_left")
			btCurIndex = (btCurIndex > 0 ? btCurIndex - 1 : deals.length - 1);

		if(target == "dealsbar_toolbar_right")
			btCurIndex = (btCurIndex == deals.length - 1 ? 0 : btCurIndex + 1);

		if(target == "dealsbar_toolbar_close")
			jQuery('#dealsbar_deals_toolbar').hide();

		var deal = deals[btCurIndex];	  		
		jQuery('a#dealsbar_deal_text').text(deal.description + ' - ' + deal.merchant).attr("href", deal.trackingurl);

	});	
});