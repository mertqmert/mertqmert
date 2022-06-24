<?php
define("mysqlServer", "localhost");
define("mysqlDB", "geodb");
define("mysqlUser", "ird");
define("mysqlPass", "geheim");

class connectToDB
{
    public static function connectionSuccessful()
    {
        $db_connection = new mysqli(mysqlServer, mysqlUser, mysqlPass, mysqlDB);
        //if ($mysqli->connect_errno) {
        if (!$db_connection) {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno() . ") " . $mysqli->connect_error();
            return false;
        } else {
            return true;
        }
    }



    public static function addLitter($litterID, $littertype, $description, $latitude, $longitude, $discordname)
    {
        $db_connection = new mysqli(mysqlServer, mysqlUser, mysqlPass, mysqlDB);
        //if ($mysqli->connect_errno) {
        if (!$db_connection) {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno() . ") " . $mysqli->connect_error();
        }
        $statement = $db_connection->prepare("Insert INTO litter(litterID, littertype, description, latitude, longitude, discordname) VALUES(?, ?, ?, ?, ?, ?)");
        $statement->bind_param('ssssss', $litterID, $littertype, $description, $latitude, $longitude, $discordname);
        $statement->execute();
        $statement->close();
        $db_connection->close();
    }

    public static function getNameById($id)
    {
        $db_connection = new mysqli(mysqlServer, mysqlUser, mysqlPass, mysqlDB);
        $query = "Select litterID from litter where id = $id";
        $litterIDName = "unknown";
        if ($result = $db_connection->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $litterIDName = $row['litterID'];
            }
        }

        $db_connection->close();
        return $litterIDName;
    }

    public static function getLitterList()
    {
        $arr = array();
        $db_connection = new mysqli(mysqlServer, mysqlUser, mysqlPass, mysqlDB);
        $statement = $db_connection->prepare("Select id, litterID, littertype, description, latitude, longitude, discordname from litter order by litterID ASC");
        $statement->bind_result($id, $litterID, $littertype, $description, $latitude, $longitude, $discordname);
        $statement->execute();

        while ($statement->fetch()) {
            // $arr[] = [ "id" => $id, "litterID" => $litterID, "description" => $description, "latitude" => $latitude, "longitude" => $longitude, "discordname" => $discordname];
            $arr[] = array("id" => $id, "litterID" => $litterID, "littertype" => $littertype, "description" => $description, "latitude" => $latitude, "longitude" => $longitude, "discordname" => $discordname);
        }
        $statement->close();
        $db_connection->close();
        return $arr;
    }
    public static function getJsonEncodedLitterList()
    {
        $arr = array();
        $db_connection = new mysqli(mysqlServer, mysqlUser, mysqlPass, mysqlDB);
        $statement = $db_connection->prepare("Select id, litterID, description, latitude, longitude, discordname from litter order by litterID ASC");
        $statement->bind_result($id, $litterID, $description, $latitude, $longitude, $discordname);
        $statement->execute();

        while ($statement->fetch()) {
            $arr[] = array("id" => $id, "litterID" => $litterID, "description" => $description, "latitude" => $latitude, "longitude" => $longitude, "discordname" => $discordname);
        }
        $statement->close();
        $db_connection->close();
        $arr = json_encode($arr);
        return $arr;
    }
    public static function updateLitterID($id, $littertype, $description, $latitude, $longitude, $discordname)
    {
        $db_connection = new mysqli(mysqlServer, mysqlUser, mysqlPass, mysqlDB);
        $statement = $db_connection->prepare("Update litter SET littertype = ?, description = ?,latitude = ?,longitude = ?,
        discordname = ? where id = ?");
        $statement->bind_param('sssssi', $littertype, $description, $latitude, $longitude, $discordname, $id);
        $statement->execute();
        $statement->close();
        $db_connection->close();
    }
    public static function deleteLitterID($id)
    {
        $db_connection = new mysqli(mysqlServer, mysqlUser, mysqlPass, mysqlDB);
        $statement = $db_connection->prepare("Delete from litter where id = ?");
        $statement->bind_param('i', $id);
        $statement->execute();
        $statement->close();
        $db_connection->close();
    }
}