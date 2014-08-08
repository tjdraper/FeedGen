<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FeedGen Plugin
 *
 * @package FeedGen
 * @author  TJ Draper
 * @link    http://buzzingpixel.com
 */

include(PATH_THIRD . 'feedgen/config.php');

$plugin_info = array (
	'pi_name' => FEEDGEN_NAME,
	'pi_version' => FEEDGEN_VER,
	'pi_author' => FEEDGEN_AUTHOR,
	'pi_author_url' => FEEDGEN_AUTHOR_URL,
	'pi_description' => FEEDGEN_DESC,
	'pi_usage' => Feedgen::usage()
);

class Feedgen {

	// Set typography prefs for parsing file fields
	public $typographyPrefs = array(
	    'text_format' => 'none',
	    'html_format' => 'all',
	    'auto_links' => 'n',
	    'allow_img_url' => 'y'
	);

	public function __construct()
	{
		// Load EE typography model for parsing images
		ee()->load->library('typography');
		ee()->typography->initialize();

		// Load the model
		ee()->load->model('feedgen_model');

		// Fetch common params

		$this->channel = ee()->TMPL->fetch_param('channel');

		$this->channel_id = ee()->feedgen_model->getChannelInfo(
			$this->channel
		)['channel_id'];

		$this->status = ee()->TMPL->fetch_param('status', 'open');

		$this->limit = ee()->TMPL->fetch_param('limit', '50');

		$this->infoChannel = ee()->TMPL->fetch_param(
			'info_channel',
			$this->channel
		);

		// Fetch channel information
		$this->channelInfo = ee()->feedgen_model->getChannelInfo(
			$this->infoChannel
		);

		$this->feedTitle = ee()->TMPL->fetch_param(
			'feed_title',
			ee()->config->item('site_name')
		);

		$this->feedLink = ee()->TMPL->fetch_param(
			'feed_link',
			$this->channelInfo['channel_url']
		);

		$this->urlSegments = ee()->TMPL->fetch_param(
			'url_segments',
			$this->feedLink . '/:url_title'
		);

		$this->feedDescription = ee()->TMPL->fetch_param(
			'feed_description',
			$this->channelInfo['channel_description']
		);

		$this->feedLanguage = ee()->TMPL->fetch_param(
			'feed_language',
			$this->channelInfo['channel_lang']
		);

		$this->feedCreatorEmail = ee()->TMPL->fetch_param(
			'feed_creator_email',
			ee()->config->item('webmaster_email')
		);

		// Site URL - Strip trailing slashes from URL for consistency
		$this->siteUrl = preg_replace(
			'/\b\//',
			'',
			ee()->config->item('site_url')
		);

		// If tag has no third segment, return an error
		$this->return_data = '<error>Your tag is missing a segment.</error>';
	}

	public function rss()
	{
		// Specific params

		$contentField = ee()->TMPL->fetch_param('content_field');

		// Image Field

		$imageField = ee()->TMPL->fetch_param('image_field');

		if ($imageField != false) {
			$imageFieldType = ee()->feedgen_model->getImageFieldType(
				$imageField
			);

			$imageField = ee()->feedgen_model->getFieldId($imageField);
			$imageManipulation = ee()->TMPL->fetch_param(
				'image_manipulation'
			);
		}

		// Make sure required parameters are present
		if ($this->infoChannel == false) {
			return '<error>Incorrect channel parameter</error>';
		} else if ($contentField == false) {
			return '<error>Incorrect content_field parameter</error>';
		}

		// Get content field ID
		$contentField = ee()->feedgen_model->getFieldId($contentField);

		// Options array for Model

		$modelOptions = array(
			'typographyPrefs' => $this->typographyPrefs,
			'channel_id' => $this->channel_id,
			'status' => $this->status,
			'limit' => $this->limit,
			'content_field' => $contentField
		);

		if (! empty($imageField)) {
			$modelOptions['image_field'] = $imageField;
		}

		if (! empty($imageFieldType)) {
			$modelOptions['image_field_type'] = $imageFieldType;
		}

		if (! empty($imageManipulation)) {
			if ($imageFieldType == 'file' or $imageFieldType == 'imagee') {
				$modelOptions['image_manipulation'] = $imageManipulation;
			}
		}

		// Get Feed Items
		$feedItems = ee()->feedgen_model->getRssFeed($modelOptions);

		$feedData = array(
			'feed_title' => $this->feedTitle,
			'feed_link' => $this->feedLink,
			'feed_description' => $this->feedDescription,
			'feed_language' => $this->feedLanguage,
			'feed_creator_email' => $this->feedCreatorEmail,
			'items' => array(
				'url_segments' => $this->urlSegments,
				'content' => $feedItems
			)
		);

		// Load content into view
		$returnData = ee()->load->view('rss_layout', $feedData, true);

		// Return the view
		return $returnData;
	}

	function usage()
	{
		ob_start();
?>
to-do
<?php
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}
}