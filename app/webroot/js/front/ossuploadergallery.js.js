/**
 * 
 * OSS uploader Gallery Plugin Uploader 
 * 
 */
(function ( $ ) {
 
    $.fn.ossuploadergallery = function(options) {
        
        return this.each(function(){
            
            
            $.extend( this, {
                href            : "javascript:",
                targetHref      : "#",
                afterSelect     : function(data){alert("my function is " + data);}

            }, options );
            
            $(this).click(jQuery.proxy(function(){
                
                $.fancybox({
                    'href'                          : this.targetHref,
                    'width'                         : 1000,
                    'height'                        : 1000,
                    'autoScale'                     : false,
                    'transitionIn'                  : 'none',
                    'transitionOut'                 : 'none',
                    'type'                          : 'iframe',
                    'beforeClose'                   : jQuery.proxy(function () {
                        var files      = $.fn.ossGalleryResponse;
                        if(files.length > 0){
                            this.afterSelect(files);    
                            $.fn.ossGalleryResponse = Array();
                        }
                        
                    },this)
                });
            },this));
            return this;
        });
        
        
    };
    
    $.extend($.fn,{ossGalleryResponse:Array()});
    
}( jQuery ));


