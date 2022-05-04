# Bugs

[Visit the project](http://bugs.rcmission.net/)

# Please  ....  !!
Don't fork this project, but contribute to it.


# Installing Bugs

- Download from here ( ZIP or git ) ... please prefer a "Stable" version you`ll get from the "release" tab
- Create a MySQL Database  ( note name and password, you will need them during install process )
- Make the /uploads/ sub-directory  `write-able´ ( CHMOD -R  770 )
- set  www-data  ( CHOWN -R www-data:www-data )  as owner of the Bugs's main directory and sub-directories
- Open index.php ... it should bring you to ./install/index.php page and open the installation forms
	( ex.:   http://127.0.0.1/Bugs/index.php  ) 
- Create users
- Create first project

Enjoy!

# Sreenshots

#BUGS project dashboard
![HomePage](http://bugs.rcmission.net/images/bugs-index.png)

#BUGS Creating an issue
![IssuePage](http://bugs.rcmission.net/images/bugs-new-issue.png)

#Create and manage tags
![TagsPage](http://bugs.rcmission.net/images/bugs-create-tags.png)

#Adding user
![UserPage](http://bugs.rcmission.net/images/bugs-add-user.png)

# Upgrading from a previous installation
For versions 1.7 and higher
- just click on "Administration" and follow instructions

If your version is older than september 2018
- backup config.app.php and your uploads folder.
- simply replace the codebase with the new version (via an ftp client such as Filezilla).
- make sure your uploads folder and config.app.php are still there
- If necessary, add this line to your config.app.php -> please see the last config.app.example.php


## Requirements:

- Tested on: Apache, IIS;
- PHP 7.4+;   (since 2022/05/05, PHP 7.3 and lower are no longer supported)   
- MySQL 5+;
- PDO Extension for PHP (MySQL);
- MCrypt Extension for PHP : soon MCrypt will be removed from Bugs project;
- Javascript Enabled - Bugs also heavily uses Javascript.
- PHP rewrite module

### How to contribute

We welcome and appreciate all contributions. 

For any coding contribution, please use a git tool ( visit https://git-scm.com/book/fr/v2/D%C3%A9marrage-rapide-Installation-de-Git to know more ).
Then, push your modifications through a new branch. NEVER push on master. 


# Changelog
- v.1.8 : Responsive template.  Now matches the window size wherever you use BUG ( tablet, phone, computer ) 
    - built during summer 2019, first release on Sept 29th
    - includes the very first searching tools which scan all projects and all issues for regular expression
    - november 2019: attach files form the issue creating menu
    - november 2019: wysiwyg editor problems fixed; it could work on every comment
    - november 2019: you can move issue from project A to project B if you have access to both projects
    - January 2021 : no more reference to FlashPlayer  (rev 2).
    - February 2021: One can move ticket from project A to project B and assigns the ticket to a B's member (rev 3).
    - March 2021: Owner can change the Ticket's status when he comments it (rev 3).
    - March 2021: Reader can no longer edit ticket, nor watch stats (rev 3).
    - March 2021: Search field now will search also into comments' content ( not only title or projects' name  (rev 3).
    - March 2021: Comment and close at once - new button added (rev 3).
    - Arpil 2021: User can follow project / ticket and receive email when something appends (rev 4).
    - May 2021: Admin may change some options and edit texts sent when activity occurs in issue or project  (rev 5).
    - Aug 2021: BUGS can no more work under PHP 7.0; PHP 7.3 is now minimal requirement
    - Sep 2021: install process now includes creation of first project and first ticket
    - Oct 2021: Admin may manage many more options, even the way BUGS shows error on screen or by email; ready for PHP 8 (rev 6)
    - Nov 2021: Project member may ask BUGS to count his worktime spent on specific ticket ( punch in, punch out ) so your company may charge accordingly the client (rev 7)
    - Dec 2021: Acitivites report - what every single user have done (rev 7)
    - Dec 2021: Dark templates B-> with touch of blue  G-> with touche of green  R-> with touch of red/orange (rev 7)
- v.1.7 : July 2019 
	- reports are now totally integrated to BUGS which produces pdf files
- v.1.6 : February 2019 
	- correction file 1i: update system now also updates the config file
								 23 mars 2019
	- correction file 1h: Priority ( 1 - 5 ) to every issue ( 0 = closed )
								 ckeditor appears on every textarea ( for new issue, for edit issue, for new or edit project)
								 20 mars 2019
	- correction file 1g: Perfect install system, logo BUGS during install
								 8 mars 2019
	- all what planned in [Nice changes to come](https://github.com/pixeline/bugs/projects) is now running good.
	- CAUTION : we don't support PHP 5.x since BUGS 1.5, under 1.6 many functions are unusable for that reason
								 
- v.1.5.2b : 23 septembre 2018
	- Wysyg text editor  ( you can install the one you like )
	- sorting project's issues
	- filtering project's issues
	- upload and attach file to an issue
	- automatized installation (better than before)
	- linked to report system (optionnal, report system from another git deposit)
	- some problems appear under PHP 5.x
- v.1.5.2 : 15 March 2017
	- reassign issue
	- email system based on PHPmail: work every time you change assignation
	- projects sorted by name
	- percentage work done
	- percentage time passed before deadline
	- every single word on screen may be translated by app/application/language files
	- install in many language
	
- v1.5 : 12 July 2015:
	- fix: Time Display format now configurable, see config.example.php
	- fix: SQL « tags » table not included during installation
	- fix: Bugs assets now load correctly if inside a subfolder

various layout tweaks.

- v1.2 : 28 August 2014: 
	- French translation updates
	- Various fixes
	- Multilingual email (code by [Wolfgang Gassler](http://wolfgang.gassler.org/) - [source](https://github.com/mikelbring/tinyissue/pull/197))
	- Kanban-style planning board (code by [Steve McCullough](http://irrational.ca/) - [source](https://github.com/mikelbring/tinyissue/pull/194))
- v1.1 : 26 August 2014: 
	- Tags (code by [Anton Kanevsky](http://about.me/akanevsky) - [source](https://github.com/mikelbring/tinyissue/pull/180) )
	- Visual identity. Project rebaptized Bugs. As in "Hugs", with a B.
- v1.0 : 25 August 2014:
	- project forked from Tiny Issue 1.3, by [Michael Hasselbring](http://michaelhasselbring.com), [Zachary Hoover](http://zachoover.com) and [Suthan Sangaralingham](http://suthanwebs.com/)

# Main Developers

- [Alexandre Plennevaux](https://pixeline.be) ... to 2016
- [Patrick Allaire, ptre](http://cartefoi.net) from 2016 to now
