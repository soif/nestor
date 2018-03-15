#!/bin/bash

USER=moi
DIR=`pwd`
DIR_LIB=$DIR/lib

# ----------------------------------
function install_Sound(){
	echo "\n** Enable the Audio code ..................................
- Run armbian-config , enable audio codec in System/Hardware
"
}

# ----------------------------------
function install_WiringOP(){
	# for direct gpio command
	echo "\n** Install : WiringOP ................................"
	cd $DIR_LIB
	git clone https://github.com/zhaolei/WiringOP.git -b h3
	cd WiringOP
	chmod +x ./build
	./build
	gpio readall
}


# ----------------------------------
function install_RPI_for_Matrix(){
	#https://forum.armbian.com/topic/840-h3-spi/?page=2

	echo "\n** Enable the RPI module ..................................
- Run armbian-config , enable rpi-rpidev in System/Hardware
- add the following in /boot/armbianEnv.txt
param_spidev_spi_bus=0
#param_spidev_spi_cs=0
param_spidev_max_freq=1000000
"
}

# ----------------------------------
function install_Matrix(){
	#https://luma-led-matrix.readthedocs.io/en/latest/install.html

	echo "\n** Install Led Matrix .................................."
	apt-get install build-essential python-dev python-pip libfreetype6-dev libjpeg-dev python-setuptools
	pip --version
	pip install wheel
	pip install --upgrade luma.led_matrix


	#https://projetsdiy.fr/orange-pi-onelite-gpio-python-broches-pinout/	
	echo "\n** Install OrangePi GPIO .................................."
	pip install pyA20

	cd $DIR_LIB
	git clone https://github.com/duxingkei33/orangepi_PC_gpio_pyH3
	#git clone https://github.com/nvl1109/orangepi_zero_gpio
	cd orangepi_PC_gpio_pyH3
	python setup.py install
}


# ----------------------------------
function install_tm1637(){
	echo "\n** Install : tm1637 ................................"
	cd $DIR_LIB
	git clone https://github.com/soif/tm1637-python.git
	cd python-tm1637	
	pip install --upgrade OPi.GPIO
}

# ----------------------------------
function install_nginx_php(){
	echo "\n** Install : php ................................"
	apt-get install nginx
	#apt-get install nginx php5-cli php5-fpm
	apt-get install nginx php-cli php-fpm
	systemctl enable  php7.0-fpm
	ln -s $DIR/var/nginx/nestor_site /etc/nginx/sites-available/nestor_site
	ln -s /etc/nginx/sites-available/nestor_site /etc/nginx/sites-enabled/nestor_site
	rm /etc/nginx/sites-enabled/default
	service nginx restart
	chmod 755 /root/
}

# ----------------------------------
function fix_dhcp(){
	echo "\n** Prevent DHCP to write in resolv.conf ................................"
	echo 'make_resolv_conf() { :; }' > /etc/dhcp/dhclient-enter-hooks.d/leave_my_resolv_conf_alone
	chmod 755 /etc/dhcp/dhclient-enter-hooks.d/leave_my_resolv_conf_alone
}




##########################################################################################
# MAIN
##########################################################################################

echo ""
echo " ## Installing required packages ##"
echo ""

#install_Sound
#install_WiringOP
#install_RPI_for_Matrix
#install_Matrix
#install_tm1637
#install_nginx_php
#fix_dhcp
