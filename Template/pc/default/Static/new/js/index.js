		
		var li = document.getElementById("nav").getElementsByTagName("li");
        console.log(li)

		$(li).click(function	(){
			$(this).addClass("cur").siblings().removeClass("cur");
		});




		// 轮播
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


   