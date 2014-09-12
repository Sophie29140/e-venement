$(document).ready(function(){
  // the price_gauges submission
  $('.sf_admin_form .sf_admin_form_field_gauges_prices').niceScroll();
  $('.sf_admin_form .sf_admin_form_field_gauges_prices [data-submit-url] input[type=text]').keydown(function(e){
    if ( e.which != 13 )
      return true;

    var data = {};
    $(this).parent().find('input').each(function(){
      data[$(this).prop('name')] = $(this).val(); 
    });
    
    var input = this;
    $.get($(this).closest('[data-submit-url]').attr('data-submit-url'), data, function(json){
      $.each(json,function(type, msg){
        if ( msg.message )
          LI.alert(msg.message, type);
      });
      if ( json.success.id )
        $(input).parent().find('[name="price_gauge[id]"]').val(json.success.id);
    });
    
    return false;
  });
});
