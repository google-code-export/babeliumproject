package modules.configuration
{
	import events.StartConnectionEvent;
	
	import flash.errors.IOError;
	import flash.events.AsyncErrorEvent;
	import flash.events.IOErrorEvent;
	import flash.events.NetStatusEvent;
	import flash.net.NetConnection;
	import flash.net.NetStream;
	
	import model.DataModel;
	
	import modules.videoPlayer.NetStreamClient;
	
	import mx.binding.utils.BindingUtils;
	
	public class OflaDemoRed5Sentences
	{
		
		public var rec_ns:NetStreamClient;
		public var play_ns:NetStreamClient;
		public var nc:NetConnection;
		
		public var audioFilename:String;
		public var videoFilename:String;
		
		private const CONFIG_FOLDER:String=DataModel.getInstance().configStreamsFolder;
		
		public function OflaDemoRed5Sentences()
		{
			BindingUtils.bindSetter(netStatusHandler, DataModel.getInstance(), "netConnected");
		}
		
		public function connect():void{
			if (DataModel.getInstance().netConnection.connected == false)
				new StartConnectionEvent(DataModel.getInstance().streamingResourcesPath).dispatch();
			else
				netStatusHandler(false);
		}
		
		private function netStatusHandler(value:Boolean):void{   
			if (DataModel.getInstance().netConnected == true)
			{
				nc = DataModel.getInstance().netConnection;
				
				rec_ns = new NetStreamClient(nc,"Config Rec NetStream");
				play_ns = new NetStreamClient(nc,"Config Play NetStream"); 
				//rec_ns.addEventListener(AsyncErrorEvent.ASYNC_ERROR,asyncErrrorHandler);
				//rec_ns.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);
				//play_ns.addEventListener(AsyncErrorEvent.ASYNC_ERROR,asyncErrrorHandler);
				//play_ns.addEventListener(IOErrorEvent.IO_ERROR,ioErrorHandler);
				
				//var nsClient:Object=new Object();
				//nsClient.onMetaData=function():void{};
				//nsClient.onCuePoint=function():void{};
				//nsClient.onPlayStatus=function():void{};
				//play_ns.client = nsClient;
				
			} else {
				//Streaming server connection = false;
			}
		}
		
		/*
		private function ioErrorHandler(e:IOErrorEvent):void{
			//Avoid flash debugger error messages
		}	
			
		private function asyncErrrorHandler(e:AsyncErrorEvent):void{
			//Avoid flash debugger error messages
		}
		*/
		public function closeNetStreamObjects():void{
			rec_ns.netStream.close();
			play_ns.netStream.close();
		}
		
		public function play(mediaType:String):void{
			if (mediaType=='video'){
				play_ns.netStream.play(videoFilename+'.flv');
			}else{
				play_ns.netStream.play(audioFilename+'.flv');
			}
		}
		
		public function publish(mediaType:String):void{
			
			var d:Date=new Date();
			var fileName:String;
			
			if(mediaType == 'video') {
				fileName = "conf-video-" + d.getTime().toString();
				videoFilename=CONFIG_FOLDER + "/" + fileName;
				rec_ns.netStream.publish(videoFilename, 'record');
			} else {
				fileName = "conf-audio-" + d.getTime().toString();
				audioFilename=CONFIG_FOLDER + "/" + fileName;
				rec_ns.netStream.publish(audioFilename, 'record');
			}
		}

	}
}