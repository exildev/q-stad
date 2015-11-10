/**
* 
*/

$(document).ready(function (){
	$("article ul li img").each(function (){
		var t = Math.floor(Math.random()*5)/5 + 0.2;
		$(this).css({
			'-webkit-animation' :  'show ' + t + 's'
		});
	});
	$("a.ajax").click(function (){
		var href = $(this).attr("href");
		var target = $(this).attr("target");
		$("article.main").load(href);
		return false;
	});
});