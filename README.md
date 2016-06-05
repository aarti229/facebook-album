# Facebook Albums Download 
Demo :-  [Facebook Albums Challenge](http://fzwebtech.com/rtCamp-Challenge)

## process :
1. User Login using Facebook credentials. Ask user to give permission to access of email,cover_photo,name and photos. Application fetches all Albums which is added by user or in which user is tagged 
2. Albums are displayed with a Thumbnail, Album Name. When a user clicks on Album cover-photo, all photos for that album are displayed in full screen slideshow.
3. A "Download" link(in blue) is displayed for each album. When user clicks on "Download" link, jquery(Ajax) processes PHP script to collect photos for that album, Zip them and prompts "Download Zip Folder" Link to user for download
4. An checkbox is displayed for each album. A "Download Selected Albums" link is displayed at top. When user clicks on "Download Selected Albums" link, jquery(Ajax) processes PHP script to collect photos for all checked albums, Zip them and prompts "Download Zip Folder" Link to user for download
5. A "Download All Albums" link is displayed at top. When user clicks on "Download All Albums" link, jquery(Ajax) processes PHP script to collect photos for all albums, Zip them and prompts "Download Zip Folder" Link to user for download.



## Library Used:

 * https://developers.facebook.com/docs/reference/php/4.0.0
 * http://sachinchoolur.github.io/lightGallery/
 * http://fgnass.github.io/spin.js/
 * http://getbootstrap.com/

Scripting Languages:  Jquery Ajax

Styling: Css

## How to use

=> First of all Go on https://developers.facebook.com/  

=> From menu select Apps->Add a New App->WWW->Give Name an create new app id.

=> Test version of another -> select No 

=> Choose your category -> clicks Create App ID. 

=> Right-Top corner select skip quick start.

=> After that in your app go to Settings Add: -> Namespace -> Contact Email

=> In settings +Add Platform-> Select Website Add: -> Site Url -> Domain NOTE : even if localhost url also works.


=> NOTE: if you want the all photos permission of users then you need to approve your facebook app first.

=> Download our app from github 

=> put this in root directory(Wamp => www, xampp => htdocs) -> unzip it. 

=> go to includes.php 
```
    fb_login_url is same url which is added in facebook app->settings.
    $fb_login_url = 'your login url or index url where the response is come'; 
    $fb_logout_url = 'your logout url';
```

=>  Run the index.php page and have fun


