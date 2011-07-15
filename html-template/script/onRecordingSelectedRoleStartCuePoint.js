function onRecordingSelectedRoleStartCuePoint(cue, VP){
	
	this.VP=VP;
	this.cue = cue;

	this.execute = function(){
		this.VP.setSubtitle(this.cue.text, this.cue.textColor);
		this.VP.muteVideo(true);
		this.VP.muteRecording(false);
		var time= this.cue.endTime - this.cue.startTime;
		this.VP.startTalking(this.cue.role, time);
		this.VP.highlight = true;
	}
}