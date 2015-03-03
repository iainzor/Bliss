bliss.service("unifiedUI.Menu", function() {
	var Menu = function() {
		this.enabled = true;
		
		this.enable = function() {
			this.enabled = true;
		};
		
		this.disable = function() {
			this.enabled = false;
		};
	};
	
	return function() {
		return new Menu();
	};
});