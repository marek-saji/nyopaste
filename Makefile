YUICOMPRESSOR=yuicompressor.sh
LESSC=lessc.sh

less:
	find app/htdocs/css/ -name *.less | while read less ; do dn="`dirname "$$less"`"; bn="`basename "$$less" .less`.css"; make "$$dn/$$bn"; done

%.css: %.less
	$(LESSC) $< > $@

%.min.css: %.css
	$(YUICOMPRESSOR) $< > $@

%.min.js: %.js
	$(YUICOMPRESSOR) $< > $@

