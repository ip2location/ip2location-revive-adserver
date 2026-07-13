<?php

class IP2LocationGeotargetingAPI_processSettings
{
	public function validate(&$aErrorMessage)
	{
		$key = $GLOBALS['IP2LocationGeotargetingAPI_key'] ?? '';
		$ioKey = $GLOBALS['IP2LocationGeotargetingAPI_iokey'] ?? '';

		$errors = [];

		if (!empty($key)) {
			$json = $this->_fetchJson('https://api.ip2location.com/v2/?key=' . urlencode($key) . '&check=1');
			if ($json === null || !isset($json->response) || $json->response !== 'OK') {
				$msg = $json->response ?? 'no response';
				$errors[] = 'IP2Location Web Service API Key is invalid (' . $msg . ').';
			}
		}

		if (!empty($ioKey)) {
			$json = $this->_fetchJson('https://api.ip2location.io/?key=' . urlencode($ioKey) . '&check=1');
			if ($json === null || isset($json->error)) {
				$msg = $json->error->error_message ?? 'no response';
				$errors[] = 'IP2Location.io API Key is invalid (' . $msg . ').';
			}
		}

		if (!empty($errors)) {
			$aErrorMessage[0] = implode(' ', $errors);
			return false;
		}

		return true;
	}

	private function _fetchJson($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		$response = curl_exec($ch);
		$err = curl_errno($ch);
		curl_close($ch);

		if ($err || $response === false) {
			return null;
		}

		$json = json_decode($response);
		return (json_last_error() === JSON_ERROR_NONE) ? $json : null;
	}
}
