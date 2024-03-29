<?php

namespace tdt4237\webapp;

use tdt4237\webapp\models\User;

class Sql
{
    static $pdo;

    function __construct()
    {
    }

    /**
     * Create tables.
     */
    static function up() {
        $q1 = "CREATE TABLE users (id INTEGER PRIMARY KEY, user VARCHAR(50), pass VARCHAR(50), email varchar(50), age varchar(50), imageurl VARCHAR(100), bio varhar(50), isadmin INTEGER);";
        $q4 = "CREATE TABLE movies (id INTEGER PRIMARY KEY, name VARVHAR(50), imageurl VARCHAR(100) );";
        $q5 = "CREATE TABLE moviereviews (id INTEGER PRIMARY KEY, movieid INTEGER, author VARVHAR(50), text VARCHAR(500) );";
        $q6 = "CREATE TABLE login_attempts (id INTEGER PRIMARY KEY, ip varchar(16),time_attempted varchar(10));";
        $q7 = "CREATE TABLE password_requested (id INTEGER PRIMARY KEY, user VARCHAR(50), token VARCHAR(200));";

        self::$pdo->exec($q1);
        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        self::$pdo->exec($q6);
        self::$pdo->exec($q7);

        print "[tdt4237] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
        self::insertMovies();
    }

    static function insertDummyUsers() {
        $hash1 = Hash::make(bin2hex(openssl_random_pseudo_bytes(8)));

        $q1 = "INSERT INTO users(user, pass, age, isadmin, imageurl) VALUES ('admin', '$hash1', 14, 1, 'http://1.bp.blogspot.com/--8s8qqk-vSs/UACCw2mv84I/AAAAAAAAAJw/s_3rV7-JEvs/s1600/who-s-the-boss.jpg')";

        self::$pdo->exec($q1);


        print "[tdt4237] Done inserting dummy users.".PHP_EOL;
    }

    static function insertMovies() {
        $movies = [
            ['American Psycho', 'psycho.jpg'],
            ['Open Your Eyes', 'eyes.jpg'],
            ['Wild Strawberries', 'strawberries.jpg'],
            ['The Seventh Seal', 'seal.jpg'],
            ['Cube', 'cube.jpg'],
            ['Sin City', 'sincity.jpg'],
            ['Signs', 'signs.jpg'],
            ['A.I. Artificial Intelligence', 'ai.jpg'],
        ];

        foreach ($movies as $movie) {
            $name = $movie[0];
            $imageUrl = $movie[1];

            $q = "INSERT INTO movies(name, imageurl) VALUES ('$name', '$imageUrl') ";
            self::$pdo->exec($q);
        }

        print "[tdt4237] Done inserting dummy movies.".PHP_EOL;
    }

    static function down() {
        $q1 = "DROP TABLE users";
        $q4 = "DROP TABLE movies";
        $q5 = "DROP TABLE moviereviews";
        $q6 = "DROP TABLE login_attempts";

        self::$pdo->exec($q1);
        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        self::$pdo->exec($q6);

        print "[tdt4237] Done deleting all SQL tables.".PHP_EOL;
    }
}
try {
    // Create (connect to) SQLite database in file
    Sql::$pdo = new \PDO('sqlite:app.db');
    // Set errormode to exceptions
    Sql::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
} catch(\PDOException $e) {
    echo $e->getMessage();
    exit();
}
