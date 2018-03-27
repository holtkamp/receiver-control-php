# VSX-930 remote control via telnet interface
A web page built on top of the telnet API described in [resources/VSX-1120-K-RS232.PDF](resources/VSX-1120-K-RS232.PDF).  This page only impelments the power, volume and function selectors - that's all I needed.  The remaining functionality could be easily implemented in the same style and I would welcome Pull Requests that do so. and a note of thanx to Raymond Julian for his article that gave me the idea [Remote control your Pioneer VSX receiver over telnet](http://raymondjulin.com/2012/07/15/remote-control-your-pioneer-vsx-receiver-over-telnet/).
 
## Installation

1\. Clone the Repo:
 
````bash
$ git clone https://bitbucket.org/gordywills/receiver-control.git
````

2\. run Composer Install

````bash
$ composer install
````

3\. run NPM Install

````bash
$ npm install
````

4\. copy [config.ini.dist](config.ini.dist) to config.ini and edit for the relevant telnet properties of your receiver.

````bash
$ cp config.ini.dist config.ini
$ nano config.ini
````

````ini
;config for the receiver in use

;the ip address of your receiver, the default telnet port is 23
dsn="ip_address:telnet_port"
;the return prompt for your receiver - in the VSX-930 this is a zero length string
prompt=""
;the error prompt for your receiver - in the VSX-930 this is a zero length string
errorPrompt=""
;the return line ending for your receiver - in the VSX-930 this is a <CR><LF> windows style line ending
lineEnding="\r\n"
````

5\. Serve [index.php](index.php) from a webserver of your choice and navigate to it.

*Use XML*
The Telnet connections seem very instable. It might be better to use XML
http://blue-pc.net/2013/12/28/denon-av-reciever-ueber-http-steuern/

Volume
http://denon/goform/formiPhoneAppDirect.xml?MVUP
http://denon/goform/formiPhoneAppDirect.xml?MVDOWN

http://denon/goform/formiPhoneAppDirect.xml?SITUNER
http://denon/goform/formiPhoneAppDirect.xml?SINET //Source Input NET