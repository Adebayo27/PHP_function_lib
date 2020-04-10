<?php
//This is a function that prepares and execute queries
function query($pdo, $sql, $fields = array()){
	$query = $pdo->prepare($sql);
	$query->execute($fields);
	return $query;
}
//This function helps to fetch all data from a table
//It takes 2 arguments:
// 1. connection string, 2. Table name 
function f_findAll($pdo, $table){
	$sql = query($pdo, "SELECT * FROM  `". $table . "` ");
	return $sql->fetchAll();
}
//This function helps to get a particular data from a table using a primary key
function f_findById($pdo, $table, $primarykey, $id){
	$sql = "SELECT * FROM `" . $table . "` WHERE `" . $primarykey . "` = :id";
	$parameters = array('id'=>$id);
	$qry = query($pdo, $sql, $parameters);
	return $qry->fetch();
}
//This function helps to get the total number of data in a table
function f_total($pdo, $table){
	$sql = query($pdo, "SELECT COUNT(*) FROM `" . $table . "`");
	$row = $sql->fetch();
	return $row[0];
}
//This function helps to process dates
function processDates($fields){
	foreach ($fields as $key => $value) {
		if ($value instanceof DateTime) {
			$fields[$key] = $value->format('Y-m-d');
		}
	}
	return $fields;
}
//This is a function that helps to insert data into a table. It prepares the query and execute it. All you have to do is to provide the:
// 1. connection string, 2. table name, 3. fields of the table with the data 
//e.g f_insert($pdo, 'joketable', array('id'=>1, 'joketext'=>'i am a joke')) 
function f_insert($pdo, $table, $fields){
	$sql = "INSERT INTO `" .$table. "` (";
	foreach ($fields as $key => $value) {
		$sql.= "`". $key . "`,";
	}
	$sql = rtrim($sql, ',');
	$sql.= ") VALUES(";
	foreach ($fields as $key => $value) {
	 	$sql .= ":" . $key . ",";
	 } 
	$sql = rtrim($sql, ',');
	$sql .= ")";
	$fields = processDates($fields);
	query($pdo, $sql, $fields); 
}
//This is a function that helps to update data in a table. It prepares the query and execute it. All you have to do is to provide the:
// 1. connection string, 2. table name, 3. primarykey, 4. fields of the table with the data 
//e.g f_update($pdo, 'joketable', 'id' ,array('id'=>1, 'joketext'=>'i am a joke'))
function f_update($pdo, $table, $primarykey, $fields){
	$sql = "UPDATE `" . $table . "` SET";
	foreach ($fields as $key => $value) {
		$sql.= "`" . $key . "` = :" . $key . " ,"; 
	}
	$sql = rtrim($sql, ',');
	$sql.= "WHERE `" . $primarykey. "` = :primarykey";
	$fields['primarykey'] = $fields['id'];
	$fields = processDates($fields);
	// var_dump($sql); die;	

	query($pdo, $sql, $fields);
}
// A delete function
function f_delete($pdo, $table, $primarykey, $id){
	$parameters = array(':id'=> $id);
	$qry =  'DELETE FROM `' .$table. '` WHERE `' .$primarykey. '` = :id';
	$sql = query($pdo, $qry, $parameters);
}
// This function helps to toggle btw update and insert query
//If a primary key is provided, it will update else it will insert 
function f_save($pdo, $table, $primarykey, $record){
	try {
		if ($record[$primarykey] == '') {
			$record[$primarykey] = NULL;
		}
		f_insert($pdo, $table, $record);
	} catch (PDOException $e) {
		f_update($pdo, $table, $primarykey, $record);
	}
}

?>