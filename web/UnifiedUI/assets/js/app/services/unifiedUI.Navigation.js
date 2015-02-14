bliss.service("unifiedUI.Navigation", ["$location", "bliss.App", "pages.Page", function($location, App, Page) {
	var Navigation = function() {
		var _pages = [];
		
		this.pages = function(pages) {
			if (typeof(pages) !== "undefined") {
				_pages = pages;
			}
			return _pages;
		};
		
		this.page = function(page) {
			this.activate(page);
			
			return App.page(page);
		};
		
		this.find = function(path) {
			var page = Page.activate(this.pages(), path);
			return page;
		};
		
		this.findById = function(id, pages) {
			pages = pages || this.pages();
			
			var self = this;
			var found;
			angular.forEach(pages, function(page) {
				if (page.id === id) {
					found = page;
				}
				
				if (!found && page.pages) {
					found = self.findById(id, page.pages);
				}
			});
			return found;
		};
		
		this.activate = function(page) {
			if (!page) {
				return;
			}
			
			var matcher = new RegExp($location.path() +"$", "i");
			if (page.path && matcher.test(page.path)) {
				page.active = true;
			}
			if (page.pages) {
				var self = this;
				angular.forEach(page.pages, function(page) { self.activate(page); });
			}
		};
		
		this.reset = function() {
			Page.reset(this.pages(), true);
		};
	};
	
	return new Navigation();
}]);