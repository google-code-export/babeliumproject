package commands.videoUpload
{
	import business.ExerciseDelegate;
	
	import com.adobe.cairngorm.commands.ICommand;
	import com.adobe.cairngorm.control.CairngormEvent;
	
	import events.ExerciseEvent;
	
	import model.DataModel;
	
	import mx.rpc.IResponder;
	import mx.rpc.events.FaultEvent;
	import mx.utils.ObjectUtil;
	
	import view.common.CustomAlert;
	
	import vo.CreditHistoryVO;

	public class AddUnprocessedExerciseCommand implements ICommand, IResponder
	{

		public function execute(event:CairngormEvent):void
		{
			new ExerciseDelegate(this).addUnprocessedExercise((event as ExerciseEvent).exercise);
		}
		
		public function result(data:Object):void
		{
			//Should be the id of the added exercise
			if (!data.result is int){
				CustomAlert.error("Your exercise data could not be saved successfully.");
			} else {
				
				//Add this to the DataModel
				var exerciseId:int = int(data.result);
				var userId:int = DataModel.getInstance().loggedUser.id;
				
				var tempCreditHistory:CreditHistoryVO = new CreditHistoryVO();
				tempCreditHistory.userId = userId;
				tempCreditHistory.videoExerciseId = exerciseId;
				
				DataModel.getInstance().historicData = tempCreditHistory;
				
				DataModel.getInstance().unprocessedExerciseSaved = true;
			}
		}
		
		public function fault(info:Object):void
		{
			var faultEvent: FaultEvent = FaultEvent(info);
			CustomAlert.error("Error while saving exercise data.");
			trace(ObjectUtil.toString(info));
		}
		
	}
}