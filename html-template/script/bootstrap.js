var bpConfig = {};
var bpPlayer = null;
var bpServices = null;
var bpExercises = null;

function makeUUID() {
	// http://www.ietf.org/rfc/rfc4122.txt
	// section 4.4 (Algorithms for Creating a UUID from Truly Random or
	// Pseudo-Random Number)
	// http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript
	return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
		var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
		return v.toString(16);
	}).toUpperCase();
}

function getSessionID() {
	// http://www.elated.com/articles/javascript-and-cookies/
	var results = document.cookie.match('(^|;) ?' + 'PHPSESSID' + '=([^;]*)(;|$)');
	if (results)
		return (unescape(results[2]));
	else
		return null;
}

// Set configuration values
bpConfig.uuid = makeUUID();
bpConfig.sessionID = getSessionID();
bpConfig.endpoint = 'rest.php';

// Bootstrap
bpServices = new services();
bpServices.getCommunicationToken();

function onCommunicationReady() {
	// Retrieve app preferences
	bpServices.send(false, 'getAppPreferences', null, onPreferencesRetrieved);

	// Prototype only. Login user
	var parameters = {
		'name' : 'insert_your_login_here',
		'pass' : hex_sha1("inser_your_pass_here")
	};
	bpServices.send(false, 'processLogin', parameters, onLoginRetrieved);

	// Initialize modules
	bpExercises = new exercise();
}

function onPreferencesRetrieved(data) {
	var info = data['response'];
	for ( var i in info) {
		if (info[i] != undefined) {
			var key = info[i]['prefName'];
			var value = info[i]['prefValue'];
			bpConfig[key] = value;
		}
	}
}

function onLoginRetrieved(data) {
	var info = data['response'];
	bpConfig.user = info;
}

function onPlayerReady(playerid) {
	bpPlayer = document.getElementById(playerid);
	if (!bpPlayer) {
		Alert('There was a problem while loading the video player.');
		return;
	}

	
}

function onConnectionReady(playerId){
	
	if(bpPlayer == undefined){
		bpPlayer = document.getElementById(playerid);
		if (!bpPlayer) {
			Alert('There was a problem while loading the video player.');
			return;
		}
	}
	
	
	// Load the exercises module using the "Sintel" sample video
	var ex = {
		'id' : 73,
		'name' : 'U1MbBtkIGZQ.flv',
		'title' : 'Sintel'
	};
	bpExercises.loadExercise(bpPlayer, ex);
	
}