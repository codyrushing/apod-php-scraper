# A PHP Scraper for NASA's Astronomy Picture of the Day

## Disclaimer - I am not a PHP developer.  Some of this code is probably somewhat ghetto.  Also, scraping in general is kinda ghetto.  Use at your own risk.

[NASA's Astronomy Picture of the Day](http://apod.nasa.gov/apod/astropix.html) is great, but they have no API for it - just a super old-school webpage that they update once a day.  

What this script does is scrape that page once a day, pull out the title and image URL and store them in the server memory (using APC).  Optionally, it can also save a thumbnail out for you to use.

** Note: ** sometimes NASA posts a YouTube video instead of a photo.  If that's the case, it will use whatever is pre-existing in the cache (presumably yesterday's APOD), but it will not try to store a YouTube video.

## Usage

There are a couple of variables at the top of apod.php that you can modify:

* $save_thumbnail : Boolean, turns on thumbnail creation;
* $thumbnail_width : in pixels;
* $apod\_folder\_relative : path to where you want thumbnails stored.  Relative from server root.  Make sure whatever folder you use has proper write permissions.

Simply include the apod.php script.  Then if you do an apc_fetch("apod_data"), it will return an associative array with the following keys:

* "title" : The title of the APOD
* "hosted\_image\_path" : Path to the full size NASA-hosted image
* "thumb" : path to thumbnail, unless thumbnails are turned off in which case this is null

This script requires two PHP libraries be installed: 

* [cURL](https://php.net/curl) - required to fetch the page server side
* [APC](http://www.php.net/apc/) - to cache the results in memory