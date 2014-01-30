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
	@git tag -a "v$$(./scripts/print_version.sh)"

doc:
	@./scripts/gen_doc.sh

-include $(ROOT_PATH)/rules.mk
