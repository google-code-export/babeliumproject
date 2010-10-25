package modules.main
{
	import events.LoginEvent;
	import events.ViewChangeEvent;
	
	import flash.display.BitmapData;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.net.SharedObject;
	import flash.utils.clearInterval;
	import flash.utils.setInterval;
	
	import model.DataModel;
	
	import modules.userManagement.LoginRestorePassForm;
	
	import mx.binding.utils.BindingUtils;
	import mx.controls.LinkButton;
	import mx.controls.PopUpMenuButton;
	import mx.core.Application;
	import mx.core.FlexGlobals;
	import mx.core.UIComponent;
	import mx.events.CloseEvent;
	import mx.events.FlexEvent;
	import mx.events.MenuEvent;
	import mx.managers.PopUpManager;
	
	import spark.components.BorderContainer;
	import spark.components.HGroup;
	
	import view.common.PrivacyRights;
	
	import vo.LoginVO;

	public class HeaderClass extends BorderContainer
	{
		private var interval:uint;
		private var intervalLoops:int;

		private var rememberSO:SharedObject;

		//The keyCode for ENTER key
		public static const ENTER_KEY:int=13;
		
		[Bindable]
		public var userOptions:Array= new Array({code: 'LABEL_USER_ACCOUNT', action: ViewChangeEvent.VIEW_ACCOUNT_MODULE});

		//Visual components declaration
		public var userCP:HGroup;
		public var anonymousCP:HGroup;

		public var userCPName:PopUpMenuButton;
		public var uCrds:LinkButton;
		public var signInButton:LinkButton;
		public var signUpButton:LinkButton;
		public var signOutButton:LinkButton;
		public var helpFAQButton:LinkButton;
		
		public var localeComboBox:LocalizationComboBox;
		
		[Embed(source='resources/images/header_bgr_pttr.png')]
		public var bg:Class;



		public function HeaderClass()
		{
			super();
			this.addEventListener(FlexEvent.CREATION_COMPLETE, onCreationComplete);
		}
		
		public function onCreationComplete(event:FlexEvent):void
		{

			//Set the data bindings for this class
			setBindings();

			//First, check if any user cookie is present and if so, make the login
			rememberSO=SharedObject.getLocal("babeliaData");
			if (rememberSO.data.username != undefined && rememberSO.data.hash != undefined)
			{
				cachedAuthentication();
			}
			else
			{
				//Since our user isn't signed in we hide the users cp
				userCP.visible=false;
			}
		}

		public function setBindings():void
		{
			var model:DataModel=DataModel.getInstance();

			BindingUtils.bindSetter(onUserAuthenticated, model, "isLoggedIn");
			BindingUtils.bindSetter(creditsUpdated, model, "creditUpdateRetrieved");
			BindingUtils.bindSetter(exerciseRecording, model, "recordingExercise");
			BindingUtils.bindSetter(onAccountActivation, model, "accountActivationRetrieved");
		}

		public function signInClickHandler():void
		{
			//Create and show login popup
			showLogin();
		}

		public function showLogin():void
		{
			DataModel.getInstance().loginPop = PopUpManager.createPopUp(FlexGlobals.topLevelApplication.parent, LoginRestorePassForm, true) as LoginRestorePassForm;
			PopUpManager.centerPopUp(DataModel.getInstance().loginPop);
		}

		private function cachedAuthentication():void
		{
			var cachedUser:LoginVO=new LoginVO(rememberSO.data.username, rememberSO.data.hash);
			new LoginEvent(LoginEvent.PROCESS_LOGIN, cachedUser).dispatch();
		}

		public function signUpClickHandler():void
		{
			//Change contentViewStack to sign up page
			new ViewChangeEvent(ViewChangeEvent.VIEW_REGISTER_MODULE).dispatch();
		}
		
		public function userOptionsLabelFunction(item:Object):String{
			return resourceManager.getString('myResources', item.code.toString());
		}

		public function userOptionsItemClickHandler(event:MenuEvent):void
		{
			var whereToGo:String = event.item.action;
			//Change contentViewStack to account page
			new ViewChangeEvent(whereToGo).dispatch();
		}

		public function signOutClickHandler():void
		{
			//Since our user isn't signed in we hide the users cp
			new LoginEvent(LoginEvent.SIGN_OUT, null).dispatch();
			// Redirecting to home
			new ViewChangeEvent(ViewChangeEvent.VIEW_HOME_MODULE).dispatch();
			anonymousCP.visible=true;
			userCP.visible=false;
			rememberSO.clear();
		}

		private function onUserAuthenticated(upd:Boolean):void
		{
			if (DataModel.getInstance().isLoggedIn)
			{
				anonymousCP.visible=false;
				userCPName.label=DataModel.getInstance().loggedUser.name;
				uCrds.label=DataModel.getInstance().loggedUser.creditCount.toString();
				userCP.visible=true;
				localeComboBox.updateSelectedIndex();
			}
		}

		private function blinkCredits():void
		{
			if (intervalLoops <= 20)
			{
				if (uCrds.visible)
				{
					uCrds.visible=false;
					intervalLoops++;
				}
				else
				{
					uCrds.visible=true;
					intervalLoops++;
				}
			}
			else
			{
				uCrds.visible=true;
				clearInterval(interval);
			}
		}

		private function creditsUpdated(retr:Boolean):void
		{
			if (DataModel.getInstance().loggedUser && DataModel.getInstance().creditUpdateRetrieved)
			{
				uCrds.label=DataModel.getInstance().loggedUser.creditCount.toString();
				intervalLoops=0;
				interval=setInterval(blinkCredits, 300);
				DataModel.getInstance().creditUpdateRetrieved=false;
			}
		}
		
		private function exerciseRecording(value:Boolean):void{
			var status:Boolean = ! DataModel.getInstance().recordingExercise;
			signInButton.enabled = status;
			signUpButton.enabled = status;
			signOutButton.enabled = status;
		}
		
		public function helpFAQ_clickHandler(event:MouseEvent):void{
			new ViewChangeEvent(ViewChangeEvent.VIEW_HELP_MODULE).dispatch();	
		}
		
		private function onAccountActivation(flag:Boolean) : void
		{
			localeComboBox.updateSelectedIndex();
		}

	}
}