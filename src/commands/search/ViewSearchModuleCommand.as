package commands.search
{
	import com.adobe.cairngorm.commands.ICommand;
	import com.adobe.cairngorm.control.CairngormEvent;
	
	import control.BabeliaBrowserManager;
	
	import events.CloseConnectionEvent;
	import events.ViewChangeEvent;
	
	import model.DataModel;
	
	import modules.search.Search;
	
	import spark.components.Group;
	
	public class ViewSearchModuleCommand implements ICommand
	{
		
		public function execute(event:CairngormEvent):void
		{
			var index:Class = ViewChangeEvent.VIEWSTACK_SEARCH_MODULE_INDEX;
			new CloseConnectionEvent().dispatch();
			if(DataModel.getInstance().appBody.numElements > 0)
				removeAllChildrenFromComponent(DataModel.getInstance().appBody);
			DataModel.getInstance().appBody.addElement(new index());
			
			
			BabeliaBrowserManager.getInstance().updateURL(
				BabeliaBrowserManager.index2fragment(index));
		}
		
		protected function removeAllChildrenFromComponent(component:Group):void
		{
			for (var i:uint=0; i < component.numElements; i++)
				component.removeElementAt(i);
		}

	}
}