<?php

require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';

function IP2Location_limitationsIsOperatorPositive($op)
{
	return $op == '==' || $op == 'ge' || $op == 'le' || $op == 'gt' || $op == 'lt';
}

function IP2Location_limitationMatchNumeric($paramName, $limitation, $op, $aParams = [], $namespace = 'CLIENT_GEO')
{
	if ($limitation == '') {
		return !IP2Location_limitationsIsOperatorPositive($op);
	}
	if (empty($aParams)) {
		$aParams = $GLOBALS['_MAX'][$namespace];
	}

	if (!isset($aParams[$paramName]) || !is_numeric($aParams[$paramName]) || !is_numeric($limitation)) {
		return !($op);
	}
	$value = $aParams[$paramName];

	switch ($op) {
		case '==': return $value == $limitation;
		case '!=': return $value != $limitation;
		case 'lt': return $value < $limitation;
		case 'gt': return $value > $limitation;
		case 'le': return $value <= $limitation;
		case 'ge': return $value >= $limitation;
	}

	return !MAX_limitationsIsOperatorPositive($op);
}
