/*
	--------------------------------
	Infinite Scroll Behavior
	Manual / Twitter-style
	--------------------------------
	+ https://github.com/paulirish/infinitescroll/
	+ version 2.0b2.110617
	+ Copyright 2011 Paul Irish & Luke Shumard
	+ Licensed under the MIT license
	
	+ Documentation: http://infinite-scroll.com/
	
*/
jQuery.extend(jQuery.infinitescroll.prototype,{

	_loadcallback_twitter: function infscr_loadcallback_twitter(box,data) {
            var opts = this.options,
	    		callback = this.options.callback, // GLOBAL OBJECT FOR CALLBACK
	    		result = (opts.state.isDone) ? 'done' : (!opts.appendCallback) ? 'no-append' : 'append',
	    		frag;
		switch (result) {
  
		case 'done':
  
			instance._showdonemsg();
			return false;
  
			break;
  
		case 'no-append':
  
			if (opts.dataType == 'html') {
				data = '<div>' + data + '</div>';
				data = $(data).find(opts.itemSelector);
			};
  
			break;
  
		case 'append':
			var children = box.children();
  			
			// if it didn't return anything
			if (children.length == 0) {
				return this._error('end');
			}
  			
  			
			// use a documentFragment because it works when content is going into a table or UL
			frag = document.createDocumentFragment();
			while (box[0].firstChild) {
				frag.appendChild(box[0].firstChild);
			}
			this._debug('contentSelector', jQuery(opts.contentSelector)[0]);
			jQuery(opts.itemSelector).filter(":last").after(frag);
			// previously, we would pass in the new DOM element as context for the callback
			// however we're now using a documentfragment, which doesnt havent parents or children,
			// so the context is the contentContainer guy, and we pass in an array
			//   of the elements collected as the first argument.
			data = children.get();
			break;
  
	}
  
	// loadingEnd function
	opts.loading.finished.call(jQuery(opts.contentSelector)[0],opts)
	
  
	// smooth scroll to ease in the new content
	if (opts.animate) {
		var scrollTo = jQuery(window).scrollTop() + jQuery('#infscr-loading').height() + opts.extraScrollPx + 'px';
		jQuery('html,body').animate({ scrollTop: scrollTo }, 800, function () { opts.state.isDuringAjax = false; });
	}
  
	if (!opts.animate) opts.state.isDuringAjax = false; // once the call is done, we can allow it again.
  
	callback(instance,data);
	},
	_setup_twitter: function infscr_setup_twitter () {
		var opts = this.options,
			instance = this;
			
		// Bind nextSelector link to retrieve
		jQuery(opts.nextSelector).click(function(e) {
			if (e.which == 1 && !e.metaKey && !e.shiftKey) {
				e.preventDefault();
				instance.retrieve();
			}
		});
		
		// Define loadingStart to never hide pager
		instance.options.loading.start = function (opts) {
			opts.loading.msg
				.appendTo(opts.loading.selector)
				.show(opts.loading.speed, function () {
                	beginAjax(opts);
            });
		}
	}
	
});