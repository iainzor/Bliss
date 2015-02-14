bliss.service("pages.Page", function() {
	var Page = function() {
		var reset = this.reset = function(pages, recursive) {
			angular.forEach(pages, function(page) {
				page.active = false;

				if (recursive === true) {
					reset(page.pages, true);
				}
			});
		};
		
		this.activate = function(pages, path) {
			var found = null;
			var self = this;
		
			angular.forEach(pages, function(page) {
				page.active = false;

				var r = new RegExp("^\/"+ page.path +"$", "i");
				if (r.test(path)) {
					found = page;
				}

				var foundSub = self.activate(page.pages, path);
				if (foundSub !== null) {
					page.active = true;
					found = foundSub;
				}
			});

			if (found) {
				found.active = true;
			}

			return found;
		};
		
		this.findParent = function(target, pages, previous) {
			var parent;
			var self = this;
			
			angular.forEach(pages, function(p) {
				if (p.id === target.id && previous) {
					parent = previous;
				} else if (p.pages && p.pages.length) {
					parent = self.findParent(target, p.pages, p);
				}
			});
			
			return parent;
		};
	};
	
	return new Page();
});