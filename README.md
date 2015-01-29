# auto-fill-post-password-from-url
Fills out the post password for the user if provided in the url

#Use case
If a post in your site is semi-public and proteced by a password the visiter is prompted to enter the password.
In some cases of embedding or communication with less web savvy persons the extra step of entering a password can be a un-needed barrier.

If the provided access-token/password was not correct the default password form will be displayed.

#How to
Adding the following parameter to the url of the protected post:
?access_token=[yourpasswordhere]

Example:
http://example.com/2015/01/29/semi-public-post/?access_token=PostPassWordHere

#Risks
Sending the post password inside the url via GET-Method is not as safe as via POST or let the user type it.
It's maybe a trade of to make.

#Hooks and Filter
##Filter
###cmc_afppwbu_url_parameter
By this filter the name of the url parameter can be changed.

#Issues
- The provided password can just be upper-, lowercase alphanumeric.
