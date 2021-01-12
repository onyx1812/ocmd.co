<?php

namespace CodeClouds\Unify\Service;

/**
 * Server request handler.
 * @package CodeClouds\Unify
 */
class Helper
{

	/**
	 * To return the TimeZone in a string format. 
	 * Example 
	 * EST or America/New_York
	 * IST or Asia/Kolkata
	 * 
	 * @param bool $abbreviation If TimeZone 3/4 char abbreviation is needed then set it to TRUE other it will return full form
	 * @return string
	 */
	public Static function wh_get_timezone_string($abbreviation = TRUE)
	{
		$format = $abbreviation ? 'T' : 'e';
		$timezone = get_option('timezone_string');

		//If site timezone string exists
		if (!empty($timezone))
		{
			$dateTime = new \DateTime();
			$dateTime->setTimeZone(new \DateTimeZone($timezone));
			return $dateTime->format($format);
		}

		//Getting UTC offset, if it isn't set then return UTC
		if (0 === ( $utc_offset = get_option('gmt_offset', 0) ))
			return 'UTC';

		//Adjusting UTC offset from hours to seconds
		$utc_offset *= 3600;
		$timezone = timezone_name_from_abbr('', $utc_offset, 0);
		// attempt to guess the timezone string from the UTC offset
		if (!empty($timezone))
		{
			$dateTime = new \DateTime();
			$dateTime->setTimeZone(new \DateTimeZone($timezone));
			return $dateTime->format($format);
		}

		//Guessing timezone string manually
		$is_dst = date('I');
		foreach (timezone_abbreviations_list() as $abbr)
		{
			foreach ($abbr as $city)
			{
				if ($city['dst'] == $is_dst && $city['offset'] == $utc_offset)
					return $abbreviation ? strtoupper($abbr) : $city['timezone_id'];
			}
		}

		//Default to UTC
		return 'UTC';
	}
	
	
	public static function getDataFromFile($file)
	{
		switch ($file)
		{
			case 'Messages':
				$data = include_once __DIR__ . '/Messages.php';
				break;
			case 'request-unfiy-pro':
				$data = __DIR__ . '/../Templates/Mail/request-unfiy-pro.php';
				break;
			case 'request-unfiy-pro-user':
				$data = __DIR__ . '/../Templates/Mail/request-unfiy-pro-user.php';
				break;
			default :
				$data = [];
				break;
		}
		
		return $data;
	}
	
	public static function getPaginationTemplate($prev_dis, $next_dis, $paged, $total)
	{
		echo include_once __DIR__ . '/../Templates/Pagination/pagination-template.php';
	}
	
	public static function getCrmType()
	{
		$crm = null;
		$crm_meta = 0;
		
		$setting_option = \get_option('woocommerce_codeclouds_unify_settings');
		
		if (!empty($setting_option))
		{
			$crm = \get_post_meta($setting_option['connection'], 'unify_connection_crm');

			if (!empty($crm))
			{
				$crm = $crm[0];

				if ($crm == 'limelight')
				{
					$crm_meta = \get_post_meta($setting_option['connection'], 'unify_connection_offer_model');
					$crm_meta = (!empty($crm_meta)) ? $crm_meta[0] : 0;
				}
			}
		}
		
		return ['crm' => $crm, 'crm_meta' => $crm_meta];
	}
}
