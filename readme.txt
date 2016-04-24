== About RSS to The Events Calendar Importer ==

RSS to the Events Calendar Importer parses an RSS feed of events and imports each into The 
Events Calendar from Modern Tribe. Obviously, the Events Calendar plugin is required. 

This plugin is a final project for CS50x 2016. Be advised as such it is the authors first foray
into WordPress plugin development. It seems like its working properly to me, but as I am a neophyte, use it at your own risk. 

For more information on the project requirements, see http://cdn.cs50.net/2016/x/project/project.html. 
The class itself is offered at https://www.edx.org/course/introduction-computer-science-harvardx-cs50x.

Helpful documentation, examples, and sources used in this plugin's development include:
 - WordPress Plugin Developer Handbook - https://developer.wordpress.org/plugins/
 - Various WordPress Codex Pages - http://codex.wordpress.org/
 - CS50 Pset 8 lookup() source code for how to parse RSS - http://cdn.cs50.net/2015/fall/psets/8/pset8/pset8.zip
 - Modern Tribe The Events Calendar GitHub (particularly File_Importer_Events.php) - https://github.com/moderntribe/the-events-calendar 
 - Modern Tribe The Events Calendar Function Documentation - particularly https://theeventscalendar.com/function/tribe_create_event/  and https://theeventscalendar.com/function/tribe_create_venue/
 - Post on Plug-in Self Deactivation found here: http://10up.com/blog/2012/wordpress-plug-in-self-deactivation/
 - Tutorial on Adding Admin / Settings Pages found here: https://blog.idrsolutions.com/2014/06/wordpress-plugin-part-1/
 - Handling Plugin Options with Settings API Tutorial found here: http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
 - Information on How to Get Posts by GUID found here: http://stackoverflow.com/questions/27053807/getting-posts-by-guid

== Installation ==

Use FTP to place the rss-to-events folder in your plugins directory. ( www -> wp-content -> plugins )

Or, zip the folder and upload it from the Plugin -> Add New -> Upload Plugin option in the WordPress back end. 

Activate, add your feed information, and you are good to go! 

== How do I find the feed information for the Settings Form? ==

You should already have a URL of an RSS feed of events you want to import into your calendar. 
Look at the RSS feed directly in your browser to view it as xml and copy the corresponding tag name into the settings form. 
Do not include angle brackets. (If your browser won't display the feed, try Chrome. It appears to automatically display RSS as xml).
