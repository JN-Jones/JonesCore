<?php

class JB_Alerts_BaseFormatter extends MybbStuff_MyAlerts_Formatter_AbstractFormatter
{
	public function formatAlert(MybbStuff_MyAlerts_Entity_Alert $alert, array $outputAlert)
	{
		$code = $alert->getType()->getCode();
		$jb = substr($code, 0, 3);
		if(strtolower($jb) != "jb_")
			return $code;

		$l = substr($code, 3);

		if(!isset($this->lang->$l))
			return $code;

		$extra = $alert->getExtraDetails();
		if(!empty($extra['lang_data']))
		{
			return $this->lang->sprintf(
				$this->lang->$l,
				$outputAlert['from_user_profilelink'],
				$outputAlert['dateline'],
				$extra['lang_data']
			);
		}
		else
		{
			return $this->lang->sprintf(
				$this->lang->$l,
				$outputAlert['from_user_profilelink'],
				$outputAlert['dateline']
			);
		}
    }
}