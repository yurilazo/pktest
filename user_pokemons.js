class pokemon {

	constructor(options=null){

		var id_specie = "";

		if ( typeof options === 'string' )
			id_specie = options;
		else if ( typeof options.id_specie === 'string')
			id_specie = options.id_specie;

		if ( typeof exports.battlepokedex[id_specie] !== 'object' ) return false;

		this.id_specie = id_specie;

		this.pv = 'docile';
		this.level = 100;
		this.ivs = {hp:31,atk:31,def:31,spa:31,spd:31,spe:31};
		this.eps = {hp:0,atk:0,def:0,spa:0,spd:0,spe:0};
		this.stats = {hp:0,atk:0,def:0,spa:0,spd:0,spe:0};
	}

	merge_pokemon_data(options = null){

		for ( var prop in this) {

			if ( ( typeof options[prop] === 'string') && ( typeof this[prop] === 'string') )
				this[prop] = options[prop];
			if ( ( typeof options[prop] === 'number') && ( typeof this[prop] === 'number') )
				this[prop] = options[prop];

			for( var stat_type in this[prop]) {
				if ( typeof options[prop][stat_type] === 'number')
					this[prop][stat_type] = options[prop][stat_type];
			}
		}
	}
}

var user_pokemons = {
	pokemons : [],
	set_form_selector : function(selector = 'form') {
		this.form_selector = selector;
	},
	add_pokemon : function (id_specie){
		if( typeof exports.battlepokedex[id_specie] !== 'undefined' ){
			this.pokemons.push(new pokemon(id_specie));
		}
	},
	rm_pokemon : function (pokemon_id){
		if ( typeof this.pokemons[pokemon_id] !== 'undefined' ) {
			delete this.pokemons[pokemon_id];
		}
	},
	calculate_pokemon_stats : function(id_pokemon) {

		//PS: 10 + { Nivel / 100 x [ (Stat Base x 2) + IV + PE ] } + Nivel
		for ( var stat_type in this.pokemons[id_pokemon].stats ) {

			if ( stat_type === "hp") {
				this.pokemons[id_pokemon].stats[stat_type] = parseInt( 10 + ( ( this.pokemons[id_pokemon].level / 100 ) * ( ( exports.battlepokedex[this.pokemons[id_pokemon].id_specie].baseStats[stat_type] * 2 ) + this.pokemons[id_pokemon].ivs[stat_type] + ( this.pokemons[id_pokemon].eps[stat_type] / 4) ) ) + this.pokemons[id_pokemon].level );
			}else{
				var nature_multiplier = this.pvs[this.pokemons[id_pokemon].pv][stat_type];
				if ( !$.isNumeric(nature_multiplier) )
					nature_multiplier = 1;

				this.pokemons[id_pokemon].stats[stat_type] = parseInt( ( 5 + ( ( this.pokemons[id_pokemon].level / 100 ) * ( (exports.battlepokedex[this.pokemons[id_pokemon].id_specie].baseStats[stat_type] * 2) + this.pokemons[id_pokemon].ivs[stat_type] + ( this.pokemons[id_pokemon].eps[stat_type] / 4) ) ) ) * nature_multiplier );
			}
		}
		console.log(this.pokemons[id_pokemon].stats);
	},
	get_form : function(){

		var id_pokemon = 0;

		$form = $(this.form_selector);

		this.pokemons[id_pokemon] = new pokemon($form.find("input[name='id_specie']").val());
		this.pokemons[id_pokemon].level = parseInt( $form.find("input[name='level']").val() );
		this.pokemons[id_pokemon].pv = $form.find("select[name='pv']").val();
		this.pokemons[id_pokemon].ivs.hp = parseInt( $form.find("input[name='iv_hp']").val() );
		this.pokemons[id_pokemon].ivs.atk = parseInt( $form.find("input[name='iv_atk']").val() );
		this.pokemons[id_pokemon].ivs.def = parseInt( $form.find("input[name='iv_def']").val() );
		this.pokemons[id_pokemon].ivs.spa = parseInt( $form.find("input[name='iv_spa']").val() );
		this.pokemons[id_pokemon].ivs.spd = parseInt( $form.find("input[name='iv_spd']").val() );
		this.pokemons[id_pokemon].ivs.spe = parseInt( $form.find("input[name='iv_spe']").val() );
		this.pokemons[id_pokemon].eps.hp = parseInt( $form.find("input[name='ep_hp']").val() );
		this.pokemons[id_pokemon].eps.atk = parseInt( $form.find("input[name='ep_atk']").val() );
		this.pokemons[id_pokemon].eps.def = parseInt( $form.find("input[name='ep_def']").val() );
		this.pokemons[id_pokemon].eps.spa = parseInt( $form.find("input[name='ep_spa']").val() );
		this.pokemons[id_pokemon].eps.spd = parseInt( $form.find("input[name='ep_spd']").val() );
		this.pokemons[id_pokemon].eps.spe = parseInt( $form.find("input[name='ep_spe']").val() );
	},
	submit_stat_form : function(e) {
		e.preventDefault();

		user_pokemons.get_form();
		user_pokemons.calculate_pokemon_stats(0);
	},
	initialize: function(selector = 'form') {

		this.set_form_selector(selector);
		var neuter = 1;
		var booster = 1.1;
		var droper = 0.9;
		this.pvs = {
			adamant : {atk : booster, spa : droper },
			bashful : {spa : neuter, spa : neuter },
			bold : {def : booster, atk : droper },
			brave : {atk : booster, spe : droper },
			calm : {spd : booster, atk : droper },
			careful : {spd : booster, spa : droper },
			docile : {def : neuter, def : neuter },
			gentle : {spd : booster, def : droper },
			hardy : {atk : neuter, atk : neuter },
			hasty : {spe : booster, def : droper },
			impish : {def : booster, spa : droper },
			jolly : {spe : booster, spa : droper },
			lax : {def : booster, spd : droper },
			lonely : {atk : booster, def : droper },
			mild : {spa : booster, def : droper },
			modest : {spa : booster, atk : droper },
			naive : {spe : booster, spd : droper },
			naughty : {atk : booster, spd : droper },
			quiet : {spa : booster, spe : droper },
			quirky : {spd : neuter, spd : neuter },
			rash : {spa : booster, spd : droper },
			relaxed : {def : booster, spe : droper },
			sassy : {spd : booster, spe : droper },
			serious : {spe : neuter, spe : neuter },
			timid : {spe : booster, atk : droper },
		}
		$(document).on("submit",selector,this.submit_stat_form);
	}
}

user_pokemons.initialize("#stats_form");