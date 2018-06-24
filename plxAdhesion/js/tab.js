jQuery(function($) {

	//$('textarea.expanding').autogrow({minHeight : 100});
	
	//Décommenter les 2 lignes en dessous pour utiliser le système d'onglets de jQuery Tools
	//$("div.panes > div.contenu").css({'display' : 'none'});
	//$("ul.css-tabs").tabs("div.panes > div.contenu", {history: true});
	
	//Gestion des onglets d'après http://www.grafikart.com
	var anchor = window.location.hash;
	$('.css-tabs').each(function(){
		var current = null;
		var id = $(this).attr('id');
		//Si l'on veut conserver l'historique d'ouverture des onglets
		//il faut ajouter la classe history au menu
		if ($(this).hasClass('history') === true) {
			var history = true;
		}
		if (anchor != '' && $(this).find('a[href="'+anchor+'"]').length > 0 ) {
			current = anchor;
		} else if($.cookie('tab'+id) && $(this).find('a[href="'+$.cookie('tab'+id)+'"]').length > 0 ){
			current = $.cookie('tab'+id);
		} else {
			current = $(this).find('a:first').attr('href');
		}
		$(this).find('a[href="'+current+'"]').addClass('current');
		$(current).siblings().hide();
		$(this).find('a').click(function(){
			var link = $(this).attr('href');
			if (link != current) {
				$('a.current').removeClass('current');
				$(this).addClass('current');
				$(link).show().siblings().hide();
				current = link;
				if (history) {
					$.cookie('tab'+id,current);
				}
			}
			if ($(this).attr('href').match(new RegExp('\#')) !== null){
				return false;
			}
		});
	});
});