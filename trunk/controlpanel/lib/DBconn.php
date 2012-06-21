<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dbconn
 *
 * @author conradv
 */
class DBconn {

//put your code here
    private $dbh;

    function __construct($host, $database, $user, $password) {
        try {
            $this->dbh = new PDO("mysql:host=$host;dbname=$database", $user, $password);
        } catch (PDOException $e) {
            echo "There is a problem connecting to database";
        }
    }

    function fetch($sql, $data) {
        $sth = $this->dbh->prepare($sql);
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $sth->execute($data);
        return $sth;
    }

    function insert($sql, $data) {
        $sth = $this->dbh->prepare($sql);
        $sth->execute($data);
        return $this->dbh->lastInsertId();
    }

    function delete($sql, $data) {
        $sth = $this->dbh->prepare($sql);
        $sth->execute($data);
        return $sth->rowCount();
    }

    function update($sql, $data) {

        $sth = $this->dbh->prepare($sql);
        $sth->execute($data);
        return $sth->rowCount();
    }

    function __destruct() {
        $this->dbh = null;
    }

}

?>
