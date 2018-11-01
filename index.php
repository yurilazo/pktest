<meta charset="utf-8" />
<body>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


try {
	$conn = new PDO("mysql:host=localhost;dbname=batallaspokemon", 'root', 'root');

	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	echo "Connected successfully<br/><br/>";
} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage()."<br/>";
}

function consulta_test() {
	try {

		$name_type_attack = 'grass';
		$name_type_defense = 'water';

		$result = $conn->query('SELECT `type_attack`.`name_type` as `name_type_attack`, `type_defense`.`name_type` as `name_type_defense`, `multiplier`
			FROM `effectiveness`
			JOIN `types` as `type_attack` ON `effectiveness`.`id_type_attack` = `type_attack`.`id_type`
			JOIN `types` as `type_defense` ON `effectiveness`.`id_type_defense` = `type_defense`.`id_type`
			WHERE `id_effectiveness` > 100 AND `id_effectiveness` < 140');

		foreach ($result as $row) {
			echo 'ataque tipo '.$row["name_type_attack"].' afecta a defensa tipo '.$row["name_type_defense"].' con '.($row["multiplier"]*100).'% de efectividad <br/>';
		}

	} catch(PDOException $e) {
	    echo "ERROR: " . $e->getMessage();
	}
}

//$conn->query('TRUNCATE TABLE `effectiveness`');
?>
</body>
