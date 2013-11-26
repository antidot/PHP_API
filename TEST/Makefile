#*******************************************************************************
#
# AFS © Antidot 2013
#
#*******************************************************************************
.PHONY: test


test:
	@if [ $(which phpunit) ]; \
	then \
		echo 'You need phpunit installed on your computer to run unit tests'; \
	else \
		phpunit --stop-on-failure --include-path $(CURDIR)/.. $(CURDIR); \
	fi
