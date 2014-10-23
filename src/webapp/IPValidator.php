<?php 
namespace tdt4237\webapp;

class IPValidator 
{	

	const INSERT_IP_QUERY = "";
	const COUNT_IP_QUERY = "";

	static $app;
	

	function __construct()
	{
	}

    /**
     * Return true if more than 5 login attempts today
     */
    static function exceededLoginAttempts($ip) {

    	date_default_timezone_set('Europe/Oslo');

    	$date = date("Ymd");

    	$query = "SELECT id FROM login_attempts WHERE time_attempted >= '$date' and time_attempted <= '$date' and ip = '$ip'";
    	$result = self::$app->db->query($query);

    	$attempts = 0;

    	foreach ($result as $row) {
    		$attempts++;
    	}

    	if($attempts > 5){
    		return true;
    	}

    	return false;
    	
    }

    /**
     * Register failed attempt 
     */
    static function registerAttempt($ip) {

    	date_default_timezone_set('Europe/Oslo');
    	$date = date("Ymd");
    	
    	$query = "INSERT INTO login_attempts (ip, time_attempted) VALUES('$ip', '$date')";
    	self::$app->db->exec($query);

    }

}

try {
    // Create (connect to) SQLite database in file
	Sql::$pdo = new \PDO('sqlite:app.db');
    // Set errormode to exceptions
	Sql::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch(\PDOException $e) {
	echo $e->getMessage();
	exit();
}

IPValidator::$app = \Slim\Slim::getInstance();