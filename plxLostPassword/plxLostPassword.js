// auth.php
function validateEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

function lostpassword_onsubmit(inputName, inputPassword, errorMsg) {
    var field1 = document.getElementById(inputName);
    var field2 = document.getElementById(inputPassword);
    var result = false;
    if (field1 && field2) {
        var test = validateEmail(field1.value);
        if (test) {
            field2.value = '';
            result = true;
        }
        else {
            alert(field1.value + '\n\n' + errorMsg + ' !!');
        }
    }
    else {
        alert(inputName + ' or ' + inputPassword + ' not found !!!');
    }
    return result;
}

function lostpassword_onclick(idDiv, idCheck, inputMail, inputPassword, errorMsg) {
	var div1 = document.getElementById(idDiv);
	var inputCheck = document.getElementById(idCheck);
    var form_auth = document.getElementById('form_auth');
	if (div1 && inputCheck && form_auth) {
		if (div1.style.display == 'block') {
			div1.style.display = 'none';
			inputCheck.value = '';
            form_auth.onsubmit = '';
		}
		else {
			div1.style.display = 'block';
			inputCheck.value = '1'
            form_auth.onsubmit = function() { return lostpassword_onsubmit(inputMail, inputPassword, errorMsg); }
		}
	}
	else {
		alert('Elements id="' + idDiv + ' or ' + idCheck +  ' or ' + inputMail + '" are missing !');
	}
	return false;
}

// config.php
function lostpassword_config_onsubmit(id1, errorMsg) {
	var body = document.getElementById(id1);
	var result = false;
	if (body) {
		var value = body.value;
		var regex1 = /#LOGIN/;
		var regex2 = /#PASSWORD/;
		if (regex1.test(value) && regex2.test(value)) {
			result = true;
		}
		else {
			alert(errorMsg);
		}
	}
	else {
		alert('Element with id="' + id1 +'" not found !!!');
	}
	return result;
}
