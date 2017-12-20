
$(function(){
//	$('#tab_contents div[id != "tab1"]').hide();
	$('#tab_contents #tab2').hide();
	$("#tab_menu a").click(function(){
//		$("#tab_contents div").hide();
		$("#tab_contents .divtab").hide();
		$($(this).attr("href")).show();
		$(".current").removeClass("current");
		$(this).addClass("current");
		return false;
	});
});

