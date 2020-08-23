function getsession() {
  if(document.mainform.sess.value != '') {
    return  document.mainform.sess.value;
  }
  else {
    alert('no session id');
    document.location.href='index.php' ;
    return '';
  }
  

}
function viewcontactus() {
  document.location.href='contactus.php?sess=' + escape(getsession());
}

function viewevents() {
  document.location.href='events.php?sess=' + escape(getsession());
}

function viewbookings(locationid, approved) {

  var href = '';
  if (locationid!=null && locationid!='0' && locationid!='')
	  href='reservations.php?locationid=' + locationid + '&sess=' + escape(getsession());
  else
	  href='reservations.php?locationid=0&sess=' + escape(getsession());

  if (approved!=null &&  approved!='')
	  href +='&approved=' + approved;
  document.location.href = href;
}

function viewbookingrequests(locationid, approved) {
  var href = '';
if (locationid!=null && locationid!='0' && locationid!='')
	  href='reservations.php?locationid=' + locationid + '&sess=' + escape(getsession());
  else
	  href='reservations.php?locationid=0&sess=' + escape(getsession());

  if (approved!=null && approved!='0' && approved!='')
	  href +='&approved=' + approved;
  document.location.href = href;
}

function approvebooking(reservationid) {

  var href=''
  href='reservation_approve.php?id=' + reservationid + '&sess=' + escape(getsession());
  if (document.mainform !=null && document.mainform.locationid !=null) {
      href = href + '&locationid=' + document.mainform.locationid.value;
  }
  if (document.mainform !=null && document.mainform.approved !=null) {
      href = href + '&approved=' + document.mainform.approved.value;
  }


  document.location.href = href;
}


function viewinfo() {
  alert('Go to cottage info page');
}

function viewfamily() {
  document.location.href='family.php?sess=' + escape(getsession());
}

function viewmessages() {
  document.location.href='messages.php?sess=' + escape(getsession());
}

function createmessage() {
  document.location.href='message_add.php?sess=' + escape(getsession());
}


function viewproblem() {
  document.location.href='contactus.php?team=support&sess=' + escape(getsession());
}

function showmessage(msgid) {
  alert('Show message ' + msgid);
}

function viewmain() {
  document.location.href='welcome.php?sess=' + escape(getsession());
}

function selectreservationdates() {
  alert('Select dates')
}


function createreservation() {
  document.location.href='reservation_add.php?sess=' + escape(getsession());
}

function contactmanagement() {
  document.location.href='contactus.php?team=management&sess=' + escape(getsession());
}

function showfamilydetail() {
  alert('Show family detail');
}

function createfamilymember() {
  document.location.href='familymember_add.php?sess=' + escape(getsession());
  
}

function createfamily() {
  document.location.href='family_add.php?sess=' + escape(getsession());
}

function createnews() {
  document.location.href='news_add.php?sess=' + escape(getsession());
}


function createevent() {
  document.location.href='event_add.php?sess=' + escape(getsession());
}

function viewmessage(id) {
   document.location.href='message.php?msgid=' + id + '&sess=' + escape(getsession());
}

function viewusers() {
   document.location.href='users.php?sess=' + escape(getsession());
}

function adduser() {
   document.location.href='user_add.php?sess=' + escape(getsession());
}

function latestnews() {
   document.location.href='latestnews.php?sess=' + escape(getsession());
}

function reservationrequests() {
   document.location.href='reservations.php?sess=' + escape(getsession());
}

function changepassword() {
   document.location.href='changepassword.php?sess=' + escape(getsession());
}
