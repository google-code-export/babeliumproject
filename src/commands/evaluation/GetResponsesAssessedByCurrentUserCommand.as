package commands.evaluation
{
	import com.adobe.cairngorm.commands.ICommand;
	import com.adobe.cairngorm.control.CairngormEvent;
	
	import mx.rpc.IResponder;
	
	public class GetResponsesAssessedByCurrentUserCommand implements ICommand, IResponder
	{
		public function GetResponsesAssessedByCurrentUserCommand()
		{
		}
		
		public function execute(event:CairngormEvent):void
		{
		}
		
		public function result(data:Object):void
		{
		}
		
		public function fault(info:Object):void
		{
		}
	}
}