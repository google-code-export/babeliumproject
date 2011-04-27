function initNavigationLinks()
{
	/* ALL EXCEPT LAST CHILD */
	$("nav#mainnav > ul > li > a").not("nav#mainnav > ul > li:last-child > a").hover(function()
	{
		$(this).parent().css("background","url(themes/babelium/images/button_nav_highlight_"+$(this).attr('class')+".png) no-repeat 50% 58%,"+
										"url(themes/babelium/images/separator.png) no-repeat center right");
	}, function()
	{
		$(this).parent().css("background","url(themes/babelium/images/separator.png) no-repeat center right");
	});
	
	/* LAST CHILD */
	$("nav#mainnav > ul > li:last-child > a").hover(function()
	{
		$(this).parent().css("background","url(themes/babelium/images/button_nav_highlight_about.png) no-repeat 50% 58%");
	}, function()
	{
		$(this).parent().css("background-image","none");
	});
	
	
	/**
	 * On Click Functions
	 */
	$("nav#mainnav > ul > li > a.home").click(function()
	{
		if ( $("#motd").css("display") == "none" )
		{
			$("section#maincontent > header").slideUp(500, function()
			{
				$("#motd").slideDown(500);
			});
		}
	});
	
	$("nav#mainnav > ul > li > a").not(".home").click(function()
	{
		var pressed = $(this);
		if ( $("#motd").css("display") == "none" )
		{
			$("section#maincontent > header").slideUp(500, function()
			{
				$("section#maincontent > header > h1").html(pressed.text());
				$("section#maincontent > header").slideDown(500);
			});
		}
		else
		{
			$("#motd").slideUp(500, function()
			{
				$("section#maincontent > header > h1").html(pressed.text());
				$("section#maincontent > header").slideDown(500);
			});
		}
	});
	
	/**
	 * LOAD INITIAL SECTION
	 */
	var location = $.url.fragment(0);
	$("#motd").slideUp(500);
	$("section#maincontent > header").slideUp(500);
	
	if ( location == null || location == "home" )
		$("#motd").slideDown(500);
	else
		$("section#maincontent > header").slideDown(500);
}