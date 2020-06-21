![GitHub last commit](https://img.shields.io/github/last-commit/lukesubuntu/virtualmin_blesta)
![GitHub issues](https://img.shields.io/github/issues/lukesubuntu/virtualmin_blesta)
![GitHub issues by-label](https://img.shields.io/github/issues/lukesubuntu/virtualmin_blesta/bug?color=orange)
![GitHub closed issues](https://img.shields.io/github/issues-closed/lukesubuntu/virtualmin_blesta?color=red)  
  
  
  Plugin in BETA so please check [Todo Status](https://github.com/lukesUbuntu/virtualmin_blesta/issues/11) or [Current Project Status](https://github.com/lukesUbuntu/virtualmin_blesta/projects/2) before installing.
  
# Virtualmin Module for Blesta v4.0+ 

  
I myself have now got into webhosting with Virtualmin and updating this plugin for my own needs as i want all my clients hosting automated, from billing to the client controlling everything within blesta.

  If you have any suggestion's feel free to create an issue, aim is to allow client to login to blesta to manage there hosting.
  
  [Todo Status](https://github.com/lukesUbuntu/virtualmin_blesta/issues/11) or [Current Project Status](https://github.com/lukesUbuntu/virtualmin_blesta/projects/2)
  
  Module currently works with blesta 3.5+ (php 5.6 & 7.0+) project is still in beta i wouldn't install till  [Todo Status](https://github.com/lukesUbuntu/virtualmin_blesta/issues/11) or [Current Project Status](https://github.com/lukesUbuntu/virtualmin_blesta/projects/2) has been completed. But if you want to have a play and provide issues or feedback feel free to install & help make this module.

  Ahh yeah so When will it be ready?   
  Once completed above and redone a few security issues i have found that, i am hoping for August 2020 for first release

## 

**Virtualmin Blesta Module version 0.1.3**

### Current available client options for VirtualMin
- server status
- mail accounts
- install script
- databases 


[**Blesta**](http://www.blesta.com) is a professional web based billing and support application focusing on productivity and usability.

[**Virtualmin**](https://www.virtualmin.com/) is a powerful and flexible web hosting control panel for Linux it allows full server management.

Virtualmin Blesta module uses [**Virtualmin's Api**](https://www.virtualmin.com/documentation/developer/http) to allow clients to take full control of there hosting within blesta. It also can auto provision across multiple server groups or pop locations.


## Module Screen shots
please note not all tabs are active in this repo module version 

### Client Panel - Overview

![Overview](http://i.imgur.com/PLtkNdA.png)

### Client Panel - Mail Accounts

![Mail Accounts](http://i.imgur.com/Bqs81OT.png)

### Client Panel  - Databases

![Databases](http://i.imgur.com/7i5DLMa.png)

####### NOTES #######
 Plugin in BETA so please check [Todo Status](https://github.com/lukesUbuntu/virtualmin_blesta/issues/11) or [Current Project Status](https://github.com/lukesUbuntu/virtualmin_blesta/projects/2) before installing.
  
## Installation instructions
### Via Git
1. clone repo into your blesta modules folder eg `cd /home/blesta_install/public_html/components/modules` 
2. run `$ git clone https://github.com/lukesUbuntu/virtualmin_blesta.git`
3. Go to Blesta Admin, and click on Settings->Modules and it will list the Virtualmin module, Click on "INSTALL"
4. Go to Virtualmin module and add your Virtualmin Server Details.

#### Via FTP
1. [Download](https://github.com/lukesUbuntu/virtualmin_blesta/archive/master.zip) this module
2. Uncompress file,and rename folder to *virtualmin_blesta* 
3. Upload *virtualmin_blesta* folder to your blesta root dir to */components/modules/* directory.
3. Go to Blesta Admin, and click on Settings->Modules and it will list the Virtualmin module, Click on "INSTALL"
4. Go to Virtualmin module and add your Virtualmin Server Details.

