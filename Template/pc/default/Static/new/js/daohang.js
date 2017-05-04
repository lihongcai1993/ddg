function daohang() {
         var li = document.getElementById("nav").getElementsByTagName("li");
        $(li).mouseenter(function   (){
            $(this).addClass("cur").siblings().removeClass("cur");
        });
}		
       
        
		
	

   