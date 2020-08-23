/* Tigra Menu items structure */
var MENU_ITEMS = [
	['Reservations', 'javascript:viewbookings()', {'sb':'Manage or view reservations'},
		['View Reservations', 'javascript:viewbookings()', {'sb':'View reservations'}],
		['Add Reservation', 'javascript:createreservation()', {'sb':'Create a new reservation'}]
	],
	['Family', 'javascript:viewfamily()', {'sb':'View family details'},
		['View Family Members', 'javascript:viewfamily()', {'sb':'Manage or view family members'}],
		['Add Family Member', 'javascript:createfamilymember()', {'sb':'Create a new family member'}],
		['Add Family', 'javascript:createfamily()', {'sb':'Add a new family name'}]
	],
	['Messages', 'javascript:viewmessages()', {'sb':'View messags received and sent'},
		['View Messages', 'javascript:viewmessages()', {'sb':'View messages'}],
		['Create Message', 'javascript:createmessage()', {'sb':'Create a new message'}]
	],
	['Events', 'javascript:viewevents()', {'sb':'View family events'},
		['View Events', 'javascript:viewevents()', {'sb':'View family events such as birthdays, anniversaries or parties'}],
		['Add Event', 'javascript:createevent()', {'sb':'Create a new family event entry '}]
	],
	['Admin', null, {'sb':'Administration functions'},
		['View Users', 'javascript:viewusers()', {'sb':'Manage and view Malthouse users'}],
		['Add User', 'javascript:adduser()', {'sb':'Add a new Malthouse user login'}],
		['Change Password', 'javascript:changepassword()', {'sb':'Change your password'}],
		['Latest News', 'javascript:latestnews()', {'sb':'Manage Latest News'}]
	]
];
