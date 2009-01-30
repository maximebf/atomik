all: release

release:
	svn export ./release-base ./atomik
	cp Atomik.php ./atomik/index.php
	cp CHANGELOG.txt ./atomik
	cp LICENSE.txt ./atomik
	zip -r atomik.zip ./atomik
	
clean:
	rm -r ./atomik
	rm atomik.zip