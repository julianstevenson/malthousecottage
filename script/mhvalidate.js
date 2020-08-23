
function validatelogin() {
  if (document.mainform.email.value=='') {
     alert('Please enter an email address')
     document.mainform.email.focus()
     return false;
  }
  if (document.mainform.email.value.indexOf('@')<0) {
     alert('Please enter a valid email address')
     document.mainform.email.focus()
     return false;
  }
  if (document.mainform.password.value=='') {
     alert('Please enter a password')
     document.mainform.password.focus()
     return false;
  }

  return true;
}


function checkmandatoryfield(obj, nme) {
  if (obj!=null) {
     if (obj.value == '') {
       alert( 'Please enter mandatory field: ' + nme);
       obj.focus()
       return false;
     }
  }
  
  return true;
}

function checkmandatoryfielddropdown(obj, nme, ignorefirst) {
  if (obj!=null) {
     if (ignorefirst) {
       if (obj.selectedIndex < 1) {
         alert( 'Please enter mandatory field: ' + nme);
         obj.focus()
         return false;
       }
     }
     else if (obj.selectedIndex < 0){
       alert( 'Please enter mandatory field: ' + nme);
       obj.focus()
       return false;
     }
  }
  
  return true;
}

function checkdate(obj, nme) {
  var separator = "/"
  var date
  var month
  var year
  var test

  if (obj.value.length == 10) {
    if (obj.value.substring(2, 3) == separator && obj.value.substring(5, 6) == separator) {
        date  = obj.value.substring(0, 2)
        month = obj.value.substring(3, 5)
        year  = obj.value.substring(6, 10)
        test = new Date(year, month - 1, date)

        if (year == Y2K(test.getYear()) && (month - 1 == test.getMonth()) && (date == test.getDate())) {
          return true;
        }
        else {
          alert('Please enter a valid date in format dd/mm/yyyy: ' + nme);
          obj.focus();
          return false;
        }
    }
    else {
        alert('Please enter a valid date in format dd/mm/yyyy: ' + nme);
        obj.focus();
        return false;
    }
  }
  else {
   alert('Please enter a valid date in format dd/mm/yyyy: ' + nme);
        obj.focus();
   return false;
  }
  
  return true;
}

function Y2K(pYear) { 
  return (pYear < 1000) ? pYear + 1900 : pYear
}


function CheckDateDropDownStartEnd(pStartDateControl, pEndDateControl, pStartFieldCaption, pEndFieldCaption) {

	// Syntax: Date.UTC(year, month, day[, hours[, minutes[, seconds[,ms]]]])
        var startday  = pStartDateControl.value.substring(0, 2)
        var startmonth = pStartDateControl.value.substring(3, 5)
        var startyear  = pStartDateControl.value.substring(6, 10)
        var endday  = pEndDateControl.value.substring(0, 2)
        var endmonth = pEndDateControl.value.substring(3, 5)
        var endyear  = pEndDateControl.value.substring(6, 10)
 	
 	var startdate = new Date(parseInt(startyear-0), parseInt(startmonth-1), parseInt(startday-0), 0, 0, 0);
 	var enddate = new Date(parseInt(endyear-0), parseInt(endmonth-1), parseInt(endday-0), 0, 0, 0);
var starttime = Date.UTC(Y2K(startdate.getYear()), startdate.getMonth(), startdate.getDate(), 0, 0, 0)
  var endtime = Date.UTC(Y2K(enddate.getYear()), enddate.getMonth(), enddate.getDate(), 0, 0, 0)
 
  if (endtime < starttime) {
    alert(pStartFieldCaption + ' must be before ' + pEndFieldCaption)
    pStartDateControl.focus()
    return false
  }
  return true
}

