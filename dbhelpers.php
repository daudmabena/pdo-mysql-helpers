<?php

  ///////////////////////////////////////////////////////////////////////////////
  // Mike's PDO Helper functions                                               //
  // ===========================                                               //
  // Mike Turley [github.com/mturley] shamelessly copies and pastes these      //
  // functions any time he needs to use PDO. It's better than writing all this //
  // god-awful PHP again every time MySQL shows its ugly head on a LAMP stack. //
  ///////////////////////////////////////////////////////////////////////////////

  // connects to the MySQL database and returns a PDO handle for it.
  function getDB() {
    $dbhost = 'PUT.YOUR.HOST/HERE';
    $dbname = 'DATABASE_NAME_HERE';
    $dbuser = 'USERNAME_HERE';
    $dbpass = 'PASSWORD_HERE';
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }

  // getInsertSQL()
  // Usage: creates a SQL string ready for PDO bindings in an INSERT query.
  // Example Call:
  //   getInsertSQL("people", array("name", "email"));
  //     >> "INSERT INTO people (name, email) VALUES (:name, :email)"
  function getInsertSQL($tableName, $fieldsArr) {
    $sqlkeys = "";
    $sqlvals = "";
    foreach($fieldsArr as $i => $field) {
      $sqlkeys .= $field;
      $sqlvals .= ":".$field;
      if($i != sizeof($fieldsArr) - 1) {
        $sqlkeys .= ", ";
        $sqlvals .= ", ";
      }
    }
    return "INSERT INTO ".$tableName." (".$sqlkeys.") VALUES (".$sqlvals.")";
  }

  // getUpdateSQL()
  // Usage: creates a SQL string ready for PDO bindings in an UPDATE query.
  // Example Call:
  //   getUpdateSQL("people", array("name", "stuff"), "email");
  //     >> "UPDATE people SET name=:name, stuff=:stuff WHERE email=:email"
  // NOTE: the UPDATE sql query this function creates must not only be bound to
  //       the values of each field, but also to the value of :email, or
  //       whatever the $whereField is, at query prepare time.
  function getUpdateSQL($tableName, $fieldsArr, $whereField) {
    $sql = "UPDATE ".$tableName." SET ";
    $firstField = true;
    foreach($fieldsArr as $i => $field) {
      if($firstField) {
        $firstField = false;
      } else {
        $sql .= ", ";
      }
      $sql .= $field."=:".$field;
    }
    $sql .= " WHERE ".$whereField."=:".$whereField;
    return $sql;
  }

  // getSelectSQL()
  // Usage: creates a SQL string ready for PDO bindings in a SELECT query.
  // Example Calls:
  //   getSelectSQL("people", "email", "=", array("name, stuff"), 1);
  //     >> "SELECT name, stuff FROM people WHERE email = :email LIMIT 1"
  //   getSelectSQL("people", "email", "=", array("name, stuff"));
  //     >> "SELECT name, stuff FROM people WHERE email = :email"
  //   getSelectSQL("people", "email", "=");
  //     >> "SELECT * FROM people WHERE email = :email"
  //   
  function getSelectSQL($tableName, $whereField, $whereSign, $fieldsArr = array(), $limit = null) {
    $sql = "SELECT ";
    if(sizeof($fieldsArr) == 0) {
      $sql .= "*";
    } else {
      $firstField = true;
      foreach($fieldsArr as $i => $field) {
        if($firstField) {
          $firstField = false;
        } else {
          $sql .= ", ";
        }
        $sql .= $field;
      }
    }
    $sql .= " FROM ".$tableName." WHERE ".$whereField." ".$whereSign." :".$whereField;
    if($limit != null) {
      $sql .= " LIMIT ".$limit;
    }
    return $sql;
  }

?>