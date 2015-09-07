<?php

namespace view;

class DateTimeView {


	public function show() {

		date_default_timezone_set('Europe/Stockholm');

		$timeString = '<p>'.date("l,"). ' the ' .date("jS"). ' of ' .date("F Y,"). ' The time is' .date(" g:i:s"). '</p>';

		return $timeString;
	}
}