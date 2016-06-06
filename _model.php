<?php
	/*
	 * Table of contents
	 * -> Article management
	 * -> Feed management
	 */
	class ReaderManager {
		public function get_link () {
			global $DB_ADDRESS;
			global $DB_USER;
			global $DB_PASS;
			global $DB_NAME;

			return $link = DB_Connect($DB_ADDRESS, $DB_USER, $DB_PASS, $DB_NAME);
		}
		/*
		 * Article management
		 */
		public function getAllArticles ($showFavorites, $label, $columns='*') {
			$link = ReaderManager::get_link();
			$USER_ID = $_SESSION['USER_ID'];

			$labelClause = ((isset ($label) && $label != 'All') ? " AND `label` = '$label'" : '');
			
			$sql = $showFavorites ? 	// if showFavorites select all favorited articles
<<<EOD
	SELECT
		$columns
	FROM
		`reader_cache`
	WHERE
		`USER_ID`='$USER_ID'
			AND
		`favorite`=1
	ORDER BY
		`posted` ASC
EOD
: 										// else select all unviewed articles with given label (if any)
<<<EOD
	SELECT
		$columns
	FROM
		`reader_cache`
	WHERE
		`viewed`='0'
			AND
		`USER_ID`='$USER_ID'
	$labelClause
	ORDER BY
		`posted`
	ASC
EOD;
			
			$result = $link->query($sql);

			$articles = array ();

			if ($result = $link->query($sql)) {
				while ( $row = $result->fetch_object() ) {
					$articles[] = array (
						"ITEM_ID" => $row->ITEM_ID,
						"viewed" => $row->viewed,
						"favorite" => $row->favorite,
						"title" => $row->title,
					);
				}
			}

			return $articles;
		}
		public function getAllArticlesMaxId ($showFavorites, $label) {
			$link = ReaderManager::get_link();
			$USER_ID = $_SESSION['USER_ID'];

			$labelClause = ((isset ($label) && $label != 'All') ? " AND `label` = '$label'" : '');
			$sql = $showFavorites ? 	// if showFavorites select all favorited articles
<<<EOD
	SELECT
		MAX(ITEM_ID)
	FROM
		`reader_cache`
	WHERE
		`USER_ID`='$USER_ID'
			AND
		`favorite`=1
	ORDER BY
		`posted` ASC
EOD
: 										// else select all unviewed articles with given label (if any)
<<<EOD
	SELECT
		MAX(ITEM_ID)
	FROM
		`reader_cache`
	WHERE
		`viewed`='0'
			AND
		`USER_ID`='$USER_ID'
	$labelClause
	ORDER BY
		`posted`
	ASC
EOD;

			$result = $link->query($sql);

			$maxItemId = array ();

			$result = $link->query($sql);
			$row = mysqli_fetch_array($result);

			return $row['MAX(ITEM_ID)'];
		}

		public function getArticlesBySearch ($query, $label=null) {
			$USER_ID = $_SESSION['USER_ID'];

			$favoriteClause = ' AND `favorite` = \'1\'';
			$labelClause = " AND `label` = '$label' ";

			// if label is set use label, if label is set to favorites use favorite clause
			$additionalClause = $label != null ? ( $label == 'Favorites' ? $favoriteClause : $labelClause) : '';

			$sql = <<<EOD
	SELECT
		*
	FROM
		`reader_cache`
	WHERE
		(
			`title` LIKE '%$query%'
				OR
			`description` LIKE '%$query%'
		)
		$additionalClause
			AND
		`USER_ID`='$USER_ID'
	ORDER BY
		`posted` DESC
EOD;
			
			$data = mysql_query( $sql ) or die(mysql_error());

			return $data;
		}

		public function getLabels () {
			$link = ReaderManager::get_link();
			$USER_ID = $_SESSION['USER_ID'];

			$sql = <<<EOD
	SELECT DISTINCT
		`label`
	FROM
		`reader_feeds`
	WHERE
		`USER_ID`='$USER_ID'
EOD;

			$labels = array ();

			if ($result = $link->query($sql)) {
				while ( $row = $result->fetch_object() ) {
					$labels[] = $row->label;
				}
			}

			return $labels;
		}

		public function getCount ($label) {
			$link = ReaderManager::get_link();
			$USER_ID = $_SESSION['USER_ID'];

			$sql = <<<EOD
	SELECT
		COUNT(*)
	FROM
		`reader_cache`
	WHERE
		`USER_ID`='$USER_ID'
			AND
		`label`='$label'
			AND
		`viewed`='0';
EOD;
			$result = $link->query ($sql);

			$row = mysqli_fetch_array ($result);
			$count = $row['COUNT(*)'];

			return $count;
		}

		public function favoriteToggle ($ITEM_ID) {
			$link = ReaderManager::get_link();
			$USER_ID = $_SESSION['USER_ID'];

			$sql = <<<EOD
	UPDATE
		`sarah`.`reader_cache`
	SET
		`favorite`= IF(`favorite` = 1, 0, 1)
	WHERE
		`ITEM_ID`='$ITEM_ID'
EOD;
			$result = $link->query($sql);

			return $result;
		}

		public function markAllRead ($label, $maxItemId) {
			$link = ReaderManager::get_link();
			$USER_ID = $_SESSION['USER_ID'];
			$labelClause = isset ($label) && $label != 'All' ? " AND `reader_cache`.`label` = '$label'" : '';

			$sql = <<<EOD
	UPDATE
		`sarah`.`reader_cache`
	SET
		`viewed` = '1'
	WHERE
		`ITEM_ID` <= $maxItemId
			AND
		`USER_ID` = '$USER_ID'
	$labelClause
EOD;

			$data = $link->query($sql);

			return $data;
		}

		public function getArticle ($ITEM_ID) {
			$link = ReaderManager::get_link();
			$USER_ID = $_SESSION['USER_ID'];

			$sql = <<<EOD
	SELECT
		*
	FROM
		`reader_cache`
	WHERE
		`ITEM_ID`='$ITEM_ID'
			AND
		`USER_ID` = '$USER_ID'
EOD;

			$result = $link->query( $sql );
			$row = $result->fetch_array();

			// $db_write_success = mysql_query("UPDATE `sarah`.`reader_cache` SET `viewed` = '1' WHERE `reader_cache`.`ITEM_ID` = " . $_REQUEST['item_id'] . ";") or die(mysql_error());
			return $row;
		}

		public function setViewed ($ITEM_ID) {
			$link = ReaderManager::get_link();
			$USER_ID = $_SESSION['USER_ID'];

			$sql = <<<EOD
	UPDATE
		`sarah`.`reader_cache`
	SET
		`viewed` = '1'
	WHERE
		`reader_cache`.`ITEM_ID` = $ITEM_ID
EOD;

			$db_write_success = $link->query( $sql );

			return $db_write_success;
		}


		/*
		 * Feed management
		 */
		public function getFeeds () {
			$USER_ID = $_SESSION['USER_ID'];

			$sql = <<<EOD
	SELECT
		*
	FROM
		`sarah`.`reader_feeds`
	WHERE
		`reader_feeds`.`USER_ID` = $USER_ID
EOD;

			$data = mysql_query( $sql ) or die(mysql_error());

			return $data;
		}

		public function getFeed ($FEED_ID) {
			$USER_ID = $_SESSION['USER_ID'];

			$sql = <<<EOD
	SELECT
		*
	FROM
		`reader_feeds`
	WHERE
		`FEED_ID`='$FEED_ID'
			AND
		`USER_ID`='$USER_ID'
EOD;

			$data = mysql_query($sql) or die(mysql_error());

			// return the first row in the resultset it *SHOULD* be the only record
			$row = mysql_fetch_array ( $data );
			return $row;
		}

		public function addFeed ($name, $label, $rss) {
			$USER_ID = $_SESSION['USER_ID'];

			$sql = <<<EOD
	INSERT INTO
		`sarah`.`reader_feeds` (
			`USER_ID`,
			`name`,
			`label`,
			`rss`
		) VALUES (
			'$USER_ID',
			'$name',
			'$label',
			'$rss'
		);
EOD;
			return mysql_query( $sql ) or die(mysql_error());
		}

		public function deleteFeed ($FEED_ID) {
			$USER_ID = $_SESSION['USER_ID'];

			$sql = <<<EOD
	DELETE FROM
		`sarah`.`reader_feeds`
	WHERE
		`FEED_ID`='$FEED_ID'
			AND
		`USER_ID`='$USER_ID'
EOD;
			return mysql_query( $sql ) or die(mysql_error());
		}

		public function updateFeed ($FEED_ID, $name, $label, $rss) {
			$USER_ID = $_SESSION['USER_ID'];

			$sql = <<<EOD
	UPDATE
		`sarah`.`reader_feeds`
	SET
		`name` = '$name',
		`label` = '$label',
		`rss` = '$rss'
	WHERE
		`FEED_ID`='$FEED_ID'
			AND
		`USER_ID`='$USER_ID'
EOD;
			
			return mysql_query($sql) or die(mysql_error());
		}		
	}