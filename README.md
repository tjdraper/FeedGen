# Beta

This is the beta development branch. I'll write full documentation soon but for now, here's a sample tag. You would throw this into an EE XML template:

	{exp:feedgen:rss
		url_segments="article/:year/:month/:day/:url_title"
		status="open|other"
		feed_creator_email="info@mysite.com"
		channel="articles"
		content_field="body"
		image_field="hero_image"}