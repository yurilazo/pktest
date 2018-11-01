<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	try {
		$conn = new PDO("mysql:host=localhost;dbname=batallaspokemon", 'root', 'root');

		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
		echo "Connection failed: " . $e->getMessage()."<br/>";
	}

	function indexar_arreglo($arreglo, $campo, $multi_row = TRUE) {


		$arreglo_indexado = array();

		foreach ($arreglo as $row) {

			if( $multi_row )
				$arreglo_indexado[$row[$campo]][] = $row;
			else
				$arreglo_indexado[$row[$campo]] = $row;
		}
		return $arreglo_indexado;
	}

	function get_dex() {

		global $conn;

		$tipos = indexar_arreglo($conn->query('SELECT * FROM `types` ORDER BY `id_type`'),'id_type',FALSE);
		$habilidades = indexar_arreglo($conn->query('SELECT `id_ability`, `name_ability` FROM `abilitys` ORDER BY `id_ability`'),'id_ability',FALSE);
		$habilidades_especies = indexar_arreglo($conn->query('SELECT `id_specie`, `id_ability`, `hide` FROM `species_abilitys`'),'id_specie');
		$tipos_especies = indexar_arreglo($conn->query('SELECT `id_specie`, `id_type` FROM `species_types` ORDER BY `id_species_types`'),'id_specie');
		$categorias = array_merge(indexar_arreglo($conn->query('SELECT * FROM `tiers`'),'id_tier',FALSE), array( 0 => array('acronym_tier'=>'Sin categoría') ) );
		$especies = $conn->query('SELECT * FROM `species`');

		return compact('tipos','habilidades','habilidades_especies','tipos_especies','categorias','especies');
	}

	function show_dex(){

		extract(get_dex());

		?><table id="dex" class="table table-borderless table-responsive-sm table-sm table-striped">
			<thead>
				<tr>
					<th scope="col" class="sorting">#</th>
					<th scope="col" class="sorting">Categoría</th>
					<th scope="col" class="sorting">Nombre</th>
					<th scope="col" class="sorting">Tipo</th>
					<th scope="col" class="sorting">Habilidad</th>
					<th scope="col" class="sorting">HP</th>
					<th scope="col" class="sorting">ATQ</th>
					<th scope="col" class="sorting">DEF</th>
					<th scope="col" class="sorting">SPATQ</th>
					<th scope="col" class="sorting">SPDEF</th>
					<th scope="col" class="sorting">VEL</th>
					<th scope="col" class="sorting">Gen</th>
				</tr>
			</thead>
			<tbody><?php
		foreach ($especies as $especie) {

			$show_type = '';
			foreach ($tipos_especies[$especie['id_specie']] as $tipo_especie) {

				$show_type .= $tipos[$tipo_especie['id_type']]['name_type'].'<br>';
			}

			$show_hab = '';
			foreach ($habilidades_especies[$especie['id_specie']] as $habilidad_especie) {

				$show_hab .= ( $habilidad_especie['hide'] )? 'Habilidad oculta: ' : '';
				$show_hab .= $habilidades[$habilidad_especie['id_ability']]['name_ability'].'<br>';
			}

			echo '<tr><th scope="row" class="align-middle">'.$especie['dex_number'].'</th><td class="align-middle">'.$categorias[$especie['id_tier']]['acronym_tier'].'</td><td class="align-middle">'.$especie['name_specie'].'</td><td class="align-middle">'.$show_type.'</td><td class="align-middle">'.$show_hab.'</td><td class="align-middle">'.$especie['hp_specie'].'</td><td class="align-middle">'.$especie['atk_specie'].'</td><td class="align-middle">'.$especie['def_specie'].'</td><td class="align-middle">'.$especie['spatk_specie'].'</td><td class="align-middle">'.$especie['spdef_specie'].'</td><td class="align-middle">'.$especie['spe_specie'].'</td><td class="align-middle">'.$especie['id_generation_specie'].'</td></tr>';
		}
		?></tbody></table><?php
	}

	function get_movedex() {
		global $conn;

		$movimientos = $conn->query('SELECT * FROM `moves`');
		$tipos = indexar_arreglo($conn->query('SELECT * FROM `types` ORDER BY `id_type`'),'id_type',FALSE);
		$categorias_movimiento = array('0'=>'Físico','1'=>'Especial','2'=>'estatus');
		return compact('movimientos','tipos','categorias_movimiento');
	}

	function show_movedex(){

		extract(get_movedex());

		?><table id="dex" class="table table-borderless table-responsive-sm table-sm table-striped"><?php
		foreach ($movimientos as $movimiento) {

			echo '<tr><th scope="row">'.$movimiento['id_move'].'</th><td>'.$movimiento['name_move'].'</td><td>'.$tipos[$movimiento['id_type_move']]['name_type'].'</td><td>'.$categorias_movimiento[$movimiento['category_move']].'</td><td>'.$movimiento['pp_move'].'</td><td>'.$movimiento['power_move'].'</td><td>'.$movimiento['accuray_move'].'</td><td>'.$movimiento['description_move'].'</td><td>'.$movimiento['probability_move'].'</td><td>'.$movimiento['id_generation_move'].'</td></tr>';
		}
		?></table><?php
	}

	function get_form_stats_vars(){

		?>
		<form id="form_stats_vars" class="form">
			<div class="form-group"><div class="form-label">nivel</div><input type="text" name="level"/></div>
			<div class="form-group"><div class="form-label">stat base</div><input type="text" name="statbase"/></div>
			<div class="form-group"><div class="form-label">Puntos de esfuerzo</div><input type="text" name="ep"/></div>
			<div class="form-group"><div class="form-label">Valor individual</div><input type="text" name="iv"/></div>
			<div class="form-group"><div class="form-label">Valor de personalidad</div><input type="text" name="pv"/></div>
			<div class="form-group"><div class="form-label">Tipo de stats</div>
				<select name="statetoset">
					<option value="hp">hp</option>
					<option value="atk">atk</option>
					<option value="def">def</option>
					<option value="spatk">spatk</option>
					<option value="spdef">spdef</option>
					<option value="spe">spe</option>
				</select>
			</div>
			<div class="form-group"><input type="submit" name="calcular" value="calcular!"></div>
		</form>
		<?php
	}

	function show_stats_form(){

		?>
		<form id="stats_form" class="form">
			<div class="row">
				<div class="col-2">
					<div class="form-group"><label>Pokemon</label></div>
					<div class="form-group"><label>id_specie</label><input class="form-control" type="text" name="id_specie"/></div>
					<div class="form-group"><label>nivel</label><input class="form-control" type="text" name="level"/></div>
					<div class="form-group"><div class="form-label">Naturaleza</div>
						<select name="pv" class="form-control">
							<option value="adamant">adamant</option>
							<option value="bashful">bashful</option>
							<option value="bold">bold</option>
							<option value="brave">brave</option>
							<option value="calm">calm</option>
							<option value="careful">careful</option>
							<option value="docile">docile</option>
							<option value="gentle">gentle</option>
							<option value="hardy">hardy</option>
							<option value="hasty">hasty</option>
							<option value="impish">impish</option>
							<option value="jolly">jolly</option>
							<option value="lax">lax</option>
							<option value="lonely">lonely</option>
							<option value="mild">mild</option>
							<option value="modest">modest</option>
							<option value="naive">naive</option>
							<option value="naughty">naughty</option>
							<option value="quiet">quiet</option>
							<option value="quirky">quirky</option>
							<option value="rash">rash</option>
							<option value="relaxed">relaxed</option>
							<option value="sassy">sassy</option>
							<option value="serious">serious</option>
							<option value="timid">timid</option>
						</select>
					</div>
				</div>
				<div class="col-2">
					<div class="form-group"><label>IVS</label></div>
					<div class="form-group"><label>hp</label><input class="form-control" type="text" name="iv_hp"/></div>
					<div class="form-group"><label>atk</label><input class="form-control" type="text" name="iv_atk"/></div>
					<div class="form-group"><label>def</label><input class="form-control" type="text" name="iv_def"/></div>
					<div class="form-group"><label>spa</label><input class="form-control" type="text" name="iv_spa"/></div>
					<div class="form-group"><label>spd</label><input class="form-control" type="text" name="iv_spd"/></div>
					<div class="form-group"><label>spe</label><input class="form-control" type="text" name="iv_spe"/></div>
				</div>
				<div class="col-2">
					<div class="form-group"><label>EPS</label></div>
					<div class="form-group"><label>hp</label><input class="form-control" type="text" name="ep_hp"/></div>
					<div class="form-group"><label>atk</label><input class="form-control" type="text" name="ep_atk"/></div>
					<div class="form-group"><label>def</label><input class="form-control" type="text" name="ep_def"/></div>
					<div class="form-group"><label>spa</label><input class="form-control" type="text" name="ep_spa"/></div>
					<div class="form-group"><label>spd</label><input class="form-control" type="text" name="ep_spd"/></div>
					<div class="form-group"><label>spe</label><input class="form-control" type="text" name="ep_spe"/></div>
				</div>
				<div class="form-group"><input type="submit" class="form-control" name="calcular" value="calcular!"></div>
			</div>
		</form>
		<?php
	}
?>

<!doctype html>
<html lang="es">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<link rel="stylesheet" href="css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<script src="jquery-latest.min.js" type="text/javascript"></script>
		<title>Pokémon Battle Frontier</title>
	</head>
	<body>
		<div class="container">
			<?php show_stats_form(); ?>
			<input type="text" name="text1" id="text1">
			<button id="test1">prueba 1</button><br/>
			<input type="text" name="text2" id="text2">
			<button id="test2">prueba 2</button>
		</div>
	</body>
	<script type="text/javascript">var exports = {};</script>
	<script type="text/javascript" src="pokedex.js"></script>
	<script type="text/javascript" src="user_pokemons.js"></script>
	<script type="text/javascript">

		var flaglist = {
			"protect": 1,
			"mirror": 1,
			"heal": 1,
			"contact": 1,
			"snatch": 1,
			"bullet": 1,
			"distance": 1,
			"authentic": 1,
			"mystery": 1,
			"reflectable": 1,
			"pulse": 1,
			"bite": 1,
			"recharge": 1,
			"nonsky": 1,
			"sound": 1,
			"charge": 1,
			"gravity": 1,
			"punch": 1,
			"defrost": 1,
			"powder": 1,
			"dance": 1
		};
		
		var pk = {};
		$(document).on('click','#test1',function(){
			pk = new pokemon($('#text1').val());
			console.log(pk);
		});
		$(document).on('click','#test2',function(){

		});
	</script>
</html>
