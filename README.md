**Virtualmin Blesta Module version 0.1.3**

### This virtualmin for blesta module ## is not finished, and only clients menus available are 
- server status
- mail accounts
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
This is first initial release so please use at your *own risk*, I have done a ton of *sandbox* testing, and only several **live** testing, please [submit](https://github.com/lukesUbuntu/gogetsslv2/issues) any issues, and check [todo list](#-todo)
  
## Installation instructions
### Via Git
1. CD into blesta root folder */components/modules* directory
2. run `$ git clone https://github.com/lukesUbuntu/virtualmin_blesta.git`
3. Go to Blesta Admin, and click on Setings->Modules and it will list the Virtualmin module, Click on "INSTALL"
4. Go to Virtualmin module and add your Virtualmin Server Details.

#### Via FTP
1. [Download](https://github.com/lukesUbuntu/virtualmin_blesta/archive/master.zip) this module
2. Uncompress file,and rename folder to *virtualmin_blesta* 
3. Upload *virtualmin_blesta* folder to your blesta root dir to */componets/modules/* directory.
3. Go to Blesta Admin, and click on Setings->Modules and it will list the Virtualmin module, Click on "INSTALL"
4. Go to Virtualmin module and add your Virtualmin Server Details.

