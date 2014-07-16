# Command line execution tools for concrete5 #

This repository provides scripts that allow you to run specific parts of the concrete5 installation through the command line.

These are extremely handy when you need to run a tool or a job that might take a long time to execute. For example importing hundreds of thousands of database entries. In this kind of situations, raising the PHP's own execution limit might not always be enough if you face e.g. server timeout settings. And other benefit is that these do not clog the web server's worker processes as the scripts would be running a long time. Therefore, it saves also headaches for your server administrators.

These tools are aimed to work with concrete5.6.


## Installation ##

1. Download this repository
2. Extract the "bin" folder to your concrete5 installation's root directory

This could also be installed outside the concrete5's root directory but that would require some changes in the base.php script that are not covered by this documentation.

For Apache servers, the folder should also contain a .htaccess file which prevents any public requests to those scripts.


## Usage ##

### Running a tool ###

1. Go into the bin-folder cd /your/home/dir/public_html/bin
2. Run the "tool.php" script from command line. The first argument should be the tool's name without the ".php" extension. E.g. "php tool.php my_custom_tool"


### Running a job ###

1. Go into the bin-folder cd /your/home/dir/public_html/bin
2. Run the "job.php" script from command line. The first argument should be the job's name without the ".php" extension. E.g. "php job.php generate_sitemap"


## Important notes ##

When running the scripts from command line, there are certain concrete5-specific variables that might not be correctly set for the command line "session".

One and the most important one of these being the "BASE_URL" constant. You can fix this by adding this into your /config/site.php configuration file:

```php
<?php
// ... your other configurations are above this line ...
if (defined('C5_EXECUTE_CMD')) {
	define('BASE_URL', 'http://www.yoursite.com');
	// For sites working in a relative directory, set this accordingly:
	define('DIR_REL', '');
}
```

As shown above, we can use the C5_EXECUTE_CMD constant during a command line "session" to define specific functionality for the command line use. For example, you could print out extra data straight from your jobs by checking whether the script is running through command line.
