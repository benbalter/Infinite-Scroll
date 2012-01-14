jQuery(document).ready(function($) {
	$(".nav-tab-wrapper .nav-tab").click(function (event) {
		event.preventDefault();
		$newactive = $(this).attr("rel");
		if($newactive=='presets')
			jQuery(".infscroll_preset_list").infinitescroll("resume");
		else
			jQuery(".infscroll_preset_list").infinitescroll("pause");
		$(".infscroll-tab-active").removeClass("infscroll-tab-active");
		$(".infscroll-tab-"+$newactive).addClass("infscroll-tab-active");
		$(".nav-tab-wrapper .nav-tab-active").removeClass("nav-tab-active");
		$(this).addClass("nav-tab-active");
	});
	$(".submit input[name=auto_fill]").click(function (event) {
		event.preventDefault();
		$(document.infinitescrollform["infscr_options[infscr_content_selector]"]).val($(this).siblings("input[name=auto_fill_content]").val());
		$(document.infinitescrollform["infscr_options[infscr_post_selector]"]).val($(this).siblings("input[name=auto_fill_post]").val());
		$(document.infinitescrollform["infscr_options[infscr_nav_selector]"]).val($(this).siblings("input[name=auto_fill_nav]").val());
		$(document.infinitescrollform["infscr_options[infscr_next_selector]"]).val($(this).siblings("input[name=auto_fill_next]").val());
		$('.infscroll-tab-selectors input[type=text]').animate({backgroundColor: "yellow"},50, function () {
	jQuery('.infscroll-tab-selectors input[type=text]').animate({ backgroundColor: "white" }, 500);});
	});
	
});