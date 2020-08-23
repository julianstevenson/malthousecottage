// Tigra Menu Builder project file
// Project Name: undefined
// Saved: 08/13/2006

var N_MODE = 0;

var N_TPL = 1;

var A_MENU = {
	'menu_pos': 'relative',
	'menu_left': 50,
	'menu_top': 20
};

var A_TPL = [
	{
		'level_layout': 'h',
		'level_width': 100,
		'level_height': 24,
		'level_left': 99,
		'level_top': 0,
		'time_expand': 200,
		'time_hide': 200,
		'level_style': 0
	},
	{
		'level_layout': 'vb',
		'level_width': 120,
		'level_btop': 25,
		'level_bleft': 0,
		'level_left': 0,
		'level_top': 23,
		'level_style': 1
	},
	{
		'level_btop': 0,
		'level_bleft': 120
	}
];

var A_ITEMS = [
	{
		'text_caption': 'Reservations',
		'text_statusbar': 'Manage or view reservations',
		'children': [
			{
				'text_caption': 'View Reservations',
				'link_href': 'javascript:viewbookings()',
				'text_statusbar': 'View reservations'
			},
			{
				'text_caption': 'Add Reservation',
				'link_href': 'javascript:createreservation()',
				'text_statusbar': 'Create a new reservation'
			}
		]
	},
	{
		'text_caption': 'Family',
		'link_href': 'javascript:viewfamily()',
		'text_statusbar': 'View family details',
		'children': [
			{
				'text_caption': 'View Family Members',
				'link_href': 'javascript:viewfamily()',
				'text_statusbar': 'Manage or view family members'
			},
			{
				'text_caption': 'Add Family Member',
				'link_href': 'javascript:createfamilymember()',
				'text_statusbar': 'Create a new family member'
			},
			{
				'text_caption': 'Add Family',
				'link_href': 'javascript:createfamily()',
				'text_statusbar': 'Add a new family name'
			}
		]
	},
	{
		'text_caption': 'Messages',
		'link_href': 'javascript:viewmessages()',
		'text_statusbar': 'View messages received and sent',
		'children': [
			{
				'text_caption': 'View Messages',
				'link_href': 'javascript:viewmessages()',
				'text_statusbar': 'View messages'
			},
			{
				'text_caption': 'Create Message',
				'link_href': 'javascript:createmessage()',
				'text_statusbar': 'Create a new message'
			}
		]
	},
	{
		'text_caption': 'Events',
		'link_href': 'javascript:viewevents()',
		'text_statusbar': 'View family events',
		'children': [
			{
				'text_caption': 'View Events',
				'link_href': 'javascript:viewevents()',
				'text_statusbar': 'View family events such as birthdays, anniversaries or parties'
			},
			{
				'text_caption': 'Add Event',
				'link_href': 'javascript:createevent()',
				'text_statusbar': 'Create a new family event entry '
			}
		]
	},
	{
		'text_caption': 'Admin',
		'link_href': 'javascript:viewusers()',
		'text_statusbar': 'Administration functions',
		'children': [
			{
				'text_caption': 'View Users',
				'link_href': 'javascript:viewusers()',
				'text_statusbar': 'Manage and view Malthouse users'
			},
			{
				'text_caption': 'Add User',
				'link_href': 'javascript:adduser()',
				'text_statusbar': 'Add a new Malthouse user login'
			},
			{
				'text_caption': 'Change Password',
				'link_href': 'javascript:changepassword()',
				'text_statusbar': 'Change your password'
			},
			{
				'text_caption': 'Latest News',
				'link_href': 'javascript:latestnews()',
				'text_statusbar': 'Manage Latest News'
			}
		]
	}
];

var A_STYLES = [
	{
		'name': 'blue grades - root level',
		'box_background_color': ['#003399','#4D99E6',null],
		'box_border_color': '#003399',
		'box_border_width': 1,
		'box_padding': 4,
		'font_color': '#FFFFFF',
		'font_family': 'Arial, Helvetica, sans-serif;',
		'font_size': 12,
		'font_weight': 1,
		'font_style': 0,
		'font_decoration': 0,
		'text_align': 'center',
		'text_valign': 'middle',
		'n_order': 0,
		'n_id': 0,
		'classes_i': ['TM0i0','TM0i0','TM0i0'],
		'classes_o': ['TM0o0','TM0o1','TM0o1']
	},
	{
		'name': 'blue grades - sub levels',
		'box_background_color': ['#003399','#3C76B2',null],
		'box_border_color': '#2B547F',
		'box_border_width': 1,
		'box_padding': 4,
		'font_color': '#FFFFFF',
		'font_family': 'Arial, Helvetica, sans-serif;',
		'font_size': 12,
		'font_weight': 0,
		'font_style': 0,
		'font_decoration': 0,
		'text_align': 'left',
		'text_valign': 'middle',
		'n_order': 1,
		'n_id': 1,
		'classes_i': ['TM1i0','TM1i0','TM1i0'],
		'classes_o': ['TM1o0','TM1o1','TM1o1']
	}
];

 