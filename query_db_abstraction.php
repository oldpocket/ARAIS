<?php

/**
* Class DBConnection 
* 
* Abstract the database connection and low level database
* library calls for the query builder layer. If one decides to change the 
* database type, this should be the only place to change.
*
* @author      Fabio Andreozzi Godoy <fabio.godoy@oldpocket.com>
* @copyright   2019-2020 Fabio Godoy
* @link        https://github.com/oldpocket/ARAIS
* @license     https://github.com/oldpocket/ARAIS/blob/master/LICENSE
* @version     0.5.0
* @package     ARAIS
*/
class QueryDBAbstraction
{
    /**
    * @var DB
    */
    private $db = null;

    /**
    * @return DB
    */
    protected function connect()
    {
        if (!$this->db) {
            
            // Create a new database, if the file doesn't exist and open it for reading/writing.
            // The extension of the file is arbitrary.
            $this->db = new SQLite3(getenv('SQLLITE_FILE_NAME'), SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        /*
            // ToDo : load external SQL file with all SQL create statements
            $this->db->query('CREATE TABLE IF NOT EXISTS "data" (
                            "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                            "device_uid" VARCHAR,
                            "sensor_uid" VARCHAR,
                            "value" INTEGER,
                            "timestamp" TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
            )'); */
        }
        return $this->db;
    }

    /**
    * @param string $sql
    * @param array $values
    * @return string
    */
    protected final function executeInsert($sql, array $values)
    {
        $statement = $this->statement($sql);

        foreach ($values as $key => $value) {
            $statement->bindValue($key+1, $value);
        }

        if ($statement && $statement->execute()) {
            return $this->connect()->lastInsertRowID();
        }

        return null;
    }

    /**
    * @param string $sql
    * @param array $values
    * @return array
    */
    protected final function executeSelect($sql, array $values)
    {
        $statement = $this->statement($sql);

        foreach ($values as $key => $value) {
            $statement->bindValue($key+1, $value);
        }
        
        $results = $statement->execute();
        
        if (!$results) return null;
        
        $resultArray = array();
        // Get all the row results
        while($entry = $results->fetchArray(SQLITE3_ASSOC)) {
            $resultArray[] = $entry;
        };
        
        $result['request_date'] = date("Y-m-d H:i:s");
        $result['values'] = $resultArray;
        
        return json_decode(json_encode($result));
    }

    /**
    * @param string $sql
    * @param array $values
    * @return int
    */
    protected final function executeUpdate($sql, array $values)
    {
        $statement = $this->statement($sql);

        foreach ($values as $key => $value) {
            $statement->bindValue($key+1, $value);
        }

        if ($statement && $statement->execute()) {
            return $this->connect()->changes();
        }

        return null;
    }

    /**
    * @param string $sql
    * @param array $values
    * @return int
    */
    protected final function executeDelete($sql, array $values)
    {
        $statement = $this->statement($sql);

        foreach ($values as $key => $value) {
            $statement->bindValue($key+1, $value);
        }
        
        if ($statement && $statement->execute()) {
            return $this->connect()->changes();
        }
        
        return null;
    }

    /**
    * @param $sql
    * @param array $values
    * @return int|null
    */
    protected final function execute($sql, array $values)
    {
        $statement = $this->statement($sql);

        if ($statement && $statement->execute(array_values($values))) {
            return $statement->rowCount();
        }

        return null;
    }
    
    /**
    * @param $sql
    * @return PDOStatement
    */
    private final function statement($sql)
    {
        return $this->connect()->prepare($sql);
    }
} 
