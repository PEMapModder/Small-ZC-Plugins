<?php

namespace pemapmodder\spicycap\database;

use pemapmodder\spicycap\Report;
use pemapmodder\spicycap\SpicyCap;

class MySQLDatabase implements Database{
	private $main;
	private $db;
	public function __construct(\mysqli $mysqli, SpicyCap $main){
		$this->main = $main;
		$this->db = $mysqli;
	}
	/**
	 * Initialize the database.
	 */
	public function init(){
		$this->db->query("CREATE TABLE IF NOT EXISTS ids (
				name VARCHAR(15),
				value INT DEFAULT 0
				);");
		$this->db->query("INSERT INTO ids (name, value)
				SELECT * FROM (SELECT 'RID', 0) AS tmp
				WHERE NOT EXISTS (SELECT value FROM ids WHERE name = 'RID')
				LIMIT 1;");
		$this->db->query("INSERT INTO ids (name, value)
				SELECT * FROM (SELECT 'BID', 0) AS tmp
				WHERE NOT EXISTS (SELECT value FROM ids WHERE name = 'BID')
				LIMIT 1;");
		$this->db->query("CREATE TABLE IF NOT EXISTS reports (
				id INT PRIMARY KEY,
				byname VARCHAR(31),
				byip VARCHAR(15),
				targetname VARCHAR(31),
				targetip VARCHAR(15),
				flags SMALLINT,
				description VARCHAR(65535),
				logs VARBINARY(65535),
				assignee VARCHAR(31) DEFAULT NULL
				);");
		$this->db->query("CREATE TABLE IF NOT EXISTS points (
				id INT PRIMARY KEY,
				byname VARCHAR(31),
				refreport INT,
				targetip VARCHAR(15),
				creation INT,
				expiry INT,
				details VARCHAR(65535)
				);");
	}
	/**
	 * Get the next unique report ID.
	 *
	 * @return int the next unique report ID
	 */
	public function nextRID(){
		$result = $this->db->query("SELECT value FROM ids WHERE name = 'RID';");
		$id = $result->fetch_assoc()["value"];
		$result->close();
		$this->db->query("UPDATE ids SET value = (value + 1) WHERE name = 'RID';");
		return $id;
	}
	/**
	 * Converts an array from mysqli_result::fetch_assoc() of a result
	 * fetched from the reports table of the database.
	 *
	 * @param array $data Associative array containing data fetched from the reports table of the database
	 * @return Report
	 */
	private function formatReport(array $data){
		return new Report($this->main, $data["id"], $data["byname"], $data["byip"], $data["targetname"],
			$data["targetip"], $data["flags"], $data["description"], $data["assignee"], $data["logs"]);
	}
	/**
	 * Get the report of highest priority in the report queue.
	 * This function does NOT shift the report from the report queue.
	 *
	 * @return Report the report of highest priority in the queue
	 */
	public function glanceNextReport(){
		$resolvedFlag = Report::FLAG_RESOLVED;
		$result = $this->db->query("SELECT * FROM reports WHERE
				assignee = null AND (flags & $resolvedFlag) = 0
				LIMIT 1;");
		$report = $result->fetch_assoc();
		$result->close();
		if(!is_array($report)){
			return null;
		}
		return $this->formatReport($report);
	}
	/**
	 * Add a report into the report queue.
	 * @param Report $report
	 */
	public function queueReport(Report $report){
		$this->db->query("INSERT INTO reports
				(id, byname, byip, targetname, targetip, flags, description, assignee, logs) VALUES (
				{$report->getID()},
				{$this->esc($report->getFromName())},
				{$this->esc($report->getFromIP())},
				{$this->esc($report->getToName())},
				{$this->esc($report->getToIP())},
				{$report->getFlags()},
				{$this->esc($report->getDescription())},
				{$this->esc($report->getAssignee())},
				{$this->esc($report->compressLogs())}
				);");
	}
	/**
	 * Assign a report to $player and shift it from the queue of pending reports.
	 * @param string $player name of the player to assign the report to
	 * @return Report
	 */
	public function assignAndShiftReport($player){
		$resolvedFlag = Report::FLAG_RESOLVED;
		$this->db->multi_query("SELECT * FROM reports WHERE
				assignee = null AND (flags & $resolvedFlag) = 0
				LIMIT 1;
				UPDATE reports SET assignee = {$this->db->escape_string($player)}
				WHERE assignee = null AND (flags & $resolvedFlag) = 0 LIMIT 1;");
		if($this->db->next_result()){
			$result = $this->db->store_result();
			$array = $result->fetch_assoc();
			$result->close();
			return $this->formatReport($array);
		}
		return null;
	}
	/**
	 * Unassign a report from $player and add it back to the queue of pending reports.
	 *
	 * @param string $player name of the player to unassign report from
	 */
	public function unassignAndQueueReport($player){
		$rf = Report::FLAG_RESOLVED;
		$this->db->query("UPDATE reports SET assignee = NULL WHERE
				assignee = {$this->esc($player)} AND
				(flags & $rf) = 0;");
	}
	/**
	 * Make a report as resolved and, if possible, unassign the assignee of the report.
	 *
	 * @param int $rid the ID of the report to mark as resolved
	 */
	public function resolveReport($rid){
		$rf = Report::FLAG_RESOLVED;
		$this->db->query("UPDATE reports SET flags = (flags | $rf) WHERE id = $rid;");
	}
	/**
	 * Get the report assigned to the player.
	 *
	 * @param string $player the assignee to search with
	 * @return Report|null the report assigned to $player, or <code>null</code> if none
	 */
	public function getAssignedReport($player){
		$result = $this->db->query("SELECT * FROM reports WHERE assignee = {$this->esc($player)};");
		$array = $result->fetch_assoc();
		$result->close();
		if(is_array($array)){
			return $this->formatReport($array);
		}
		return null;
	}
	/**
	 * Get the next unique ban point issue ID (BID).
	 *
	 * @return int
	 */
	public function nextBID(){
		// TODO: Implement nextBID() method.
	}
	/**
	 * Issue ban points to an IP.
	 * @param string $ip IP to issue penalt
	 * @param int $points points to issue
	 * @param string $details details of the issue
	 * @param int $creation time of issue of the points
	 * @param int $expiry time of expiry of the points
	 * @return int BID (Ban point issue ID)
	 */
	public function issuePoints($ip, $points, $details, $creation, $expiry){
		// TODO: Implement issuePoints() method.
	}
	/**
	 * Get information about the ban point issue of the specified ID.
	 *
	 * @param int $bid the ID of the ban point issue
	 * @return mixed (TODO)
	 */
	public function getBanInfo($bid){
		// TODO: Implement getBanInfo() method.
	}
	/**
	 * Close the database.
	 */
	public function close(){
		// TODO: Implement close() method.
	}
	/**
	 * Counts the total ban points issued to an IP
	 *
	 * @param string $ip the IP to check
	 * @return int
	 */
	public function getBanPointsSum($ip){
		// TODO: Implement getBanPointsSum() method.
	}
	private function esc($str){
		if($str === null){
			return "NULL";
		}
		return "'{$this->db->escape_string($str)}'";
	}
}
