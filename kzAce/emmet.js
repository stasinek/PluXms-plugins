!function(e){var val=e(); if("object"==typeof exports&&"undefined"!=typeof module)module.exports=val;if("function"==typeof define&&define.amd)define("emmet",[],val);{var f;"undefined"!=typeof window?f=window:"undefined"!=typeof global?f=global:"undefined"!=typeof self&&(f=self),f.emmet=val}}(function(){var define,module,exports;return (function outer (modules, cache, entry) {
    // Save the require from previous bundle to this closure if any
    var previousRequire = typeof require == "function" && require;

    function newRequire(name, jumped){
        if(!cache[name]) {
            if(!modules[name]) {
                // if we cannot find the the module within our internal map or
                // cache jump to the current global require ie. the last bundle
                // that was added to the page.
                var currentRequire = typeof require == "function" && require;
                if (!jumped && currentRequire) return currentRequire(name, true);

                // If there are other bundles on this page the require from the
                // previous one is saved to 'previousRequire'. Repeat this as
                // many times as there are bundles until the module is found or
                // we exhaust the require chain.
                if (previousRequire) return previousRequire(name, true);
                var err = new Error('Cannot find module \'' + name + '\'');
                err.code = 'MODULE_NOT_FOUND';
                throw err;
            }
            var m = cache[name] = {exports:{}};
            modules[name][0].call(m.exports, function(x){
                var id = modules[name][1][x];
                return newRequire(id ? id : x);
            },m,m.exports,outer,modules,cache,entry);
        }
        return cache[name].exports;
    }
    for(var i=0;i<entry.length;i++) newRequire(entry[i]);

    // Override the current require with this new one
    return newRequire;
})
({"./bundles/snippets.js":[function(require,module,exports){
/**
 * Bundler, used in builder script to statically
 * include snippets.json into bundle
 */
var res = require('../lib/assets/resources');
var snippets = require('../lib/snippets.json');
res.setVocabulary(snippets, 'system');

},{"../lib/assets/resources":"assets\\resources.js","../lib/snippets.json":"snippets.json"}],"./lib/emmet.js":[function(require,module,exports){
if (typeof module === 'object' && typeof define !== 'function') {
	var define = function (factory) {
		module.exports = factory(require, exports, module);
	};
}

define(function(require, exports, module) {
	var global = typeof self != 'undefined' ? self : this;

	var utils = require('./utils/common');
	var actions = require('./action/main');
	var parser = require('./parser/abbreviation');
	var file = require('./plugin/file');

	var preferences = require('./assets/preferences');
	var resources = require('./assets/resources');
	var profile = require('./assets/profile');
	var ciu = require('./assets/caniuse');
	var logger = require('./assets/logger');

	var sliceFn = Array.prototype.slice;

	/**
	 * Returns file name part from path
	 * @param {String} path Path to file
	 * @return {String}
	 */
	function getFileName(path) {
		var re = /([\w\.\-]+)$/i;
		var m = re.exec(path);
		return m ? m[1] : '';
	}

	/**
	 * Normalizes profile definition: converts some
	 * properties to valid data types
	 * @param {Object} profile
	 * @return {Object}
	 */
	function normalizeProfile(profile) {
		if (typeof profile === 'object') {
			if ('indent' in profile) {
				profile.indent = !!profile.indent;
			}

			if ('self_closing_tag' in profile) {
				if (typeof profile.self_closing_tag === 'number') {
					profile.self_closing_tag = !!profile.self_closing_tag;
				}
			}
		}

		return profile;
	}

	return {
		/**
		 * The essential function that expands Emmet abbreviation
		 * @param {String} abbr Abbreviation to parse
		 * @param {String} syntax Abbreviation's context syntax
		 * @param {String} profile Output profile (or its name)
		 * @param {Object} contextNode Contextual node where abbreviation is
		 * written
		 * @return {String}
		 */
		expandAbbreviation: function(abbr, syntax, profile, contextNode) {
			return parser.expand(abbr, {
				syntax: syntax,
				profile: profile,
				contextNode: contextNode
			});
		},

		/**
		 * Runs given action
		 * @param  {String} name Action name
		 * @param  {IEmmetEditor} editor Editor instance
		 * @return {Boolean} Returns true if action was performed successfully
		 */
		run: function(name) {
			return actions.run.apply(actions, sliceFn.call(arguments, 0));
		},

		/**
		 * Loads Emmet extensions. Extensions are simple .js files that
		 * uses Emmet modules and resources to create new actions, modify
		 * existing ones etc.
		 * @param {Array} fileList List of absolute paths to files in extensions
		 * folder. Back-end app should not filter this list (e.g. by extension)
		 * but return it "as-is" so bootstrap can decide how to load contents
		 * of each file.
		 * This method requires a <code>file</code> module of <code>IEmmetFile</code>
		 * interface to be implemented.
		 * @memberOf bootstrap
		 */
		loadExtensions: function(fileList) {
			var payload = {};
			var userSnippets = null;
			var that = this;

			// make sure file list contians only valid extension files
			fileList = fileList.filter(function(f) {
				var ext = file.getExt(f);
				return ext === 'json' || ext === 'js';
			});

			var reader = (file.readText || file.read).bind(file);
			var next = function() {
				if (fileList.length) {
					var f = fileList.shift();
					reader(f, function(err, content) {
						if (err) {
							logger.log('Unable to read "' + f + '" file: '+ err);
							return next();
						}

						switch (file.getExt(f)) {
							case 'js':
								try {
									eval(content);
								} catch (e) {
									logger.log('Unable to eval "' + f + '" file: '+ e);
								}
								break;
							case 'json':
								var fileName = getFileName(f).toLowerCase().replace(/\.json$/, '');
								content = utils.parseJSON(content);
								if (/^snippets/.test(fileName)) {
									if (fileName === 'snippets') {
										// data in snippets.json is more important to user
										userSnippets = content;
									} else {
										payload.snippets = utils.deepMerge(payload.snippets || {}, content);
									}
								} else {
									payload[fileName] = content;
								}

								break;
						}

						next();
					});
				} else {
					// complete
					if (userSnippets) {
						payload.snippets = utils.deepMerge(payload.snippets || {}, userSnippets);
					}

					that.loadUserData(payload);
				}
			};

			next();
		},

		/**
		 * Loads preferences from JSON object (or string representation of JSON)
		 * @param {Object} data
		 * @returns
		 */
		loadPreferences: function(data) {
			preferences.load(utils.parseJSON(data));
		},

		/**
		 * Loads user snippets and abbreviations. It doesn’t replace current
		 * user resource vocabulary but merges it with passed one. If you need
		 * to <i>replaces</i> user snippets you should call
		 * <code>resetSnippets()</code> method first
		 */
		loadSnippets: function(data) {
			data = utils.parseJSON(data);

			var userData = resources.getVocabulary('user') || {};
			resources.setVocabulary(utils.deepMerge(userData, data), 'user');
		},

		/**
		 * Helper function that loads default snippets, defined in project’s
		 * <i>snippets.json</i>
		 * @param {Object} data
		 */
		loadSystemSnippets: function(data) {
			resources.setVocabulary(utils.parseJSON(data), 'system');
		},

		/**
		 * Helper function that loads Can I Use database
		 * @param {Object} data
		 */
		loadCIU: function(data) {
			ciu.load(utils.parseJSON(data));
		},

		/**
		 * Removes all user-defined snippets
		 */
		resetSnippets: function() {
			resources.setVocabulary({}, 'user');
		},

		/**
		 * Helper function that loads all user data (snippets and preferences)
		 * defined as a single JSON object. This is useful for loading data
		 * stored in a common storage, for example <code>NSUserDefaults</code>
		 * @param {Object} data
		 */
		loadUserData: function(data) {
			data = utils.parseJSON(data);
			if (data.snippets) {
				this.loadSnippets(data.snippets);
			}

			if (data.preferences) {
				this.loadPreferences(data.preferences);
			}

			if (data.profiles) {
				this.loadProfiles(data.profiles);
			}

			if (data.caniuse) {
				this.loadCIU(data.caniuse);
			}

			var profiles = data.syntaxProfiles || data.syntaxprofiles;
			if (profiles) {
				this.loadSyntaxProfiles(profiles);
			}
		},

		/**
		 * Resets all user-defined data: preferences, snippets etc.
		 * @returns
		 */
		resetUserData: function() {
			this.resetSnippets();
			preferences.reset();
			profile.reset();
		},

		/**
		 * Load syntax-specific output profiles. These are essentially
		 * an extension to syntax snippets
		 * @param {Object} profiles Dictionary of profiles
		 */
		loadSyntaxProfiles: function(profiles) {
			profiles = utils.parseJSON(profiles);
			var snippets = {};
			Object.keys(profiles).forEach(function(syntax) {
				var options = profiles[syntax];
				if (!(syntax in snippets)) {
					snippets[syntax] = {};
				}
				snippets[syntax].profile = normalizeProfile(options);
			});

			this.loadSnippets(snippets);
		},

		/**
		 * Load named profiles
		 * @param {Object} profiles
		 */
		loadProfiles: function(profiles) {
			profiles = utils.parseJSON(profiles);
			Object.keys(profiles).forEach(function(name) {
				profile.create(name, normalizeProfile(profiles[name]));
			});
		},

		// expose some useful data for plugin authors
		actions: actions,
		parser: parser,
		file: file,
		preferences: preferences,
		resources: resources,
		profile: profile,
		tabStops: require('./assets/tabStops'),
		htmlMatcher: require('./assets/htmlMatcher'),
		utils: {
			common: utils,
			action: require('./utils/action'),
			editor: require('./utils/editor')
		}
	};
});

},{"./action/main":"action\\main.js","./assets/caniuse":"assets\\caniuse.js","./assets/htmlMatcher":"assets\\htmlMatcher.js","./assets/logger":"assets\\logger.js","./assets/preferences":"assets\\preferences.js","./assets/profile":"assets\\profile.js","./assets/resources":"assets\\resources.js","./assets/tabStops":"assets\\tabStops.js","./parser/abbreviation":"parser\\abbreviation.js","./plugin/file":"plugin\\file.js","./utils/action":"utils\\action.js","./utils/common":"utils\\common.js","./utils/editor":"utils\\editor.js"}],"action\\balance.js":[function(require,module,exports){
/**
 * HTML pair matching (balancing) actions
 * @constructor
 */
if (typeof module === 'object' && typeof define !== 'function') {
	var define = function (factory) {
		module.exports = factory(require, exports, module);
	};
}

define(function(require, exports, module) {
	var htmlMatcher = require('../assets/htmlMatcher');
	var utils = require('../utils/common');
	var editorUtils = require('../utils/editor');
	var actionUtils = require('../utils/action');
	var range = require('../assets/range');
	var cssEditTree = require('../editTree/css');
	var cssSections = require('../utils/cssSections');
	var lastMatch = null;

	function last(arr) {
		return arr[arr.length - 1];
	}

	function balanceHTML(editor, direction) {
		var info = editorUtils.outputInfo(editor);
		var content = info.content;
		var sel = range(editor.getSelectionRange());
		
		// validate previous match
		if (lastMatch && !lastMatch.range.equal(sel)) {
			lastMatch = null;
		}
		
		if (lastMatch && sel.length()) {
			if (direction == 'in') {
				// user has previously selected tag and wants to move inward
				if (lastMatch.type == 'tag' && !lastMatch.close) {
					// unary tag was selected, can't move inward
					return false;
				} else {
					if (lastMatch.range.equal(lastMatch.outerRange)) {
						lastMatch.range = lastMatch.innerRange;
					} else {
						var narrowed = utils.narrowToNonSpace(content, lastMatch.innerRange);
						lastMatch = htmlMatcher.find(content, narrowed.start + 1);
						if (lastMatch && lastMatch.range.equal(sel) && lastMatch.outerRange.equal(sel)) {
							lastMatch.range = lastMatch.innerRange;
						}
					}
				}
			} else {
				if (
					!lastMatch.innerRange.equal(lastMatch.outerRange) 
					&& lastMatch.range.equal(lastMatch.innerRange) 
					&& sel.equal(lastMatch.range)) {
					lastMatch.range = lastMatch.outerRange;
				} else {
					lastMatch = htmlMatcher.find(content, sel.start);
					if (lastMatch && lastMatch.range.equal(sel) && lastMatch.innerRange.equal(sel)) {
						lastMatch.range = lastMatch.outerRange;
					}
				}
			}
		} else {
			lastMatch = htmlMatcher.find(content, sel.start);
		}

		if (lastMatch) {
			if (lastMatch.innerRange.equal(sel)) {
				lastMatch.range = lastMatch.outerRange;
			}

			if (!lastMatch.range.equal(sel)) {
				editor.createSelection(lastMatch.range.start, lastMatch.range.end);
				return true;
			}
		}
		
		lastMatch = null;
		return false;
	}

	function rangesForCSSRule(rule, pos) {
		// find all possible ranges
		var ranges = [rule.range(true)];

		// braces content
		ranges.push(rule.valueRange(true));

		// find nested sections
		var nestedSections = cssSections.nestedSectionsInRule(rule);

		// real content, e.g. from first property name to
		// last property value
		var items = rule.list();
		if (items.length || nestedSections.length) {
			var start = Number.POSITIVE_INFINITY, end = -1;
			if (items.length) {
				start = items[0].namePosition(true);
				end = last(items).range(true).end;
			}

			if (nestedSections.length) {
				if (nestedSections[0].start < start) {
					start = nestedSections[0].start;
				}

				if (last(nestedSections).end > end) {
					end = last(nestedSections).end;
				}
			}

			ranges.push(range.create2(start, end));
		}

		ranges = ranges.concat(nestedSections);

		var prop = cssEditTree.propertyFromPosition(rule, pos) || items[0];
		if (prop) {
			ranges.push(prop.range(true));
			var valueRange = prop.valueRange(true);
			if (!prop.end()) {
				valueRange._unterminated = true;
			}
			ranges.push(valueRange);
		}

		return ranges;
	}

	/**
	 * Returns all possible selection ranges for given caret position
	 * @param  {String} content CSS content
	 * @param  {Number} pos     Caret position(where to start searching)
	 * @return {Array}
	 */
	function getCSSRanges(content, pos) {
		var rule;
		if (typeof content === 'string') {
			var ruleRange = cssSections.matchEnclosingRule(content, pos);
			if (ruleRange) {
				rule = cssEditTree.parse(ruleRange.substring(content), {
					offset: ruleRange.start
				});
			}
		} else {
			// passed parsed CSS rule
			rule = content;
		}

		if (!rule) {
			return null;
		}

		// find all possible ranges
		var ranges = rangesForCSSRule(rule, pos);

		// remove empty ranges
		ranges = ranges.filter(function(item) {
			return !!item.length;
		});

		return utils.unique(ranges, function(item) {
			return item.valueOf();
		});
	}

	function balanceCSS(editor, direction) {
		var info = editorUtils.outputInfo(editor);
		var content = info.content;
		var sel = range(editor.getSelectionRange());

		var ranges = getCSSRanges(info.content, sel.start);
		if (!ranges && sel.length()) {
			// possible reason: user has already selected
			// CSS rule from last match
			try {
				var rule = cssEditTree.parse(sel.substring(info.content), {
					offset: sel.start
				});
				ranges = getCSSRanges(rule, sel.start);
			} catch(e) {}
		}

		if (!ranges) {
			return false;
		}

		ranges = range.sort(ranges, true);

		// edge case: find match that equals current selection,
		// in case if user moves inward after selecting full CSS rule
		var bestMatch = utils.find(ranges, function(r) {
			return r.equal(sel);
		});

		if (!bestMatch) {
			bestMatch = utils.find(ranges, function(r) {
				// Check for edge case: caret right after CSS value
				// but it doesn‘t contains terminating semicolon.
				// In this case we have to check full value range
				return r._unterminated ? r.include(sel.start) : r.inside(sel.start);
			});
		}

		if (!bestMatch) {
			return false;
		}

		// if best match equals to current selection, move index
		// one position up or down, depending on direction
		var bestMatchIx = ranges.indexOf(bestMatch);
		if (bestMatch.equal(sel)) {
			bestMatchIx += direction == 'out' ? 1 : -1;
		}

		if (bestMatchIx < 0 || bestMatchIx >= ranges.length) {
			if (bestMatchIx >= ranges.length && direction == 'out') {
				pos = bestMatch.start - 1;

				var outerRanges = getCSSRanges(content, pos);
				if (outerRanges) {
					bestMatch = last(outerRanges.filter(function(r) {
						return r.inside(pos);
					}));
				}
			} else if (bestMatchIx < 0 && direction == 'in') {
				bestMatch = null;
			} else {
				bestMatch = null;
			}
		} else {
			bestMatch = ranges[bestMatchIx];	
		}

		if (bestMatch) {
			editor.createSelection(bestMatch.start, bestMatch.end);
			return true;
		}
		
		return false;
	}
	
	return {
		/**
		 * Find and select HTML tag pair
		 * @param {IEmmetEditor} editor Editor instance
		 * @param {String} direction Direction of pair matching: 'in' or 'out'. 
		 * Default is 'out'
		 */
		balance: function(editor, direction) {
			direction = String((direction || 'out').toLowerCase());
			var info = editorUtils.outputInfo(editor);
			if (actionUtils.isSupportedCSS(info.syntax)) {
				return balanceCSS(editor, direction);
			}
			
			return balanceHTML(editor, direction);
		},

		balanceInwardAction: function(editor) {
			return this.balance(editor, 'in');
		},

		balanceOutwardAction: function(editor) {
			return this.balance(editor, 'out');	
		},

		/**
		 * Moves caret to matching opening or closing tag
		 * @param {IEmmetEditor} editor
		 */
		goToMatchingPairAction: function(editor) {
			var content = String(editor.getContent());
			var caretPos = editor.getCaretPos();
			
			if (content.charAt(caretPos) == '<') 
				// looks like caret is outside of tag pair  
				caretPos++;
				
			var tag = htmlMatcher.tag(content, caretPos);
			if (tag && tag.close) { // exclude unary tags
				if (tag.open.range.inside(caretPos)) {
					editor.setCaretPos(tag.close.range.start);
				} else {
					editor.setCaretPos(tag.open.range.start);
				}
				
				return true;
			}
			
			return false;
		}
	};
});
},{"../assets/htmlMatcher":"assets\\htmlMatcher.js","../assets/range":"assets\\range.js","../editTree/css":"editTree\\css.js","../utils/action":"utils\\action.js","../utils/common":"utils\\common.js","../utils/cssSections":"utils\\cssSections.js","../utils/editor":"utils\\editor.js"}],"action\\base64.js":[function(require,module,exports){
/**
 * Encodes/decodes image under cursor to/from base64
 * @param {IEmmetEditor} editor
 */
if (typeof module === 'object' && typeof define !== 'function') {
	var define = function (factory) {
		module.exports = factory(require, exports, module);
	};
}

define(function(require, exports, module) {
	var file = require('../plugin/file');
	var base64 = require('../utils/base64');
	var actionUtils = require('../utils/action');
	var editorUtils = require('../utils/editor');

	/**
	 * Test if <code>text</code> starts with <code>token</code> at <code>pos</code>
	 * position. If <code>pos</code> is omitted, search from beginning of text 
	 * @param {String} token Token to test
	 * @param {String} text Where to search
	 * @param {Number} pos Position where to start search
	 * @return {Boolean}
	 * @since 0.65
	 */
	function startsWith(token, text, pos) {
		pos = pos || 0;
		return text.charAt(pos) == token.charAt(0) && text.substr(pos, token.length) == token;
	}

	/**
	 * Encodes image to base64
	 * 
	 * @param {IEmmetEditor} editor
	 * @param {String} imgPath Path to image
	 * @param {Number} pos Caret position where image is located in the editor
	 * @return {Boolean}
	 */
	function encodeToBase64(editor, imgPath, pos) {
		var editorFile = editor.getFilePath();
		var defaultMimeType = 'application/octet-stream';

		if (editorFile === null) {
			throw "You should save your file before using this action";
		}

		// locate real image path
		file.locateFile(editorFile, imgPath, function(realImgPath) {
			if (realImgPath === null) {
				throw "Can't find " + imgPath + ' file';
			}

			file.read(realImgPath, function(err, content) {
				if (err) {
					throw 'Unable to read ' + realImgPath + ': ' + err;
				}

				var b64 = base64.encode(String(content));
				if (!b64) {
					throw "Can't encode file content to base64";
				}

				b64 = 'data:' + (actionUtils.mimeTypes[String(file.getExt(realImgPath))] || defaultMimeType) +
					';base64,' + b64;

				editor.replaceContent('$0' + b64, pos, pos + imgPath.length);
			});
		});

		return true;
	}

	/**
	 * Decodes base64 string back to file.
	 * @param {IEmmetEditor} editor
	 * @param {String} filePath to new image
	 * @param {String} data Base64-encoded file content
	 * @param {Number} pos Caret position where image is located in the editor
	 */
	function decodeFromBase64(editor, filePath, data, pos) {
		// ask user to enter path to file
		filePath = filePath || String(editor.prompt('Enter path to file (absolute or relative)'));
		if (!filePath) {
			return false;
		}

		var editorFile = editor.getFilePath();
		file.createPath(editorFile, filePath, function(err, absPath) {
			if (err || !absPath) {
				throw "Can't save file";
			}

			var content = data.replace(/^data\:.+?;.+?,/, '');
			file.save(absPath, base64.decode(content), function(err) {
				if (err) {
					throw 'Unable to save ' + absPath + ': ' + err;
				}

				editor.replaceContent('$0' + filePath, pos, pos + data.length);
			});
		});

		return true;
	}

	return {
		/**
		 * Action to encode or decode file to data:url
		 * @param  {IEmmetEditor} editor  Editor instance
		 * @param  {String} syntax  Current document syntax
		 * @param  {String} profile Output profile name
		 * @return {Boolean}
		 */
		encodeDecodeDataUrlAction: function(editor, filepath) {
			var data = String(editor.getSelection());
			var caretPos = editor.getCaretPos();
			var info = editorUtils.outputInfo(editor);

			if (!data) {
				// no selection, try to find image bounds from current caret position
				var text = info.content, m;
				while (caretPos-- >= 0) {
					if (startsWith('src=', text, caretPos)) { // found <img src="">
						if ((m = text.substr(caretPos).match(/^(src=(["'])?)([^'"<>\s]+)\1?/))) {
							data = m[3];
							caretPos += m[1].length;
						}
						break;
					} else if (startsWith('url(', text, caretPos)) { // found CSS url() pattern
						if ((m = text.substr(caretPos).match(/^(url\((['"])?)([^'"\)\s]+)\1?/))) {
							data = m[3];
							caretPos += m[1].length;
						}
						break;
					}
				}
			}

			if (data) {
				if (startsWith('data:', data)) {
					return decodeFromBase64(editor, filepath, data, caretPos);
				} else {
					return encodeToBase64(editor, data, caretPos);
				}
			}

			return false;
		}
	};
});

},{"../plugin/file":"plugin\\file.js","../utils/action":"utils\\action.js","../utils/base64":"utils\\base64.js","../utils/editor":"utils\\editor.js"}],"action\\editPoints.js":[function(require,module,exports){
/**
 * Move between next/prev edit points. 'Edit points' are places between tags 
 * and quotes of empty attributes in html
 */
if (typeof module === 'object' && typeof define !== 'function') {
	var define = function (factory) {
		module.exports = factory(require, exports, module);
	};
}

define(function(require, exports, module) {
	/**
	 * Search for new caret insertion point
	 * @param {IEmmetEditor} editor Editor instance
	 * @param {Number} inc Search increment: -1 — search left, 1 — search right
	 * @param {Number} offset Initial offset relative to current caret position
	 * @return {Number} Returns -1 if insertion point wasn't found
	 */
	function findNewEditPoint(editor, inc, offset) {
		inc = inc || 1;
		offset = offset || 0;
		
		var curPoint = editor.getCaretPos() + offset;
		var content = String(editor.getContent());
		var maxLen = content.length;
		var nextPoint = -1;
		var reEmptyLine = /^\s+$/;
		
		function getLine(ix) {
			var start = ix;
			while (start >= 0) {
				var c = content.charAt(start);
				if (c == '\n' || c == '\r')
					break;
				start--;
			}
			
			return content.substring(start, ix);
		}
			
		while (curPoint <= maxLen && curPoint >= 0) {
			curPoint += inc;
			var curChar = content.charAt(curPoint);
			var nextChar = content.charAt(curPoint + 1);
			var prevChar = content.charAt(curPoint - 1);
				
			switch (curChar) {
				case '"':
				case '\'':
					if (nextChar == curChar && prevChar == '=') {
						// empty attribute
						nextPoint = curPoint + 1;
					}
					break;
				case '>':
					if (nextChar == '<') {
						// between tags
						nextPoint = curPoint + 1;
					}
					break;
				case '\n':
				case '\r':
					// empty line
					if (reEmptyLine.test(getLine(curPoint - 1))) {
						nextPoint = curPoint;
					}
					break;
			}
			
			if (nextPoint != -1)
				break;
		}
		
		return nextPoint;
	}
	
	return {
		/**
		 * Move to previous edit point
		 * @param  {IEmmetEditor} editor  Editor instance
		 * @param  {String} syntax  Current document syntax
		 * @param  {String} profile Output profile name
		 * @return {Boolean}
		 */
		previousEditPointAction: function(editor, syntax, profile) {
			var curPos = editor.getCaretPos();
			var newPoint = findNewEditPoint(editor, -1);
				
			if (newPoint == curPos)
				// we're still in the same point, try searching from the other place
				newPoint = findNewEditPoint(editor, -1, -2);
			
			if (newPoint != -1) {
				editor.setCaretPos(newPoint);
				return true;
			}
			
			return false;
		},

		/**
		 * Move to next edit point
		 * @param  {IEmmetEditor} editor  Editor instance
		 * @param  {String} syntax  Current document syntax
		 * @param  {String} profile Output profile name
		 * @return {Boolean}
		 */
		nextEditPointAction: function(editor, syntax, profile) {
			var newPoint = findNewEditPoint(editor, 1);
			if (newPoint != -1) {
				editor.setCaretPos(newPoint);
				return true;
			}
			
			return false;
		}
	};
});
},{}],"action\\evaluateMath.js":[function(require,module,exports){
/**
 * Evaluates simple math expression under caret
 */
if (typeof module === 'object' && typeof define !== 'function') {
	var define = function (factory) {
		module.exports = factory(require, exports, module);
	};
}

define(function(require, exports, module) {
	var actionUtils = require('../utils/action');
	var utils = require('../utils/common');
	var math = require('../utils/math');
	var range = require('../assets/range');

	return {
		/**
		 * Evaluates math expression under the caret
		 * @param  {IEmmetEditor} editor
		 * @return {Boolean}
		 */
		evaluateMathAction: function(editor) {
			var content = editor.getContent();
			var chars = '.+-*/\\';
			
			/** @type Range */
			var sel = range(editor.getSelectionRange());
			if (!sel.length()) {
				sel = actionUtils.findExpressionBounds(editor, function(ch) {
					return utils.isNumeric(ch) || chars.indexOf(ch) != -1;
				});
			}
			
			if (sel && sel.length()) {
				var expr = sel.substring(content);
				
				// replace integral division: 11\2 => Math.round(11/2) 
				expr = expr.replace(/([\d\.\-]+)\\([\d\.\-]+)/g, 'round($1/$2)');
				
				try {
					var result = utils.prettifyNumber(math.evaluate(expr));
					editor.replaceContent(result, sel.start, sel.end);
					editor.setCaretPos(sel.start + result.length);
					return true;
				} catch (e) {}
			}
			
			return false;
		}
	};
});

},{"../assets/range":"assets\\range.js","../utils/action":"utils\\action.js","../utils/common":"utils\\common.js","../utils/math":"utils\\math.js"}],"action\\expandAbbreviation.js":[function(require,module,exports){
/**
 * 'Expand abbreviation' editor action: extracts abbreviation from current caret 
 * position and replaces it with formatted output. 
 * <br><br>
 * This behavio