<?php
	require_once ('../../views/ViewModel.php');

	class ReaderView extends View {
		public function render ($data) {
			$output = '';

			if (count( $data ) > 0) {
				for ($i = 0; $i < count ($data); $i++) {// (($row = mysql_fetch_array( $data )) != null) {
					$ITEM_ID = $data[$i]['ITEM_ID'];
					$viewed = $data[$i]['viewed'];
					$favorite = $data[$i]['favorite'];
					$title = $data[$i]['title'];
					$newClass = $data[$i]['viewed'] == 0 ? 'new' : '';
					$favoritedClass = $data[$i]['favorite'] == 1 ? 'active' : '';
					$favoriteIcon = IconView::render( new IconModel ('star', 'Fav'));

					$output .= <<<EOD
	<article class="$newClass" data-itemid="$ITEM_ID">
		<h2>$title</h2>
		<a href="index.php?action=favoriteToggle&amp;item_id=$ITEM_ID" class="favorite $favoritedClass">$favoriteIcon</a>
		<div style="clear: both;"></div>
		<div class="description">
			<p><a href="./?action=readFullArticle&amp;item_id=$ITEM_ID" target="_blank" title="$title">Read more</a></p>
		</div>
	</article>
EOD;
				}
			} else {
				$output = <<<EOD
	<div class="no-articles">
		There are no new news articles found.  Please wait a bit or try selecting a different category.
	</div>
EOD;
			}

			return $output;
		}
	}