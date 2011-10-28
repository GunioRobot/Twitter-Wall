/**
  *  Author: Zimtea Robert
  *  Date: 5 December 2009
  *
*/

// Create a self-invoking anonymous function. That way,
// we're free to use the jQuery dollar symbol anywhere within.
(function($){
// we'll allow the user to pass in a couple of parameters.
 $.fn.twitterWall = function(options) {

   		  // Caches this - or the ul widget(s) that was passed in.
		  //  Saves time and improves performance.
		  var c = $(this);

 // If the user doesn't pass in parameters, we'll use this object.
  var defaults = {
	   interval: 5000, //the time delay between ajax checks. Recommended as is.
	   scroolSpeed: 2000,
	   hashtag: '#twitter',
	   loading: "<div id='loading-tweets'></div>" //displayed when tWall is initialised, you can insert everything you want
  };

  // Create a new object that merges the defaults and the
  // user's "options".  The latter takes precedence.
  var options = $.extend(defaults, options);

  //check if twall more is defined, if so, proceed and add the functionality
  if($('#twall-more')){

		//when -more is pressed, get the li value, then add it to the ul height
		$('#twall-more').click(function(){
		//getting the container height
		var containerHeight = $('ul',c).outerHeight();
		var tweetCont = c.find('li').outerHeight();
		var totalHeight = containerHeight+tweetCont;
		$('ul',c).animate({height: totalHeight+'px'});
	  }, 1000, 'swing');

		$('#twall-less').click(function(){
		var containerHeight = $('ul',c).outerHeight();
		var tweetCont = c.find('li').outerHeight();
		var totalHeight = containerHeight-tweetCont;
		$('ul',c).animate({height: totalHeight+'px'});
	  }, 1000, 'swing');
  }


  //Append the hashtag to the specified id, if there is
  if(c.find('#twall-hashtag')){
	c.find('#twall-hashtag').html(options.hashtag);
  }

  return this.each(function() {
		c.append('<ul>'+options.loading+'</ul>'); //appending the list to the selected div

	 // This sets an interval that will be called continuously.
	setInterval(function(){

	var sinceid = $('ul li:first', c).attr('since_id');

		$.ajax({ type: "GET",
				 url: "/TwitterWall/twitterWall.php",
				 data: "since_id="+sinceid+"&hashtag="+escape(options.hashtag),
				 success: function(msg){
												$('ul', c).prepend(msg);
												$('li', c).fadeIn(options.scroolSpeed);
					   }

					});
		var sinceid = $('ul li:first', c).attr('since_id'); //updating the since id variable with the twitter id

	}, options.interval);

  });
 };
})(jQuery);