BRIEF DESCRIPTION:

This application is commiting a user verification in front of a my sql database.

it uses AJAX in order or not the login information provided by the user are correct, and in case they are, the user is being redirected to a Welcome page.
In case they aren't, a suitable error message is presented to the user.

The application keeps track of the user login credentials in the following manner:
As a first priority, it relies on an active session, if an active session is not existant, an attempt to identify the user using a cookie is being made.
in case an active we were able to identify the user using a cookie, we are also starting a session for this user.

if both of those attempts fail, the user is being redirected to the login page using an HTTP header.

For security reasons, each session is used up to 5 times before it's ID is regenerated, and is only valid for the same IP or the same user agent(For more strict settings, the 'AND' clause can
be replaced with 'OR'), in order to preven hijacking sessions via XSS etc.

the cookie is also valid for only 5 times, but since every time it's being use, we load the information into a session, it means that the user actually has to leave the site completely
and ten return 5 times during the day(cookie set to expire in 24 hours), which is reasonable.

Each time the user is logging-in, we are updating the cookie attribute at the DB, to correspond to the current choice of the user.

In case a user tries to use a cookie, while the database specifies that he choose not to, we do not allow him to do so, and immediately deactivate both his session and his cookie.
In case the user is not using a cookie, but the database specifies that he does, we correct this error at the first opportunity(decided not to use another query to update this field while logging off).

sanitizing data for quires is done via MD5(both user name and pass).
and the $cookie variable is sanitized manually.

FUTURE CHANGES:

I would like to add proper error handling(to prevent a situation where the user is being thrown out of the system without an explanation).
And of course some DB abstraction.

IMPORTANT REMARK:

COOKIES ARE NOT GETTING THE ABSOLUTE PATH, THE RELATIVE PATH, THE HTTP ONLY, AND THE SECURED PARAMETERS, SINCE USING THOSE PARAMETERS DISABLES THE COOKIES ON THE WAMP SERVER THAT I WAS TESTING MY
CODE ON, PLEASE DISREGARD!

To test the code on an HTTP server: http://crm.metacode.co.il/oleg/login.php