		
            var li = document.getElementById("nav").getElementsByTagName("li");
        $(li).mouseenter(function   (){
            $(this).addClass("cur").siblings().removeClass(".nave .cur");
        });
        
		
	
		// ***************************************轮播******************************
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        paginationClickable: true,
        spaceBetween: 30,
        centeredSlides: true,
        autoplay: 2500,
        autoplayDisableOnInteraction: false
    });


   