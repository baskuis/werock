;(function($){
	$.fn.tabbify = function(options) {
		
		//set configuration
		var settings = $.extend({
			currentIndex: 0
		}, options);
		
		//allow passing down
		var object = this;
		
		//define containers
		var content_containers = [];
		
		//get max height
		var max_height = 0;
		
		//load content containers
		$(this).find('.tab_content').each(function(){
			content_containers.push(this);
			if(max_height < $(this).outerHeight()){
				max_height = $(this).outerHeight();
			}
		}).css('height', max_height + 'px');
		$(this).find('.tabs_content_holder').css('height', max_height + 'px');
		
		//define tabs
		var tabs = [];
		
		//load tabs
		$(this).find('ul.tabControl li').each(function(){
			tabs.push(this);
		});
		
		//tab click listener
		$(this).find('ul.tabControl li').click(function(){
			settings.currentIndex = $('ul.tabControl li').index(this);
			var offset = $(this).offset();
			$('body,html').animate({scrollTop: offset.top - 15}, 500, 'swing');
			setState();
		});
		
		//update state to current index
		function setState(){
			
			//show the loader briefly
			$(object).find('.loader').show();
			
			//modify tab
			for(t in tabs){
				if(settings.currentIndex == t){	
					$(tabs[t]).addClass('on');
				}else{
					$(tabs[t]).removeClass('on');
				}
			}
			
			//select correct content container
			for(x in content_containers){
				if(settings.currentIndex == x){
					$(content_containers[x]).fadeIn(300);
				}else{
					$(content_containers[x]).hide();
				}
			}
			
			//hide the loader
			setTimeout(function(){
				$(object).find('.loader').hide();
			}, 300);
		}
		
		//set state to current index
		setState(settings.currentIndex);
		
		//allow chaining
		return this;
		
	}
}(jQuery));

//tabbify
$().ready(function(){
	$('#bigTabs').tabbify();
});