jQuery(document).ready(function() {
  //setup toolbar slider for sizing customization
  jQuery("#slider").slider({
    slide: function( event, ui ) {
        jQuery("#toolbar-pixels").val(ui.value);
    },
    min: 15, //0 px not allowed, as this value is checked later for being true and 0 is considered false 
    max: 60,
    value: jQuery("#toolbar-pixels").val(),
    disabled: !jQuery('#toolbar-setting').prop('checked')
  });
  //disable customization controls if toolbar not enabled
  jQuery('#toolbar-setting').click(function(){
    if(!this.checked){
      jQuery('#toolbar-text').prop('disabled', true);
      jQuery("#slider").slider( "option", "disabled", true );
      jQuery('input[name="dealsbar_options[toolbar-position]"]').prop('disabled', true);
      jQuery('#toolbar-pixels').prop('disabled', true);
      jQuery('td > .wp-picker-container').hide();
      jQuery('#toolbar-custom-css').prop('disabled', true);
      jQuery('#toolbar-merchants').prop('disabled',true);
      jQuery('#toolbar-afftrack').prop('disabled',true);
    }else if(this.checked){
      jQuery('#toolbar-text').prop('disabled', false);
      jQuery("#slider").slider( "option", "disabled", false );
      jQuery('input[name="dealsbar_options[toolbar-position]"]').prop('disabled', false);
      jQuery('#toolbar-pixels').prop('disabled', false);
      //jQuery('#toolbar-bg-color').wpColorPicker( "option", "disabled", false ); --also won't work
      jQuery('td > .wp-picker-container').show();
      jQuery('#toolbar-custom-css').prop('disabled', false);
      jQuery('#toolbar-merchants').prop('disabled',false);
      jQuery('#toolbar-afftrack').prop('disabled',false);
    }
  });
  //if user uses text input instead of slider, reflect changes in slider too...
  jQuery('#toolbar-pixels').change(function(){
    jQuery('#slider').slider( "value", this.value );
    console.log('change for toolbar pixels input happened');
  });
  //initialize color picker for toolbar customization
  jQuery('#toolbar-bg-color, #toolbar-text-color').wpColorPicker({
    //disabled: !jQuery('#toolbar-setting').prop('checked'), --no disabled method available on init...
    palettes: true
  });
  //don't show color picker if the toolbar enable checkbox was not selected. unfortunately no way to just initialize it disabled above
  jQuery('td > .wp-picker-container').toggle(jQuery('#toolbar-setting').prop('checked'));
  //normal html label doesn't work for jquery slider, so recreate same functionality by focusing the slider on label's click
  jQuery('label[for="toolbar-size"]').click(function(){
    if(jQuery('#toolbar-setting').prop('checked')){
      jQuery('#slider > span').focus();
    }
  });
  //make it so that if you click an optgroup header, it selects ALL children (merchant stores) and stops it from snapping around the scroll
  jQuery("#toolbar-merchants").click(function(e) {
  if(e.target.tagName == "OPTGROUP"){
    var node = e.target.firstChild;
    var st = this.scrollTop;
    do{
      node.selected= true; 
    }
    while((node = node.nextSibling));
      this.scrollTop = st;
    }
  });
});