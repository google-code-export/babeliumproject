package commands.videoUpload
{
	import com.adobe.cairngorm.commands.ICommand;
	import com.adobe.cairngorm.control.CairngormEvent;
	
	import model.DataModel;

	public class ViewUploadSignedInCommand implements ICommand
	{

		public function execute(event:CairngormEvent):void
		{
			DataModel.getInstance().currentUploadViewStackIndex = 1;
		}
		
	}
}