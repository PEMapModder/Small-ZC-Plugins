<?php

namespace pemapmodder\spicycap\database;

use pemapmodder\spicycap\Report;

interface Database{
	/**
	 * Initialize the database.
	 */
	public function init();
	/**
	 * Get the next unique report ID.
	 *
	 * @return int the next unique report ID
	 */
	public function nextRID();
	/**
	 * Get the report of highest priority in the report queue.
	 * This function does NOT shift the report from the report queue.
	 *
	 * @return Report the report of highest priority in the queue
	 */
	public function glanceNextReport();
	/**
	 * Add a report into the report queue.
	 * @param Report $report
	 */
	public function queueReport(Report $report);
	/**
	 * Assign a report to $player and shift it from the queue of pending reports.
	 * @param string $player name of the player to assign the report to
	 * @return Report
	 */
	public function assignAndShiftReport($player);
	/**
	 * Unassign a report from $player and add it back to the queue of pending reports.
	 *
	 * @param string $player name of the player to unassign report from
	 */
	public function unassignAndQueueReport($player);
	/**
	 * Make a report as resolved and, if possible, unassign the assignee of the report.
	 *
	 * @param int $rid the ID of the report to mark as resolved
	 */
	public function resolveReport($rid);
	/**
	 * Get the report assigned to the player.
	 *
	 * @param string $player the assignee to search with
	 * @return Report|null the report assigned to $player, or <code>null</code> if none
	 */
	public function getAssignedReport($player);
	/**
	 * Get the next unique ban point issue ID (BID).
	 *
	 * @return int
	 */
	public function nextBID();
	/**
	 * Issue ban points to an IP.
	 * @param string $ip IP to issue penalt
	 * @param int $points points to issue
	 * @param string $details details of the issue
	 * @param int $creation time of issue of the points
	 * @param int $expiry time of expiry of the points
	 * @return int BID (Ban point issue ID)
	 */
	public function issuePoints($ip, $points, $details, $creation, $expiry);
	/**
	 * Get information about the ban point issue of the specified ID.
	 *
	 * @param int $bid the ID of the ban point issue
	 * @return mixed (TODO)
	 */
	public function getBanInfo($bid);
	/**
	 * Counts the total ban points issued to an IP
	 *
	 * @param string $ip the IP to check
	 * @return int
	 */
	public function getBanPointsSum($ip);
	/**
	 * Close the database.
	 */
	public function close();
}
