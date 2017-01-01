jQuery(function(){
(function ($){
	var searchInPlace = function(){
        if(jQuery.fn.on){
            $(document).on('mouseover mouseout', '.search-in-place>.item', function(){$(this).toggleClass('active');})
                       .on('mousedown', '.search-in-place>.item', function(){document.location = $(this).find('a').attr('href');})
                       .on('mousedown', '.search-in-place>.label.more', function(){document.location = $(this).find('a').attr('href');});
        }else{
            $('.search-in-place>.item').live('mouseover mouseout', function(){$(this).toggleClass('active');})
                                       .live('mousedown', function(){document.location = $(this).find('a').attr('href');})
                                       .live('mousedown', '.search-in-place>.label.more', function(){document.location = $(this).find('a').attr('href');});
        }
	};

	searchInPlace.prototype = {
		active : null,
		search : '',
		config:{
			min 		 : codepeople_search_in_place_advanced.char_number,
			image_width  : 50,
			image_height : 50,
			colors		 : ['#F4EFEC', '#B5DCE1', '#F4E0E9', '#D7E0B1', '#F4D9D0', '#D6CDC8', '#F4E3C9', '#CFDAF0'],
			areas		 : ['div.hentry', '#content', '#main', 'div.content', '#middle', '#container', '#wrapper']
		},
		
		autocomplete : function(){
			var me = this;
			$(("input[name='s']")).attr('autocomplete', 'off').bind('input keyup focus', 
				function(){
					var s = $(this),
						v = s.val();
					if(me.checkString(v)){
						setTimeout( function(){ me.getResults(s); }, 500)
					}else{
						if(me.search.indexOf(v) != 0){
							$('.search-in-place').hide();
						}
					}	
				}
			).blur(function(){
				setTimeout(function(){$('.search-in-place').hide();}, 150);
			});
		},
		
		checkString : function(v){
			return this.config.min <= v.length;
		},
		
		getResults : function(e){
			if(e.val() == this.search){
				$('.search-in-place').show();
				return;
			}	
				
			this.search = e.val();
			var me 	= this,
				o 	= e.offset(),
				p 	= {'s': me.search},
				s 	= $('<div class="search-in-place"></div>');
			
			// For wp_ajax
			p.action = "search_in_place";
			
			// Stop all search actions
			if(me.active) me.active.abort();
			
			// Remove results container inserted previously
			$('.search-in-place').remove();
			// Set the results container
			s.width(e.outerWidth()).css({'left' : o.left, 'top' : (parseInt(o.top) + e.outerHeight()+5)+'px'}).appendTo('body');
			me.displayLoading(s);

			me.active = jQuery.get( codepeople_search_in_place_advanced.root + 'admin-ajax.php', p, function(r){
				me.displayResult(r, s);
				me.removeLoading(r, s);
			}, "json");
		},
		
		displayResult : function(o, e){
			var me = this,
				s = '';
			
			for(var t in o){
				var item = o[t],
					l = o[t].items;
					
				if(item.label)
					s += '<div class="label">'+item.label+'</div>';
				
				for(var i=0, h = l.length; i < h; i++){
					s += '<div class="item">'; 
					if(l[i].thumbnail){ 
						s += '<div class="thumbnail"><img src="'+l[i].thumbnail+'" style="visibility:hidden;float:left;position:absolute;" /></div><div class="data" style="margin-left:'+(me.config.image_width+5)+'px;min-height:'+me.config.image_height+'px;">';
					}else{
						s += '<div class="data">';
					}	
					
					s += '<span class="title"><a href="'+l[i].link+'">'+l[i].title+'</a></span>';
					if(l[i].resume) s += '<span class="resume">'+l[i].resume+'</span>';
					if(l[i].author) s += '<span class="author">'+l[i].author+'</span>';
					if(l[i].date) s += '<span class="date">'+l[i].date+'</span>';
					s += '</div></div>';
				}
			}
			
			e.prepend(s).find('.thumbnail img').load(function(){
				var size = me.imgSize(this);
				$(this).width(size.w).height(size.h).css('visibility', 'visible');
			});
		},
		
		imgSize : function(e){
			e = $(e);
			
			var w = e.width(),
				h = e.height(),
				nw, nh;
			
			if(w > this.config.image_width){
				nw = this.config.image_width;
				nh = nw/w*h;
				w = nw; h = nh;
			}
			
			if(h > this.config.image_height){
				nh = this.config.image_height;
				nw = nh/h*w;
				w = nw; h = nh;
			}
			
			return {'w':w, 'h':h};
		},
		
		displayLoading : function(e){
			e.append('<div class="label"><div class="loading"></div></div>');
		},
		
		removeLoading : function(c, e){
            var home = codepeople_search_in_place_advanced.home;
            home += ( home.indexOf( '?' ) == -1 ) ? '?' : '&' ;
			var s = (typeof c.length != 'undefined') ? codepeople_search_in_place_advanced.empty : '<a href="'+home+'s='+this.search+'&submit=Search">'+codepeople_search_in_place_advanced.more+' &gt;</a>';
			e.find('.loading').parent().addClass('more').html(s);
			
		},
		
		highlightTerms : function(terms){
			var me = this, color;
			
			innerHighlight = function(text, node){
				var skip = 0;
				if(3 == node.nodeType) {
					var pattern = text.toUpperCase();
                    var nodeData = node.data.toUpperCase();
                    var patternIndex = nodeData.indexOf(pattern);

                    if (patternIndex >= 0) {
						replaceNodeContent(node, text, patternIndex);
						skip = 1;
					}
                }
                else if(possibleTextNode(node)) {
					lookupTextNodes(node, text);
                }
				return skip;
            };
			
			replaceNodeContent = function(node, text, patternIndex) {
                var markNode = document.createElement('mark');
                var startOfText = node.splitText(patternIndex);
                var endOfText = startOfText.splitText(text.length);
                var matchedText = startOfText.cloneNode(true);

                markNode.setAttribute('style', 'background-color:'+color);
                markNode.appendChild(matchedText);
                startOfText.parentNode.replaceChild(markNode, startOfText);
            };
			
			possibleTextNode = function(node) {
                return (1 == node.nodeType && node.childNodes && !/(script|style)/i.test(node.tagName));
            };
			
			lookupTextNodes = function(node, text) {
				for (var i=0; i<node.childNodes.length; i++) {
                    i += innerHighlight(text, node.childNodes[i]);
                }
            };
			
			
			var b = $('#content');
			if(b.length){
				$.each(terms, function(i, term){
					if(term.length >= codepeople_search_in_place_advanced.char_number){
						color = me.config.colors[i%me.config.colors.length];
						innerHighlight(term, b[0]);
					}	
				});
			}
		}
	};

	var	searchObj = new searchInPlace();
	
	if(((codepeople_search_in_place_advanced.highlight*1) || (codepeople_search_in_place_advanced.highlight_resulting_page*1))&& codepeople_search_in_place_advanced.terms && codepeople_search_in_place_advanced.terms.length > 0){
		searchObj.highlightTerms(codepeople_search_in_place_advanced.terms);
	}
	
	if((codepeople_search_in_place_advanced.identify_post_type)*1 && codepeople_search_in_place_advanced.post_types){
		var post_types = eval(codepeople_search_in_place_advanced.post_types);
		for(var i in post_types){
			if(post_types[i].name && post_types[i].label)
				$('.type-'+post_types[i].name).prepend('<div class="search-in-place-type">'+post_types[i].label+'</div>');
		}	
	}
	
	searchObj.autocomplete();
		
})(jQuery);
});