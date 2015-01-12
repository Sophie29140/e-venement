$(document).ready(function(){
  // the global input from keyboard(s)
  $('form #control_checkpoint_id').change(function(){
    $('#control_ticket_id').focus();
  });
  $('#checkpoint #control_checkpoint_id').keypress(function(e){
    if ( e.which == 13 )
      $('#checkpoint').submit();
  });
  $(document).keydown(function(){
    if ( $('#checkpoint input[name="control[ticket_id]"]:focus, #control_comment:focus').length == 0 )
      $('#checkpoint input[name="control[ticket_id]"]').focus();
  });
  
  // selecting by default the last checkpoint selected
  if ( !$('#checkpoint #control_ticket_id').val() )
  {
    $('#checkpoint input[name="control[ticket_id]"]').focus();
    if ( $('#checkpoint #control_checkpoint_id option').length == 2 )
      $('#checkpoint #control_checkpoint_id option:last-child').attr('selected','selected');
    else if ( $('#checkpoint .settings').attr('data-checkpoint-id') != '' )
      $('#checkpoint #control_checkpoint_id option[value='+$('#checkpoint .settings').attr('data-checkpoint-id')+']')
        .attr('selected','selected');
  }
  else
  {
    if ( $('#checkpoint #control_checkpoint_id option').length == 2 )
      $('#checkpoint #control_checkpoint_id option:last-child').attr('selected','selected');
    else
      $('#checkpoint #control_checkpoint_id').focus();
  }
  
  // this is what happens when the user submits a control
  $('#checkpoint').submit(function(){
    var data = $(this).serialize();
    
    // form reinitialization to accelerate multi-process controls
    $('#checkpoint [name="control[comment]"]').val('');
    $('#checkpoint [name="control[ticket_id]"]').val('').focus();
    
    // the HTTP request
    if ( window.location.hash == '#debug' )
      window.open($(this).prop('action')+'?'+data+'&debug');
    else
    {
      $.get($(this).prop('action'), data, function(json){
        if ( window.location.hash != '#debug' )
          console.error(json);
        
        // the content of the control
        var control = $('<div class="control ui-corner-all ui-widget-content"></div>')
          .insertAfter('#checkpoint .ui-widget-content:first')
          .addClass(json.success ? 'ui-state-success' : 'ui-state-error')
          .prepend('<h2>'+json.timestamp+'</h2>')
        ;
        
        // removing the control's result after a timeout
        setTimeout(function(){
          if ( window.location.hash != '#debug' )
            control.fadeOut(function(){ $(this).remove(); });
        },20000);
        
        // displaying the errors
        if ( typeof json.details.control.errors == 'object' )
        {
          $('<ul class="errors"></ul>').appendTo(control);
          $.each(json.details.control.errors, function(i, error){
            $('<li></li>').text(error).appendTo(control.find('.errors'));
          });
        }
        
        // displaying the message (success/error)
        $('<p></p>').addClass('message').text(json.message)
          .insertAfter(control.find('h2'));
        
        // the transaction
        if ( $(json.tickets).length > 0 )
        {
          var tickets = $('<ul class="tickets"></ul>').insertAfter(control.find('.errors'));
          $.each(json.tickets, function(id, ticket){
            // the ticket itself
            var elt = $('<li></li>').text($('#checkpoint .settings').attr('data-ticket-label')+': #')
              .appendTo(tickets);
            $('<a></a>').text(ticket.id)
              .prop('href', ticket.url)
              .prop('target', '_blank')
              .addClass('ticket')
              .appendTo(elt);
            if ( json.details.contacts[ticket.id] != undefined )
            {
              var contacts = $('<div></div>').addClass('contacts')
                .appendTo(elt);
              
              $.each(['direct_contact', 'contact'], function(i, type){
                if ( !json.details.contacts[ticket.id][type] )
                  return;
                
                $('<hr/>').appendTo(contacts)
                $('<a></a>').text(json.details.contacts[ticket.id][type].name)
                  .addClass(type)
                  .prop('href', json.details.contacts[ticket.id][type].url)
                  .appendTo(contacts)
                ;
                
                var image = $('<div class="image"></div>').appendTo(contacts.find('.'+type));
                if ( json.details.contacts[ticket.id][type].picture_url )
                  $('<img />').prop('alt', '')
                    .prop('src', json.details.contacts[ticket.id][type].picture_url)
                    .prependTo(image);
                else
                  image.html('&nbsp;');
                /*
                else
                  $('<span></span>').addClass('image')
                    .prependTo(contacts.find('.'+type));
                */
                
                if ( json.details.contacts[ticket.id][type].comment )
                  $('<div></div>')
                    .text(json.details.contacts[ticket.id][type].comment)
                    .addClass('comment')
                    .appendTo(contacts);
              });
                
              $.each(['professional', 'organism'], function(i, type){
                if ( json.details.contacts[ticket.id].contact[type] )
                {
                  var pro = $('<div></div>').addClass(type).appendTo(contacts);
                  var link = $(json.details.contacts[ticket.id].contact[type].url != undefined ? '<a></a>' : '<span></span>')
                    .addClass('name')
                    .text(json.details.contacts[ticket.id].contact[type].name)
                    .appendTo(pro);
                  if ( json.details.contacts[ticket.id].contact[type].url != undefined )
                    link.prop('href', json.details.contacts[ticket.id].contact[type].url);
                  if ( json.details.contacts[ticket.id].contact[type].comment )
                    $('<div></div>').addClass('comment')
                      .text(json.details.contacts[ticket.id].contact[type].comment)
                      .appendTo(pro);
                }
              });
            }
          });
        }
        
        $('.control').click(function(){
          $(this).height($(this).height());
          if ( $(this).height() < $(this).find('.contacts').height() )
            $(this).height($(this).find('.contacts').height());
        });
      });
    }
    
    return false;
  });
});