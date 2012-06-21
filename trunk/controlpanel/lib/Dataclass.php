<?php
/**
 * Description of dataclass
 *
 * @author conradv
 */
class Dataclass {
    //put your code here
    private $dbconn;
    
    function __construct() {
        $this->dbconn = new DBconn(HOST, DATABASE, USER, PASSWORD);
    }
    
    function get_Users(){
        $sql = "SELECT * FROM tbl_admin;";
        return $this->dbconn->fetch($sql, null);
    }
    
    function check_Login($username, $password){
        $data = array("username"=>$username, "password"=>$password);
        return $this->dbconn->fetch("SELECT * FROM tbl_admin WHERE username=:username AND password=:password;", $data);
    }
        
    function update_password($password){
        $data = array("password"=>sha1($password));
        $this->dbconn->update("UPDATE tbl_admin set password=:password WHERE idtbladmin=1;", $data);
    }
        
    function update_room($id, $floor, $room){
        $data = array("idtbl_room"=>$id, "tbl_floor"=>$floor, "tbl_room"=>$room);
        return $this->dbconn->update("UPDATE tbl_rooms set tbl_room=:tbl_room, tbl_floorId=:tbl_floor WHERE idtbl_rooms=:idtbl_room;", $data);
    }
    
    function delete_room($id){
        $data = array("idtbl_room"=>$id);
        return $this->dbconn->update("DELETE FROM tbl_rooms WHERE idtbl_rooms=:idtbl_room;", $data);
    }
    
    function insert_room($floor, $room){
        $data = array("tbl_floor"=>$floor, "tbl_room"=>$room);
        return $this->dbconn->update("INSERT INTO tbl_rooms (tbl_room, tbl_floorId) VALUES (:tbl_room, :tbl_floor);", $data);
    }
    
    function get_Floors(){
        $sql = "SELECT * FROM tbl_floors;";
        return $this->dbconn->fetch($sql, null);
    }
    
    function insert_Floor($floor){
        $data = array("tbl_floor"=>$floor);
        return $this->dbconn->update("INSERT INTO tbl_floors (tbl_floor) VALUES (:tbl_floor);", $data);
    }
    
    function update_Floor($id, $floor){
        $data = array("id"=>$id, "floor"=>$floor);
        $sql = "UPDATE tbl_floors set tbl_floor=:floor WHERE idtbl_floor=:id;";
        return $this->dbconn->update($sql, $data);
    }

    function delete_Floor($id){
        $data = array("id"=>$id);
        $sql = "DELETE FROM tbl_floors WHERE idtbl_floor=:id;";
        return $this->dbconn->fetch($sql, $data);
    }
    
    function get_Schedules(){
        $sql = "SELECT * FROM tbl_schedules;";
        return $this->dbconn->fetch($sql, null);
    }

    function insert_Schedule($summary, $on, $off, $repeat){
        $data = array("summary"=>$summary, "on"=>$on, "off"=>$off , "repeat"=>$repeat);
        $sql = "INSERT INTO tbl_schedules (schedule_summary, schedule_on, schedule_off, schedule_repeat) values(:summary, :on, :off, :repeat);";
        return $this->dbconn->fetch($sql, $data);
    }

    function update_Schedule($id, $summary, $on, $off, $repeat){
        $data = array("jobID"=>$id, "summary"=>$summary, "on"=>$on, "off"=>$off , "repeat"=>$repeat);
        $sql = "UPDATE tbl_schedules SET schedule_summary=:summary, schedule_on=:on, schedule_off=:off, schedule_repeat=:repeat WHERE idtbl_schedules=:jobID;";        
        return $this->dbconn->fetch($sql, $data);
    }

    function delete_Schedule($id){
        $data = array("id"=>$id);
        $sql = "DELETE FROM tbl_schedules WHERE idtbl_schedules=:id;";
        return $this->dbconn->delete($sql, $data);
    }


    function get_Rooms($floor=null){
        if ($floor != null)
            $sql = "SELECT * FROM tbl_rooms where tbl_floorId=$floor;";
        else
            $sql = "SELECT * FROM tbl_rooms Inner Join tbl_floors on tbl_floorId=idtbl_floor;";
        return $this->dbconn->fetch($sql, null);
    }
}

?>
