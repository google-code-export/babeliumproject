function services(){
	this.protocol = 'http://'
	this.host = 'embedbabelium/api/';
	this.endpoint = bpConfig.endpoint;
	this.lastRandomizer = '';
	this.statToken = 'myMusicFightsAgainstTheSystemThatTeachesToLiveAndDie'; //Bob Marley's Quote
	this.commToken = '';
	this.authToken = '';
	this.token = '';

	//This variable will be accesible in the callback and points to the right scope
	var instance = this;
	
	/**
	 * The way callback should be passed is uncertain maybe we should pass it as a String and then use eval() to fetch the actual function. Also since this function
	 * is to be nested inside a class we must prepend the class instance name, in this case should be something like "services.theFunction"
	 */
	this.send = function(secured,method,parameters,callback){
		this.protocol = secured ? 'https://' : 'http://';
		var qs = this.protocol + this.host + this.endpoint + '?' + method;
		var data = {};
		var cb = callback;
		data.method = method;
		if(parameters != null)
			data.parameters = parameters;
		if(cb == null){
			cb = instance.onServiceSuccess; 		
		}
		this.token = this.generateToken(method);
		data.header = {"token":this.token,"session":bpConfig.sessionID,"uuid":bpConfig.uuid};
		$.post(qs, data, cb, "json")
		.error(function(error){
			instance.onServiceError(error);
		});
	}
	
	this.getCommunicationToken = function(){
		method = 'getCommunicationToken';
		this.protocol = 'http://';
		var qs = this.protocol + this.host + this.endpoint + '?' + method;
		
		var data = {};
		data.method = method;
		data.parameters = {'secretKey': hex_md5(bpConfig.sessionID)};
		data.header = {"session":bpConfig.sessionID,"uuid":bpConfig.uuid};
		
		$.post(qs, data, bpServices.onCommunicationTokenSuccess, "json")
		.error(function(error){
			instance.onServiceError(error);
		});
		
	}

		
	//this.authenticateUser = function(username, pass, rememberUser){
	//	var method = 'authenticateUser';
	//	var t = this.generateToken(method);
	//	var p = {};
	//	p.method = method;
	//	p.parameters = {"password": pass, "savePassword": rememberUser, "username": user};
	//	p.header = {"token": t, "session": bpConfig.sessionID, "uuid": bpConfig.uuid};
	//	
	//	this.send(true,method,p,onAuthenticateUserSuccess);
	//}
	
	this.onCommunicationTokenSuccess = function(data){
		//The request to the server was successful, now we should check if the response is right or not
		//Retrieve the communicationToken and store it for future use
		instance.commToken = data.response;
		onCommunicationReady();
	}
	
	
	//this.onAuthenticateUserSuccess = function(data){
	//	//The request to the server was successful, now we should check if the response if right or not
	//	
	//	//Retrieve the userID and the authToken
	//}
	
	this.onServiceSuccess = function(success){
		//Do sth with this data;
	}

	this.onServiceError = function(error){
		//Display an error message noticing the user that the request to the server was not successful.
		console.log("Request error: \n");
		console.log(error['response']);
	}
	
	this.createRandomSalt = function(){
		var randomizer = '';
		var charsGenerated = 0;
		while (charsGenerated < 6){
			randomizer = randomizer + (Math.floor(Math.random() * 16)).toString(16);
			charsGenerated++;
		}
		return randomizer !== this.lastRandomizer ? (randomizer) : (createRandomSalt());
	}

	this.generateToken = function (method){
		var salt = this.createRandomSalt();
		var t = hex_sha1(method + ":" + this.commToken + ":" + this.statToken + ":" + salt);
		var s = salt + t;
		//console.log('Method:' + method + ', CommToken: ' + this.commToken + ', StatToken: ' + this.statToken + ', Salt: '+salt);
		return s;
	}
	
	
}