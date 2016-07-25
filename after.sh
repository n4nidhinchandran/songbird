#!/bin/bash 

sudo add-apt-repository ppa:brightbox/ruby-ng
sudo apt-get update
sudo apt-get install ruby2.3 ruby2.3-dev -y

sudo gem install mailcatcher
sudo sed -i -e '$i /usr/local/bin/mailcatcher --ip=0.0.0.0\n' /etc/rc.local
sudo echo "sendmail_path = /usr/bin/env $(which catchmail)" | sudo tee -a /etc/php5/mods-available/mailcatcher.ini
sudo php5enmod mailcatcher
#start now
$(which mailcatcher) --ip=0.0.0.0
