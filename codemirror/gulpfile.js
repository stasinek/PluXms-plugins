const ALL_MODES =
	'apl asciiarmor asn.1 asterisk brainfuck clike clojure cmake ' +
	'cobol coffeescript commonlisp crystal css cypher d dart diff ' +
	'django dockerfile dtd dylan ebnf ecl eiffel elm erlang factor ' +
	'fcl forth fortran gas gfm gherkin go groovy haml handlebars '+
	'haskell haskell-literate haxe htmlembedded htmlmixed http idl '+
	'javascript jinja2 jsx julia livescript lua markdown mathematica '
	'mbox mirc mllike modelica mscgen mumps nginx nsis ntriples '+
	'octave oz pascal pegjs perl php pig powershell properties '+
	'protobuf pug puppet python q r rpm rst ruby rust sas sass scheme '+
	'shell sieve slim smalltalk smarty solr soy sparql spreadsheet ' +
	'sql stex stylus swift tcl textile tiddlywiki tiki toml tornado ' +
	'troff ttcn ttcn-cfg turtle twig vb vbscript velocity verilog ' +
	'vhdl vue webidl xml xquery yacas yaml yaml-frontmatter z80';

// php dépend de clike, html, javascript
const MODES = 'css php';
const ADDONS = 'comment dialog display edit fold hint lint merge mode runmode scroll search selection tern wrap';

const gulp = require('gulp');