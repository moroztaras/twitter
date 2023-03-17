'use strict';
(function($) {
  var form = $('.form-svistyn-add');
  var preview = $('#svistyn_add_preview');
  var imageInput = $('.svistyn-upload-image');
  imageInput.fileupload({
    formData: { handler: 'svistyn_image'},
    done: function (e, data) {

    },
    success: function(result, textStatus, jqXHR){
      if("messages" in result){
        var parent = $('.svistyn-upload-image').parent();
        if(parent.find('.error-form').length <= 0){
          parent.append('<div class="error-form"></div>');
        }
        parent.find('.error-form').html('<ul class="list r-li"><li class="item">'+result['messages']['warning'][0]+'</li></ul>');
        return;
      }
      if(!("image_uploaded" in result)){
        return;
      }
      preview.find('.svistyn-fd--photo').html("<img src='"+result["image_uploaded"]+"?1'/>").removeClass('hide');
    }
  });
  form.find('.form-row-text textarea').on('keyup', function () {
    var value = $(this).val();
    var textInput = $("#svistyn_add_preview").find('.svistyn-fd--text');
    if(value == ''){
      textInput.addClass('hide');
    } else {
      value = value.replace(/\n/g, "<br />");
      textInput.html('<p>'+value+"</p>").removeClass('hide');
    }

  })
})(jQuery);