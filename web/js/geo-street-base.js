$(document).ready(function(){
  if (!( $('.field.address, .sf_admin_form_field_address').length == 1
    && $('.field.postalcode, .sf_admin_form_field_postalcode').length == 1
    && $('.field.city, .sf_admin_form_field_city').length == 1 ))
  {
    if ( window.location.debug == '#debug' )
      console.error('Nothing to check out');
    return;
  }
  
  var timeouts = [];
  $('.field.address textarea, .sf_admin_form_field_address textarea').keypress(function(){
    var elt = this;
    
    // clear previous timeouts;
    while ( (timeout = timeouts.shift()) !== undefined )
      clearTimeout(timeout);
    
    // run a new one...
    timeouts.push(setTimeout(function(){
      if ( window.location.hash == '#debug' )
        console.error('Beginning address autocompletion')
      $(elt).closest('.field.address, .tdp-address').find('select').remove();
      
      // checks if there is something to check
      var city, zip;
      if ( (zip = $.trim($('.field.postalcode input, .sf_admin_form_field_postalcode input').val())).length < 3 )
        return;
      if ( (city = $.trim($('.field.city input, .sf_admin_form_field_city input').val())).length < 3 )
        return;
      if ( (address = $.trim($('.field.address textarea, .sf_admin_form_field_address textarea').val())).replace("\n", '').length < 5 )
        return;
      
      $(elt).addClass('waiting-wheel');
      
      var url = '/rp_dev.php/geo_fr_street_base/ajax';
      if ( window.location.hash == '#debug' )
        console.error('Maybe some autocomplete would be relevant...', url);
      
      // autocomplete
      $.ajax({
        method: 'get',
        url: url,
        data: {
          city: city,
          zip: zip,
          address: address
        },
        complete: function(){
          $(elt).removeClass('waiting-wheel');
        },
        success: function(json){
          if ( window.location.hash == '#debug' )
            console.error(json);
          if ( json.length == 0 )
            return;
          
          var address;
          $('.field.address, .tdp-address').addClass('autocompleting');
          var addresses = $('<select></select>').addClass('addresses').prop('size', 3)
            .hide().insertAfter($(elt).closest('.field.address textarea, .tdp-address textarea')).fadeIn()
            .each(function(){
              $(this).closest('.field.address, .tdp-address').find('select').not(this).remove();
            })
            .change(function(){
              var newAddr = $(this).val();
              $(this).closest('.field.address, .tdp-address').find('textarea').each(function(){
                var lines = $(this).val().split("\n");
                lines.pop();
                lines.push(newAddr);
                $(this).val(lines.join("\n"));
              });
              $(this).closest('.field.address, .tdp-address').find('select').fadeOut(function(){
                $(this).closest('.field.address, .tdp-address').removeClass('autocompleting');
                $(this).remove();
              });
            })
          ;
          while ( (address = json.shift()) )
            $('<option></option>').val(address).html(address)
              .appendTo(addresses);
        }
      });
    },300)); // the timeout
  });
});