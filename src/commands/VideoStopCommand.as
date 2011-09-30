package commands
{
	import com.adobe.cairngorm.commands.ICommand;
	import com.adobe.cairngorm.control.CairngormEvent;
	
	import model.DataModel;

	public class VideoStopCommand implements ICommand
	{
		public function execute(event:CairngormEvent):void
		{
			DataModel.getInstance().stopVideoFlag = 
					!DataModel.getInstance().stopVideoFlag;
		}
		
	}
}