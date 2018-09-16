/*Script original de Julien Royer

*/ if (document.getElementById && document.createTextNode) {

	(function() {

		function init() { if (!init.done) { init.done = true; var spans =
			document.getElementsByTagName("span"), m = []; for (var i = 0, span; (span = spans[ i]); ++i) {
				if (belongsToClass(span, classNames.email)) { m[m.length] = span; } }

				for (i = 0; (span = m[ i]); ++i) { initSpan(span); }

			}

		}

		function initSpan(span) {

			var p, u, h, d, n;

			var spans = span.getElementsByTagName("span");

			for (var i = 0, s; (s = spans[ i]); ++i) {

				var str = s.firstChild && s.firstChild.nodeValue;

				if (belongsToClass(s, classNames.user)) {

					u = str;

				} else if (belongsToClass(s, classNames.host)) {

					h = str;

				} else if (belongsToClass(s, classNames.domain)) {

					d = str;

				} else if (belongsToClass(s, classNames.name)) {

					n = str;

				}

			}

			if (u && h && d) {

				chgSpan(span, u, h, d, n);

			}

		}

		function chgSpan(span, u, h, d, n) {

			var email = u + "@" + h + "." + d;

			var a = createElement("a");
			
			a.href = "mailto:" + email;

			a.className = classNames.email;
			
			a.appendChild(document.createTextNode(n));

			span.parentNode.replaceChild(a, span);

		}

		// DOM

		function createElement(nn) {

			return document.createElementNS ? document.createElementNS("http://www.w3.org/1999/xhtml", nn)
			: document.createElement(nn);

		}

		function belongsToClass(m, cn) {

			return new RegExp("(^| )" + cn + "( |$)").test(m.className);

		}

		// Events

		function addLoadEvent(f) {

			if (document.addEventListener) {

				document.addEventListener("DOMContentLoaded", f, false);

			}

			if (window.addEventListener) {

				window.addEventListener("load", f, false);

			} else if (document.addEventListener) {

				document.addEventListener("load", f, false);

			} else if (window.attachEvent) {

				window.attachEvent("onload", f);

			}

		}

		var classNames = {

			"email": "email",

			"user": "u",

			"host": "h",

			"domain": "d",
			
			"name": "n"

		};

		addLoadEvent(init);

		})();

	}