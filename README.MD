# VSX-930 remote control via telnet interface
A web page built on top of the telnet API described in [resources/VSX-1120-K-RS232.PDF](resources/VSX-1120-K-RS232.PDF).  This page only impelments the power, volume and function selectors - that's all I needed.  The remaining functionality could be easily implemented in the same style and I would welcome Pull Requests that do so.
 
 ## Installation
1\. Clone the Repo:
 
 ````bash
$ git clone 
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
;config for the reciever in use

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