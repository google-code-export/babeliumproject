package business
{
	import com.adobe.cairngorm.business.ServiceLocator;
	
	import model.DataModel;
	
	import mx.rpc.AsyncToken;
	import mx.rpc.IResponder;
	import mx.rpc.remoting.RemoteObject;
	
	import vo.ChangePassVO;
	import vo.LoginVO;
	
	public class UserDelegate
	{
		
		private var responder:IResponder;
		
		public function UserDelegate(responder:IResponder)
		{
			this.responder = responder;
		}
		
		//Get top ten credited
		public function getTopTenCredited():void{
			var service:RemoteObject = ServiceLocator.getInstance().getRemoteObject("userRO");
			var pendingCall:AsyncToken = service.getTopTenCredited();
			pendingCall.addResponder(responder);
		}
		
		public function getUserInfo():void{
			var userId:int = 0;
			if (DataModel.getInstance().loggedUser != null)
				userId = DataModel.getInstance().loggedUser.id;
			var service:RemoteObject = ServiceLocator.getInstance().getRemoteObject("userRO");
			var pendingCall:AsyncToken = service.getUserInfo(userId);
			pendingCall.addResponder(responder);
		}
		
		public function restorePass(user:LoginVO):void{
			var service:RemoteObject = ServiceLocator.getInstance().getRemoteObject("userRO");
			var pendingCall:AsyncToken = service.restorePass(user.name);
			pendingCall.addResponder(responder);
		}

		public function changePass(user:ChangePassVO):void{
			var service:RemoteObject = ServiceLocator.getInstance().getRemoteObject("userRO");
			var pendingCall:AsyncToken = service.changePass(user.id, user.oldpass, user.newpass);
			pendingCall.addResponder(responder);
		}
	}
}
