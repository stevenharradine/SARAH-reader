require (['../../js/jquery-1.6.2.min'], function ($) {
	require({
		baseUrl: '../../js/'
	}, [
		"navigation",
		"add",
		"edit"
	], function( 
		nav,
		add,
		edit
	) {
		jQuery ("article").addClass("closed");

		jQuery ("article h2").bind ("click", function () {
			var $parent = jQuery (this).parent();
			var id = $parent.toggleClass("closed").attr("data-itemid");

			if (!$parent.hasClass('cached')) {
				jQuery.get('get-item.php?item_id=' + id, function(data) {
					jQuery('.result').html(data);

					$parent.removeClass("new")
						.addClass("cached")
						.children(".description")
						.prepend(data);
				});
			}
		});
		
		jQuery ("#choose-label").bind ("change", function (e) {
			selected_label = jQuery(this).val();

			location.href = selected_label == "favorite" ? "index.php?showFavorites=1" : "index.php?label=" + selected_label;
		});
		
		jQuery ("article a.favorite").bind ("click", function (e) {
			e.preventDefault();
			var $thisButton = jQuery(this);
			
			jQuery.get($thisButton.attr("href"), function(data) {
				$thisButton.toggleClass("active");
			});
		});
	});
});