#*******************************************************************************
#
# AFS © Antidot 2014
#
#*******************************************************************************
.PHONY: all_tests test individual_tests test_coverage
.PHONY: local_test local_individual_tests recurse


TEST_DIR=$(filter %/TEST, $(CURDIR))

all_tests: test individual_tests

test: local_test recurse

individual_tests: local_individual_tests recurse

local_test:
	@if [ -f $(which phpunit) ]; \
	then \
		if [ "${TEST_DIR}" != "" ]; \
		then \
			phpunit -d display_errors=1 -d display_startup_errors=1 -d error_reporting=-1 --stop-on-failure --include-path $(ROOT_PATH) $(CURDIR) || exit 1; \
		fi; \
	else \
		echo 'You need phpunit installed on your computer to run unit tests'; \
	fi

local_individual_tests:
	@if [ -f $(which phpunit) ]; \
	then \
		if [ "${TEST_DIR}" != "" ]; \
		then \
			for file in $$(ls *.php); \
			do \
				echo "\n=============================================="; \
				echo ">>>> Running tests for $${file} <<<<"; \
				echo "=============================================="; \
				phpunit --stop-on-failure --include-path $(ROOT_PATH) $${file} || exit 1; \
			done; \
		fi; \
	else \
		echo 'You need phpunit installed on your computer to run unit tests'; \
	fi

test_coverage:
	@if [ -f $(which phpunit) ]; \
	then \
			rm -rf $(ROOT_PATH)/coverage; \
			phpunit -d display_errors=1 -d display_startup_errors=1 -d error_reporting=-1 --coverage-html=$(ROOT_PATH)/coverage --stop-on-failure --include-path $(ROOT_PATH) $(ROOT_PATH) || exit 1; \
	else \
		echo 'You need phpunit installed on your computer to run unit tests'; \
	fi

recurse:
	@for DIR in $$(ls -d */ 2> /dev/null); \
	do \
		if [ -f "$${DIR}/Makefile" ]; \
		then \
			$(MAKE) -C $${DIR} $(MAKECMDGOALS) || exit 1; \
		fi; \
	done






