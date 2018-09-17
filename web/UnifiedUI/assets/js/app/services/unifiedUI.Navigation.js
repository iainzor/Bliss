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
		
		/**
		 * Set the resource for the page.  This includes the title of the resource and
		 * any sub-pages used for the navigation
		 * 
		 * @param string title Used as the page's title
		 * @param Object resource A resource object
		 */
		this.resource = function(title, resource) {
			var pages = resource.pages || [];
			var activePage = Page.find(pages, $location.path());
			var activePath = activePage ? activePage.path : resource.path || null;
			
			this.activate(activePath);
			this.activate(activePage);
			
			return App.page({
				title: title,
				pages: resource.pages || []
			});
		};
		
		this.find = function(path, firstMatch) {
			return Page.find(_pages, path, firstMatch);
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
			if (typeof(page) === "string") {
				page = this.find(page);
			}
			
			if (!page) {
				return;
			}
			
			var parent = Page.findParent(page, _pages);
			var found;

			while (parent !== null) {
				parent.active = true;
				found = Page.findParent(parent, _pages);

				if (!found) {
					break;
				} else {
					parent = found;
				}
			}
			
			page.active = true;
		};
		
		this.reset = function() {
			Page.reset(_pages, true);
		};
	};
	
	return new Navigation();
}]);