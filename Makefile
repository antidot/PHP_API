#*******************************************************************************
#
# AFS © Antidot 2014
#
#*******************************************************************************
ROOT_PATH=$(CURDIR)

.PHONY: default tag_list tag doc


default: all_tests

tag_list:
	@git tag

tag:
	@git tag -a "v$$(./scripts/print_version.sh minor)"; \
	if [ "$$?" != "0" ]; \
	then \
		echo "You can try: make tag_force"; \
	fi

tag_force:
	@git tag -f -a "v$$(./scripts/print_version.sh minor)"

doc:
	@./scripts/gen_doc.sh

-include $(ROOT_PATH)/rules.mk
