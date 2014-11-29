<?php

class JB_Alerts_BaseFormatter extends MybbStuff_MyAlerts_Formatter_AbstractFormatter
{
	public function formatAlert(MybbStuff_MyAlerts_Entity_Alert $alert, array $outputAlert)
	{
		$code = $alert->getType()->getCode();
		$jb = substr($code, 0, 3);
		if(strtolower($jb) != "jb_")
			return $code;

		$l = "myalerts_format_".substr($code, 3);

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

	public function buildShowLink(MybbStuff_MyAlerts_Entity_Alert $alert)
	{
		$extra = $alert->getExtraDetails();
		return $this->mybb->settings['bburl']."/".$extra['link'];
	}
}