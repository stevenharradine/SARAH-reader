 #!/bin/bash
 cd /etc/sarah/www/apps/SARAH-reader/updater
 javac -cp "../../../libs/mysql-connector-java-5.1.32-bin.jar:../../../libs/dom4j-1.6.1.jar:../../../libs/nekohtml.jar:../../../libs/json-simple-1.1.1.jar:../../../libs/feed4j.jar:../../../libs/xercesImpl-2.7.1.jar:." ReaderUpdater.java