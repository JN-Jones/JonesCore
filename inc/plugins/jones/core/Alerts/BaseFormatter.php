<?php

class JB_Alerts_BaseFormatter extends MybbStuff_MyAlerts_Formatter_AbstractFormatter
{
	/**
	 * @param MybbStuff_MyAlerts_Entity_Alert $alert
	 * @param array                           $outputAlert
	 *
	 * @return string
	 */
	public function formatAlert(MybbStuff_MyAlerts_Entity_Alert $alert, array $outputAlert)
	{
		$code = $alert->getType()->getCode();
		$prefix = substr($code, 0, strpos($code, "_"));
		if(JB_Packages::i()->getVendorForPrefix($prefix)  === false)
			return $code;

		$l = "myalerts_format_".substr($code, strlen($prefix)+1);

		if(!isset($this->lang->$l))
			return $code;

		$extra = $alert->getExtraDetails();
		$link = $this->buildShowLink($alert);
		if(!empty($extra['lang_data']))
		{
			return $this->lang->sprintf(
				$this->lang->$l,
				$outputAlert['from_user_profilelink'],
				$outputAlert['dateline'],
				$link,
				e($extra['lang_data'])
			);
		}
		else
		{
			return $this->lang->sprintf(
				$this->lang->$l,
				$outputAlert['from_user_profilelink'],
				$outputAlert['dateline'],
				$link
			);
		}
	}

	public function init() {}

	/**
	 * @param MybbStuff_MyAlerts_Entity_Alert $alert
	 *
	 * @return string
	 */
	public function buildShowLink(MybbStuff_MyAlerts_Entity_Alert $alert)
	{
		$extra = $alert->getExtraDetails();
		return $this->mybb->settings['bburl']."/".$extra['link'];
	}
}
