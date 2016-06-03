jQuery(document).ready(function($) {
	
	// Activate FitVids
	$("body").fitVids();
	
	// toggle blog-menu
	$(".nav-toggle").on("click", function(){	
		$(this).toggleClass("active");
		$(".navigation").slideToggle(function (){
			$(".navigation").css('overflow', 'visible');
		});
	});
	
	// Load Flexslider
    $(".flexslider").flexslider({
        animation: "slide",
        controlNav: false,
        prevText: "Prev",
        nextText: "Next",
        smoothHeight: true   
    });
	
	// Smooth scroll to the top	
    $('.tothetop').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 500);
        return false;
    });
	
	// Add custom audio player
	$('#audio-player').mediaelementplayer({
	    alwaysShowControls: true,
	    features: ['playpause','progress','volume'],
	    audioVolume: 'horizontal',
	    audioWidth: 1000,
	    audioHeight: 100,
	    autosizeProgress:false,	    
	}); 
	

});