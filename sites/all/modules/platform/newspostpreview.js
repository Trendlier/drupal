
(function($){
    
if($(".node-news_post-form") != null){
    setInterval(function(){displayPreview()}, 3000);
} 
})(jQuery);

function displayPreview(){
    $ = jQuery;
    if($("#newspostpreview") != null){
        $("#newspostpreview").html("");
    }
 
    var bodyHTML = $(".cke_wysiwyg_frame").contents().find("body")[0];
    var title = "<h1>"+$("#edit-title").val()+"</h1>";
    var mainImage = $(".image-preview")[0].innerHTML;
    
    
    
    $($(".content")[0]).append('<div id="newspostpreview"></div>');
    
    $("#newspostpreview").css({'width':'100%', 'height':'342px','-webkit-column-gap':'10px',
                '-moz-column-gap':'10px',
                'column-gap': '10px', 
                '-webkit-column-width':'296px',
                '-moz-column-width':'296px',
                'column-width':'296px'});
    
    var imgs = $(bodyHTML).find('img');
    
    for(i = 0; i < imgs.length; i++){
        $(imgs[0]).css({'max-width': '296px'});
    }
    
    $("#newspostpreview").append(mainImage);
    $("#newspostpreview").append(title);
    $("#newspostpreview").append(bodyHTML.innerHTML);
 
}
