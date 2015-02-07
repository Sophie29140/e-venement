<?php if ( !$object->getTable()->hasRelation('Picture') || $object->isNew() ) return; ?>
<div class="tdp-picture" data-id="<?php echo $object->id ?>">
  <?php use_javascript('helper') ?>
  <?php use_javascript('photobooth') ?>
  <div class="current">
    <a href="<?php echo url_for($sf_context->getModuleName().'/delPicture?id='.$object->id) ?>" target="_blank">x</a>
    <?php if ( $object->picture_id ): ?>
      <?php echo $object->Picture->getRawValue()->render() ?>
    <?php else: ?>
      <img />
    <?php endif ?>
  </div>
  <div class="webcam small"><button class="start"><?php echo image_tag('camera.png') ?></button></div>
  <!--
  <a
    data-text-query="<?php echo __('Facebook ID') ?>"
    class="facebook"
    href="https://www.facebook.com/%%ID%%"
    target="_blank"
  ><?php echo image_tag('facebook.png') ?></a>
  -->
  <input class="file" type="file" name="file" />
  
  <script type="text/javascript"><!--
    if ( LI == undefined )
      var LI = {};
    
    LI.rpFileUpload = function(img)
    {
      $.ajax({
        type: 'post',
        url: '<?php echo url_for($sf_context->getModuleName().'/newPicture') ?>',
        data: {
          id: $('.tdp-picture').attr('data-id'),
          image: img.replace(/^data:image\/.{3,9};base64,/, ''),
          type: img.replace(/^data:/,'').replace(/;base64,.*$/,'')
        },
        success: function(){
          console.log('Picture changed');
          $('.tdp-picture .current img').prop('src', img);
        }
      });
    }
    
    $(document).ready(function(){
      // delete picture
      $('.tdp-picture .current a').click(function(){
        var picture = $(this).parent().find('img');
        $.get($(this).prop('href'), function(){
          $(picture).prop('src','').prop('alt','');
        });
        return false;
      });
      
      // file upload
      $('.tdp-picture input[type=file]').change(function(){
        var fread = new FileReader();
        if ( $(this).prop('files')[0].type.match('image.*') )
        {
          fread.onloadend = function(){ LI.rpFileUpload(fread.result); }
          fread.readAsDataURL($(this).prop('files')[0]);
        }
        $(this).val('');
        return false;
      });
      
      // cam capture
      LI.ifMediaCaptureSupported(function(){
        $('.tdp-picture .webcam .start').click(function(){
          $(this).closest('.webcam').removeClass('small').photobooth().on('image', function(event, data){
            LI.rpFileUpload(data);
          });
          $(this).remove();
          return false;
        });
      },function(){
        $('.tdp-picture .webcam').remove();
        console.log('Media capture is not supported or no media is available...');
      });
    });
  --></script>
</div>