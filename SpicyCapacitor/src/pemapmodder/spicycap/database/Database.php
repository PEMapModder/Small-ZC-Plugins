<?php

namespace pemapmodder\spicycap\database;

use pemapmodder\spicycap\Report;

interface Database{
	public function init();
	public function nextRID();
	public function glanceNextReport();
	public function queueReport(Report $report);
	public function assignAndShiftReport($name);
	public function unassignAndQueueReport($name);
	public function resolveReport($rid);
	public function nextBID();
	/**
	 * Issues ban points to an IP
	 * @param string $ip IP to issue penalt
	 * @param int $points points to issue
	 * @param string $details details of the issue
	 * @param int $creation time of issue of the points
	 * @param int $expiry time of expiry of the points
	 * @return int BID (Ban point issue ID)
	 */
	public function issuePoints($ip, $points, $details, $creation, $expiry);
	public function getBanInfo($bid);
	public function close();
}
