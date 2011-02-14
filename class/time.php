<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
////	File:
////		time_class.php
////	Actions:
////		1) time equations
////	Account:
////		Added on July 22nd 2007 for TERNSTYLE v1.0.0
////
////	Written by Matthew Praetzel. Copyright (c) 2007 Matthew Praetzel.
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

if(!class_exists('timeClass')) {
//
class timeClass {

	function clientTime($o) {
		return timeClass::utcNow()+(intval($o));
	}
	function timestamp($m,$d,$y,$h,$n,$s) {
		return mktime($h,$n,$s,$m,$d,$y);
	}
	function utcStamp($m,$d,$y,$h,$n,$s) {
		return gmmktime($h,$n,$s,$m,$d,$y);
	}
	function utcNow() {
		$t = time();
		return gmmktime(gmdate("H",$t),gmdate("i",$t),gmdate("s",$t),gmdate("m",$t),gmdate("d",$t),gmdate("Y",$t));
	}
	function utcAtTime($m,$d,$y,$h,$n,$s,$o) {
		return gmmktime($h,$n,$s,$m,$d,$y)-$o;
	}
	function atStartStamp($t) {
		return gmmktime(0,0,0,gmdate("m",$t),gmdate("d",$t),gmdate("Y",$t));
	}
	function dayInSameWeek($s,$d) {
		$s = timeClass::atStartStamp($s);
		$w = gmdate("w",$s);
		if($w > $d) {
			return $s-(($w-$d)*86400);
		}
		return $s+(($d-$w)*86400);
	}
	function startOfWeek($s,$p) {
		$s = $p ? timeClass::atStartStamp($s) : $s;
		$o = gmdate('w',$s);
		return $s-($o*86400);
	}
	function firstDayOfMonth($s) {
		return gmmktime(0,0,0,gmdate("n",$s),1,gmdate("Y",$s));
	}
	function lastDayOfMonth($s) {
		return gmmktime(0,0,0,gmdate("n",$s),gmdate("t",$s),gmdate("Y",$s));
	}
	
	function numberOfWeekDaysInMonth($m,$y,$d) {
		$f = gmmktime(0,0,0,$m,1,$y);
		$s = timeClass::startOfWeek($f,true);
		$w = gmdate("w",$f);
		if($w > $d) {
			$f = $s+604800+($d*86400);
		}
		elseif($w < $d) {
			$f += ($d-$w)*86400;
		}
		$c = 0;
		while(gmdate("n",$f) == $m) {
			$f += 604800;
			$c++;
		}
		return $c;
	}
	function numberOfWeekDaysAfterDate($s,$d) {
		$f = timeClass::atStartStamp($s);
		$m = gmdate("n",$f);
		$s = timeClass::startOfWeek($f,true);
		$w = gmdate("w",$f);
		if($w > $d) {
			$f = $s+604800+($d*86400);
		}
		elseif($w < $d) {
			$f += ($d-$w)*86400;
		}
		$c = 0;
		while(gmdate("n",$f) == $m) {
			$f += 604800;
			$c++;
		}
		return $c;
	}
	function getNextWeekDay($s,$d) {
		$f = timeClass::atStartStamp($s);
		$s = timeClass::startOfWeek($f,true);
		$w = gmdate("w",$f);
		if($w > $d) {
			$f = $s+604800+($d*86400);
		}
		elseif($w < $d) {
			$f += ($d-$w)*86400;
		}
		return $f;
	}
	
	function fixOffset($o) {
		$b = strlen($o) > 4 ? true : false;
		$h = $b ? substr($o,1,2) : substr($o,0,2);
		$h = intval($h)*3600;
		$m = $b ? substr($o,3,2) : substr($o,2,2);
		$m = intval($m)*60;
		$s = $b ? -($h+$m) : $h+$m;
		return $s;
	}
	function importTZDB($t) {
		global $getDB,$mysql_address,$mysql_username,$mysql_password,$mysql_database,$row,$multi_row;
		$tz = DateTimeZone::listAbbreviations();
		foreach($tz as $k => $v) {
			for($i=0;$i<count($v);$i++) {
				if(!empty($v[$i]["timezone_id"])) {
					$d = $v[$i]["dst"] == 1 ? 1 : 0;
					$iq = "insert into " . $t . " (name,zone,offset,dst) values ('" . str_replace("_"," ",$v[$i]["timezone_id"]) . "','" . $k . "','" . $v[$i]["offset"] . "','" . $d . "')";
					$getDB -> Connect(2,$iq,$mysql_address,$mysql_username,$mysql_password,$mysql_database,"");
				}
			}
		}
	}

}

$getTIME = new timeClass;
}

/****************************************Terminate Script******************************************/
?>