#*******************************************************************************
#
# AFS © Antidot 2014
#
#*******************************************************************************
ROOT_PATH=$(CURDIR)

default: all_tests

tag_list:
	@git tag

tag:
	@git tag -a "v$$(./scripts/print_version.sh)"

-include $(ROOT_PATH)/rules.mk
