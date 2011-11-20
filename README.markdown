# proxyrequests BETA

* Readme date: Nov 20 2011
* Contributors: lukapusic
* Author: Luka Pusic <luka@pusic.si>
* URI: http://360percents.com/

## Description
Proxyrequests is (will be) a proxy grabber, checker and request handler (single and paralell). Program proxyGrabber scrapes proxies from daily updated online lists and saves them to a file. Class requests in proxyRequests.php can easily initiate paralell or single http GET | POST connections tunneled through proxies from our proxy file. This program can be used for increasing view counts on several sites, increasing online voteing and polls results, anonymous scrapeing...


## System requirements
* PHP curl extension

## Instructions
1. Include the requests class in your project and initiate it

## Changelog

#### Nov 20 2011
* 

## Known issues
* curl_multi requests only work stable on 1020- instances at a time, so that should be the maximum limit of your paralell connections, but if you have more than 1020 proxies, the program will wait for the requests to complete and then execute the rest in a loop

## License
 ----------------------------------------------------------------------------
 "THE BEER-WARE LICENSE" (Revision 42):
 <luka@pusic.si> wrote this file. As long as you retain this notice you
 can do whatever you want with this stuff. If we meet some day, and you think
 this stuff is worth it, you can buy me a beer in return. Luka Pusic
 ----------------------------------------------------------------------------
