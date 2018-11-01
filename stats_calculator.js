var stats_calculator = {
	getform : function() {
		
		this.pk.states_base = {
			hp:parseInt(this.$form_stats_vars.find("input[name='statbase']").val()),
			atk:parseInt(this.$form_stats_vars.find("input[name='statbase']").val()),
			def:parseInt(this.$form_stats_vars.find("input[name='statbase']").val()),
			spatk:parseInt(this.$form_stats_vars.find("input[name='statbase']").val()),
			spdef:parseInt(this.$form_stats_vars.find("input[name='statbase']").val()),
			spe:parseInt(this.$form_stats_vars.find("input[name='statbase']").val())
		};
		this.pk.eps = {
			hp:parseInt(this.$form_stats_vars.find("input[name='ep']").val()),
			atk:parseInt(this.$form_stats_vars.find("input[name='ep']").val()),
			def:parseInt(this.$form_stats_vars.find("input[name='ep']").val()),
			spatk:parseInt(this.$form_stats_vars.find("input[name='ep']").val()),
			spdef:parseInt(this.$form_stats_vars.find("input[name='ep']").val()),
			spe:parseInt(this.$form_stats_vars.find("input[name='ep']").val())
		};
		this.pk.ivs = {
			hp:parseInt(this.$form_stats_vars.find("input[name='iv']").val()),
			atk:parseInt(this.$form_stats_vars.find("input[name='iv']").val()),
			def:parseInt(this.$form_stats_vars.find("input[name='iv']").val()),
			spatk:parseInt(this.$form_stats_vars.find("input[name='iv']").val()),
			spdef:parseInt(this.$form_stats_vars.find("input[name='iv']").val()),
			spe:parseInt(this.$form_stats_vars.find("input[name='iv']").val())
		};
		this.pk.level = parseInt(this.$form_stats_vars.find("input[name='level']").val());
		this.pk.pv = parseFloat(this.$form_stats_vars.find("input[name='pv']").val());
		this.statetoset = this.$form_stats_vars.find("select[name='statetoset']").val();
	},
	calculate : function () {

		if ( this.statetoset == 'hp') {
			this.pk.states[this.statetoset] = Math.round( 10 + ( (this.pk.level/100) * ( ( (this.pk.states_base[this.statetoset]*2) + this.pk.ivs[this.statetoset] + (this.pk.eps[this.statetoset]/4) ) ) ) + this.pk.level );
		} else {
			this.pk.states[this.statetoset] = Math.round(5 + ( (this.pk.level/100) * ( ( (this.pk.states_base[this.statetoset]*2) + this.pk.ivs[this.statetoset] + (this.pk.eps[this.statetoset]/4) ) ) ) * this.pk.pv);
		}
	},
	exe_calculator_form: function(e){
		e.preventDefault();
		
		stats_calculator.getform();
		stats_calculator.calculate();
		console.log(stats_calculator.pk.states);
	},
	initialize: function (form_selector = "from") {

		this.pk = {
			id_pk: 0,
			states : {hp:0,atk:0,def:0,spatk:0,spdef:0,spe:0}
		}
		this.$form_stats_vars = $(form_selector);
		$(document).on('submit',form_selector,this.exe_calculator_form);
	}
}
//$(document).on('submit','#form_stats_vars',function() {});
stats_calculator.initialize('#form_stats_vars');