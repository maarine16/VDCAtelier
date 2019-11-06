$(document).ready(function(){

    if ($('#photo').length == 1) {
        $('#photo').change(function(e){
            let reader = new FileReader();
            reader.onload = function(e){
                if($('img').length == 1) {
                $("img").attr('src',e.target.result);
                } else {
                    $('#photo').prev('label').before('<img src="' + e.target.result + '" class="thumbnail img-fluid">');
                }   
            }
            if(e.target.result.length > 0) {
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }


    // Sticky Header
$(window).scroll(function() {

    if ($(window).scrollTop() > 200) {
        $('.content-nav-layout').addClass('sticky');
        $('.logo').addClass('logo-sticky');
    } else {
        $('.content-nav-layout').removeClass('sticky');
        $('.header-layout .logo').removeClass('logo-sticky');
    }
});

$(window).resize(function() {
    if ($(window).width() > 991) {
        $(".content-nav-layout").css("overflow", "inherit");
        if ($('.dropdown-menu').hasClass('show')) {
            $('.dropdown-menu').removeClass('show');
        }
    } else {
        $(".content-nav-layout").css("overflow", "hidden");
    }
});

// Mobile Navigation
$('.mobile-toggle').click(function() {
    if ($('.content-nav-layout').hasClass('open-nav')) {
        $('.content-nav-layout').removeClass('open-nav');
        $('.mobile-toggle').removeClass('mobile-toggle-rotate');
    } else {
        $('.content-nav-layout').addClass('open-nav');
        $('.mobile-toggle').addClass('mobile-toggle-rotate');
    }
});

$('.content-nav-layout > li > a').click(function() {
    if ($('.content-nav-layout').hasClass('open-nav')) {
        $('.navigation').removeClass('open-nav');
        $('.content-nav-layout').removeClass('open-nav');
    }
});


});


// REQUETE AJAX

$(function(){
    $('#valid_code').click(function(){
        $code_promo = $('#code_promo').val();
        console.log($code_promo);
        $.ajax({
            // cibler une url
            url: "web/produit/produitcontroller.php",
            // envoyer une valeur (l'indice)
            data: {indice : $code_promo},
            // indiquer la méthode pour la requete 
            method: 'get',
            contentType: String,
            // fonction pour traiter l'information recue du serveur
            success: function(leTexte){
                console.log(leTexte);
                // $('.code_promo').append( leTexte );
                // $('.action').animate({
                //     left: 0,
                // }, 1000).removeClass('action');
            }
        });
    });
});


// $(function(){

//     $('input[type=checkbox]').change(function () {
//         if ($(this).prop("checked")) {
//             $value = $(this).attr('value'); // retourn la value du checkbox

//             $.ajax({
//                 type: "GET",
//                 dataType: 'json',
//                 url:  "/ajax",
//                 data: {indice : $value},
//                 success: function($response)
//                 {
//                     alert('ok');
//                     console.log($response);
                

//                     // $.each(msg, function(index, product)
//                     // {
//                     //     $('.ChoiceProductSearch').html('');
//                     //     $('.ChoiceProductSearch').append('<option value="'+ product.id +'" selected="selected"> '+ product[0].name +' </option>');
//                     // });
//                 }
//             });
//         }
//         //Here do the stuff you want to do when 'unchecked'
//     });

// });