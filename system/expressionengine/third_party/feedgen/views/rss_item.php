<?
	// If link is not absolute, make it absolute
	if (strpos($items['url_segments'], 'http://') === false) {
		$link = ee()->config->item('site_url') . '/' . $items['url_segments'];
	} else {
		$link = $items['url_segments'];
	}

	foreach ($items['content'] as $item) {
		// Parse url segments
		$link = str_replace(':url_title', $item['url_title'], $link);

		$link = str_replace(':year', $item['year'], $link);

		$link = str_replace(':month', $item['month'], $link);

		$link = str_replace(':day', $item['day'], $link);

		// If image url is not absolute, make it absolute
		if (! empty($item['image']) and strpos($item['image'], 'http://') === false) {
			$image = ee()->config->item('site_url') . $item['image'];
		} else if (! empty($item['image'])) {
			$image = $item['image'];
		}

		$dcDate = ee()->localize->format_date(
			'%Y-%m-%dT%H:%i:%s%O',
			$item['entry_date']
		);

		?>
			<item>
				<title><?=$item['title']?></title>
				<link><?=$link?></link>
				<guid isPermaLink="false"><?=$link?>#<?=$item['edit_date']?></guid>
				<description><![CDATA[
					<?
						if (! empty($image)) {
							?>
								<img src="<?=$image?>">
								<br><br>
							<?
						}
					?>
					<?=$item['content']?>
				]]></description>
				<dc:date><?=$dcDate?></dc:date>
				<dc:creator><?=$item['screen_name']?></dc:creator>
			</item>
		<?
	}
?>