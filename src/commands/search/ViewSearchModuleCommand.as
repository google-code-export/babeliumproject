package commands.search
{
	import com.adobe.cairngorm.commands.ICommand;
	import com.adobe.cairngorm.control.CairngormEvent;
	
	import events.ViewChangeEvent;
	
	import model.DataModel;
	
	public class ViewSearchModuleCommand implements ICommand
	{
		
		public function execute(event:CairngormEvent):void
		{
			DataModel.getInstance().viewContentViewStackIndex =
					ViewChangeEvent.VIEWSTACK_SEARCH_MODULE_INDEX;
		}

	}
}