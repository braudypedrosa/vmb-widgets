jQuery(document).ready(function(){

	jQuery('.vmb-reviews').slick({
		dots: true,
		infinite: true,
		speed: 300,
		slidesToShow: 2,
		slidesToScroll: 1,
		adaptiveHeight: true,
		responsive: [
			{
			  breakpoint: 767,
			  settings: {
				slidesToShow: 1,
				slidesToScroll: 1
			  }
			},
		  ],
		nextArrow:'<i class=\"fa fa-solid fa-chevron-right slick-next\"></i>',
		prevArrow:'<i class=\"fa fa-solid fa-chevron-left slick-prev\"></i>'
	});

	jQuery('.vmb-specials').slick({
		dots: true,
		infinite: true,
		speed: 300,
		slidesToShow: 3,
		slidesToScroll: 1,
		adaptiveHeight: true,
		responsive: [
			{
			  breakpoint: 767,
			  settings: {
				slidesToShow: 1,
				slidesToScroll: 1
			  }
			},
		],
		nextArrow:'<i class=\"fa fa-solid fa-chevron-right slick-next\"></i>',
		prevArrow:'<i class=\"fa fa-solid fa-chevron-left slick-prev\"></i>'
	});



});