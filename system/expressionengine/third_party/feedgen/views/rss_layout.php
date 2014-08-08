<rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:admin="http://webns.net/mvcb/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
	<channel>
		<?
			// If link is not absolute, make it absolute
			if (strpos($feed_link, 'http://') === false) {
				$feed_link = ee()->config->item('site_url') . '/' . $feed_link;
			}
		?>
		<title><?=$feed_title?></title>
		<link><?=$feed_link?></link>
		<atom:link href="<?=$feed_link?>" rel="self" type="application/rss+xml" />
		<description><?=$feed_description?></description>
		<dc:language><?=$feed_language?></dc:language>
		<dc:creator><?=$feed_creator_email?></dc:creator>
		<dc:rights>Copyright <?echo date('Y')?></dc:rights>
			<?=ee()->load->view('rss_item', $items, true)?>
	</channel>
</rss>