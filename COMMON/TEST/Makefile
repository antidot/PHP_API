#*******************************************************************************
#
# AFS © Antidot 2013
#
#*******************************************************************************
.PHONY: all_tests test individual_tests

all_tests: test individual_tests

test:
	@if [ -f $(which phpunit) ]; \
	then \
	phpunit --stop-on-failure --include-path $(CURDIR)/../.. $(CURDIR); \
	else \
		echo 'You need phpunit installed on your computer to run unit tests'; \
	fi

individual_tests:
	@if [ -f $(which phpunit) ]; \
	then \
		for file in $$(ls *.php); \
		do \
		  echo "\n=============================================="; \
			echo ">>>> Running tests for $${file} <<<<"; \
		  echo "=============================================="; \
			phpunit --stop-on-failure --include-path $(CURDIR)/../.. $${file} || break; \
		done; \
	else \
		echo 'You need phpunit installed on your computer to run unit tests'; \
	fi
