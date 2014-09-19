<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FeedGen Model
 *
 * @package FeedGen
 * @author  TJ Draper
 * @link    http://buzzingpixel.com
 */

class Feedgen_model extends CI_Model {

	public function getFieldId($fieldName)
	{
		$query = ee()->db
			->select('field_id')
			->from('channel_fields')
			->where('field_name', $fieldName)
			->get();

		if ($query->num_rows > 0) {
			return $query->row()->field_id;
		}

		return;
	}

	public function getChannelInfo($channel)
	{
		$query = ee()->db
			->select('
				channel_title,
				channel_url,
				channel_description,
				channel_lang,
				channel_id
			')
			->from('channels')
			->where_in('channel_name', $channel)
			->limit(1)
			->get();

		if ($query->num_rows > 0) {
			$returnData = array();

			foreach ($query->row() as $key => $value) {
				// Strip trailing slashes from URL for consistency
				if ($key == 'channel_url') {
					$value = preg_replace('/\b\//', '', $value);
				}

				$returnData[$key] = $value;
			}

			return $returnData;
		}

		return;
	}

	public function getImageFieldType($imageField)
	{
		$query = ee()->db
			->select('field_type')
			->from('channel_fields')
			->where('field_name', $imageField)
			->get();

		if ($query->num_rows > 0) {
			return $query->row()->field_type;
		}

		return;
	}

	public function getRssFeed($options)
	{
		// var_dump($options); die;

		$status = explode('|', $options['status']);

		ee()->db
			->select('
				CT.title,
				CT.url_title,
				CT.entry_date,
				CT.edit_date,
				CT.year,
				CT.month,
				CT.day,
				M.screen_name
			')
			->from('channel_titles CT')
			->join('channel_data CD', 'CT.entry_id = CD.entry_id')
			->join('members M', 'CT.author_id = M.member_id')
			->where('CT.channel_id', $options['channel_id'])
			->where_in('CT.status', $status)
			->order_by('edit_date', 'desc')
			->limit($options['limit']);

		ee()->db->select(
			'field_id_' . $options['content_field'] . ' AS content'
		);

		if (! empty($options['image_field'])) {
			if ($options['image_field_type'] == 'file') {
				ee()->db->select(
					'field_id_' . $options['image_field'] . ' AS image'
				);
			} else if ($options['image_field_type'] == 'photo_frame') {
				ee()->db
					->select('PF.file AS image')
					->join(
						'photo_frame PF',
						'CT.entry_id = PF.entry_id
							AND PF.order = 0
							AND PF.field_id =' . $options['image_field'],
						'left'
					);
			} else if ($options['image_field_type'] == 'imagee') {
				ee()->db
					->select(
						'CONCAT(
							"{filedir_",
							upload_location_id,
							"}",
							filename,
							trim("."),
							extension
						) AS image', false
					)
					->join(
						'imagee_images I',
						'CT.entry_id = I.entry_id
							AND (I.cover = 1 OR I.position = 1)',
						'left'
					);
			}
		}

		$query = ee()->db->get();

		if ($query->result()) {
			$returnData = $this->_buildResults(
				$query->result(),
				$options['typographyPrefs']
			);
		}

		if (! empty($options['image_field'])) {
			$returnData = $this->_buildImageFileField(
				$returnData,
				$options
			);
		}

		// Encode Special Characters
		foreach ($returnData as $key => $value) {
			foreach ($value as $subKey => $subValue) {
				if ($subKey == 'title' or $subKey == 'content') {
					$returnData[$key][$subKey] = htmlspecialchars($subValue);
				}
			}
		}

		return $returnData;
	}

	private function _buildResults($input, $typographyPrefs)
	{
		$returnData = array();

		foreach ($input as $inputKey => $inputResult) {
			foreach ($inputResult as $inputItemKey => $inputItem) {
				if ($inputItemKey == 'content') {
					$inputItem = ee()->typography->parse_type(
						$inputItem, $typographyPrefs
					);
				}

				$returnData[$inputKey][$inputItemKey] = $inputItem;
			}
		}

		return $returnData;
	}

	private function _buildImageFileField($input, $options)
	{
		foreach ($input as $inputKey => &$inputValue) {
			if (! empty($inputValue['image'])) {
				if (! empty($options['image_manipulation'])) {
					$splitImage = explode('}', $inputValue['image']);

					$inputValue['image'] = $splitImage[0] . '}';

					$inputValue['image'] .= '_'
					. $options['image_manipulation'] . '/';

					$inputValue['image'] .= $splitImage[1];
				}

				$inputValue['image'] = ee()->typography->parse_type(
					$inputValue['image'], $options['typographyPrefs']
				);
			}
		}

		return $input;
	}
}