YUICOMPRESSOR=yuicompressor.sh
LESSC=node hg/htdocs/js/less/bin/lessc

all: less js-submodules

js-submodules:
	@echo
	@echo '** JS SUBMODULES'
	@echo
	@find app/htdocs/js/ -iname Makefile | while read makefile ; do ( cd "`dirname "$$makefile"`"; make ); done

less:
	@echo
	@echo '** LESS STYlESHEETS'
	@echo
	@find app/htdocs/ -name *.less | while read less ; do dn="`dirname "$$less"`"; bn="`basename "$$less" .less`.css"; make "$$dn/$$bn"; done

%.css: %.less
	$(LESSC) -x $< > $@

%.min.js: %.js
	$(YUICOMPRESSOR) $< > $@

