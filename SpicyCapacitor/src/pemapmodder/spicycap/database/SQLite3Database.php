<?php

namespace pemapmodder\spicycap\database;

use pemapmodder\spicycap\Report;
use pemapmodder\spicycap\SpicyCap;

class SQLite3Database implements Database{
	/** @var SpicyCap */
	private $main;
	/** @var string */
	private $path;
	/** @var \SQLite3 */
	private $db;
	public function __construct($path, SpicyCap $main){
		$this->path = $path;
		$this->main = $main;
	}
	/**
	 * Initialize the database.
	 */
	public function init(){
		$this->db = new \SQLite3($this->path);
		$this->db->exec("CREATE TABLE IF NOT EXISTS ids (name TEXT PRIMARY KEY, value INTEGER);");
		$this->db->exec("INSERT OR IGNORE INTO ids (name, value) VALUES ('RID', 0);");
		$this->db->exec("INSERT OR IGNORE INTO ids (name, value) VALUES ('BID', 0);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS reports (
				id INTEGER PRIMARY KEY,
				by_name TEXT COLLATE NOCASE,
				by_ip TEXT,
				target_name TEXT COLLATE NOCASE,
				target_ip TEXT,
				flags INTEGER,
				description TEXT,
				logs BLOB,
				assignee TEXT COLLATE NOCASE DEFAULT NULL
				);");
		$this->db->exec("CREATE TABLE IF NOT EXISTS points (
				id INTEGER PRIMARY KEY,
				by_name TEXT,
				ref_report INTEGER DEFAULT -1,
				target_ip TEXT,
				creation INTEGER,
				expiry INTEGER,
				details TEXT DEFAULT ''
				);");
	}
	/**
	 * Get the next unique report ID.
	 *
	 * @return int the next unique report ID
	 */
	public function nextRID(){
		$result = $this->db->query("SELECT value FROM ids WHERE name = 'RID';")->fetchArray(SQLITE3_ASSOC);
		$this->db->exec("UPDATE ids SET value = ".($result["value"] + 1)." WHERE name = 'RID';");
	}
	/**
	 * Get the report of highest priority in the report queue.
	 * This function does NOT shift the report from the report queue.
	 *
	 * @return Report the report of highest priority in the queue
	 */
	public function glanceNextReport(){
		$id = $this->getNextReportIDInQueue();
		if($id === null){
			return null;
		}
		return $this->getReportByID($id);
	}
	private function getReportByID($id){
		$data = $this->db->query("SELECT * FROM reports WHERE id = $id;")->fetchArray(SQLITE3_ASSOC);
		return new Report($this->main, $id, $data["by_name"], $data["by_ip"], $data["target_name"],
			$data["target_ip"], $data["flags"], $data["description"], $data["assignee"], $data["logs"]);
	}
	private function getNextReportIDInQueue(){
		$op = $this->db->prepare("SELECT id FROM reports WHERE
				(assignee = NULL) AND
				((flags & :resolvedflag) = 0) LIMIT 1;");
		$op->bindValue(":resolvedflag", Report::FLAG_RESOLVED);
		$result = $op->execute()->fetchArray(SQLITE3_ASSOC);
		if(!is_array($result)){
			return null;
		}
		return $result["id"];
	}
	/**
	 * Add a report into the report queue.
	 * @param Report $report
	 */
	public function queueReport(Report $report){
		$op = $this->db->prepare("INSERT OR REPLACE INTO reports
				(id, by_name, by_ip, target_name, target_ip, flags, description, assignee, logs) VALUES (
				{$report->getID()},
				{$this->esc($report->getFromName())},
				{$this->esc($report->getFromIP())},
				{$this->esc($report->getToName())},
				{$this->esc($report->getToIP())},
				{$report->getFlags()},
				{$this->esc($report->getDescription())},
				{$report->getAssignee()},
				:logs
				);");
		$op->bindValue(":logs", $report->compressLogs());
		$op->execute();
	}
	/**
	 * Assign a report to $player and shift it from the queue of pending reports.
	 * @param string $player name of the player to assign the report to
	 * @return Report
	 */
	public function assignAndShiftReport($player){
		$id = $this->getNextReportIDInQueue();
		if($id !== null){
			$this->db->exec("UPDATE reports SET assignee = '{$this->db->escapeString($player)}'
					WHERE id = $id;");
			return $this->getReportByID($id);
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
		$this->db->exec("UPDATE reports SET assignee = NULL WHERE
				assignee = '{$this->db->escapeString($player)}' AND
				(flags & $rf) = 0;");
	}
	/**
	 * Make a report as resolved and, if possible, unassign the assignee of the report.
	 *
	 * @param int $rid the ID of the report to mark as resolved
	 */
	public function resolveReport($rid){
		$this->db->exec("UPDATE reports SET assignee = NULL,
				flags = (flags & ~".Report::FLAG_RESOLVED.")
				WHERE id = $rid;");
	}
	/**
	 * Get the report assigned to the player.
	 *
	 * @param string $player the assignee to search with
	 * @return Report|null the report assigned to $player, or <code>null</code> if none
	 */
	public function getAssignedReport($player){
		$array = $this->db->query("SELECT id FROM players WHERE
				assignee = '{$this->db->escapeString($player)}';")->fetchArray(SQLITE3_ASSOC);
		return is_array($array) ? $this->getReportByID($array["id"]):null;
	}
	/**
	 * Get the next unique ban point issue ID (BID).
	 *
	 * @return int
	 */
	public function nextBID(){
		$id = $this->db->query("SELECT value FROM ids WHERE name = 'BID';")->fetchArray(SQLITE3_ASSOC)["id"];
		$this->db->query("UPDATE ids SET value = ".($id + 1)." WHERE name = 'BID';");
		return $id;
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
		return "'{$this->db->escapeString($str)}'";
	}
}
