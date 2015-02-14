bliss.service("unifiedUI.Layout", ["unifiedUI.Menu", function(Menu) {
	var Layout = function() {
		this.mode = "fluid";
		this.menu = Menu();
		
		/**
		 * @param Object config
		 */
		this.setConfig = function(config) {
			var name, value;
			for (name in config) {
				value = config[name];
				this[name] = value;
			}
		};
		
		/**
		 * Change the layout mode
		 */
		this.shrink = function() { this.mode = "thin"; };
		this.grow = function() { this.mode = "fluid"; };
		
		/**
		 * Reset the layout
		 */
		this.reset = function() {
			this.grow();
			this.menu.enable();
		};
	};
	
	return new Layout();
}]);