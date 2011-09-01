package commands.subtitles
{
	import business.ExerciseDelegate;
	
	import com.adobe.cairngorm.commands.ICommand;
	import com.adobe.cairngorm.control.CairngormEvent;
	
	import events.SubtitleListEvent;
	
	import model.DataModel;
	
	import mx.collections.ArrayCollection;
	import mx.resources.ResourceManager;
	import mx.rpc.IResponder;
	import mx.rpc.events.FaultEvent;
	import mx.utils.ArrayUtil;
	import mx.utils.ObjectUtil;
	
	import view.common.CustomAlert;
	
	import vo.ExerciseVO;
	
	public class GetExercisesWithoutSubtitlesCommand implements ICommand, IResponder
	{	
		
		private var _dataModel : DataModel = DataModel.getInstance();
		
		public function execute(event:CairngormEvent):void
		{
			new ExerciseDelegate(this).getExercisesWithoutSubtitles();
		}
		
		public function result(data:Object):void
		{
			var result:Object=data.result;
			var resultCollection:ArrayCollection;
			
			if (result is Array && (result as Array).length > 0)
			{
				resultCollection=new ArrayCollection(ArrayUtil.toArray(result));
				
				if (!(resultCollection[0] is ExerciseVO))
				{
					CustomAlert.error(ResourceManager.getInstance().getString('myResources','ERROR_WHILE_RETRIEVING_SUBTITLES'));
				}
				else
				{
					//Set the data to the application's model
					_dataModel.exercisesWithoutSubtitles = resultCollection;
				
				}
			} else {
				_dataModel.exercisesWithoutSubtitles.removeAll();
			}
			_dataModel.exercisesWithoutSubtitlesRetrieved = ! _dataModel.exercisesWithoutSubtitlesRetrieved;
		}
		
		public function fault(info:Object):void
		{
			var faultEvent:FaultEvent = FaultEvent(info);
			CustomAlert.error(ResourceManager.getInstance().getString('myResources','ERROR_WHILE_RETRIEVING_SUBTITLES'));
			trace(ObjectUtil.toString(info));
		}
	}
}