$(document).ready(function(){
    if ($('#app_product_fileEntity').length == 1){
        $('#app_product_fileEntity').change(function(e){
          let reader = new FileReader();
          reader.onload = function(e){ 
            if($('img').length==1){
              $("img").attr('src',e.target.result);
            }
            else{
              $('.custom-file-label').after('<img src="' + e.target.result + '" class="thumbnail img-fluid">');
            }
          }
          if (e.target.files.length > 0 )
          reader.readAsDataURL(e.target.files[0]);
        });
    }
  
  });