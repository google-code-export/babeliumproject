package modules.videoPlayer.controls.babelia
{
	import flash.display.Sprite;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	
	import modules.videoPlayer.controls.SkinableComponent;
	
	import mx.controls.ProgressBar;
	import mx.controls.ProgressBarMode;
	import mx.controls.Text;
	import mx.core.UIComponent;
	
	public class RoleTalkingPanel extends SkinableComponent
	{
		/**
		 * SKIN CONSTANTS
		 */
		public static const TEXT_COLOR:String = "textColor";
		public static const ROLE_COLOR:String = "roleColor";
		public static const BAR_COLOR:String = "barColor";
		public static const BORDER_COLOR:String = "borderColor";
		public static const BG_COLOR:String = "bgColor";
		public static const HL_COLOR:String="hlColor";
		public static const BORDER_WEIGHT:String="borderWeight";
		public static const CORNER_RADIUS:String="cornerRadius";
		
		private var _bg:Sprite;
		private var _textBox:Text;
		private var _roleBox:Text;
		private var _pBar:ProgressBar;
		private var _talking:Boolean = false;
		private var _boxWidth:Number = 200;
		private var _boxHeight:Number = 50;
		private var _defaultMargin:Number = 5;
		private var _timer:Timer;
		private var _duration:Number;
		private var _refreshTime:Number = 10;
		private var _startTime:Number;
		private var _pauseTime:Number;
		private var _highlight:Boolean = false;
		
		public function RoleTalkingPanel()
		{
			super("RoleTalkingPanel"); // Required for setup skinable component

			_bg = new Sprite();
			addChildAt(_bg, 0);

			_textBox = new Text();
			_textBox.setStyle("fontWeight", "bold");
			_textBox.selectable = false;
			_textBox.text = resourceManager.getString('myResources','LABEL_ROLE_CURRENTLY_TALKING')+": ";
			
			addChild(_textBox);
			
			_roleBox = new Text();
			_roleBox.setStyle("weight", "bold");
			_roleBox.selectable = false;
			
			addChild(_roleBox);
			
			_pBar = new ProgressBar( );
			_pBar.mode = ProgressBarMode.MANUAL;
			_pBar.label = "";
			_pBar.width = 20;
			_pBar.height = 20;
			
			addChild(_pBar);

			resize(_boxWidth, _boxHeight);
		}
		
		override public function availableProperties(obj:Array = null) : void
		{
			super.availableProperties([BG_COLOR,BORDER_COLOR,BAR_COLOR,TEXT_COLOR,ROLE_COLOR]);
		}
		
		public function resize(width:Number, height:Number) : void
		{			
			this.width = width;
			this.height = height;
			
			refresh();
		}
		
		override protected function updateDisplayList(w:Number, h:Number):void
		{
			if ( w != 0 ) width = w;
			if ( h != 0 ) height = h;
			
			CreateBG( width, height );
			
			_textBox.x = _defaultMargin*3;
			_textBox.y = _defaultMargin;
			_textBox.width = 55;
			_textBox.height = 20;
			_textBox.setStyle("color", getSkinColor(TEXT_COLOR));
			
			_roleBox.x = _textBox.x + _textBox.width;
			_roleBox.y = _textBox.y;
			_roleBox.width = width - _textBox.width - 2*_defaultMargin;
			_roleBox.height = 20;
			_roleBox.setStyle("color", getSkinColor(ROLE_COLOR));
			
			_pBar.x = _defaultMargin*2;
			_pBar.y = _textBox.y + _textBox.height;
			_pBar.width = width - 4*_defaultMargin;
			_pBar.setStyle("barColor", getSkinColor(BAR_COLOR));
		}
		
		public function get talking() : Boolean
		{
			return _talking;
		}
		
		public function setTalking(role:String, duration:Number) : void
		{
			_talking = true;
			_duration = duration;
			_roleBox.text = role;
			_pBar.minimum = 0;
			_pBar.maximum = duration;
			_startTime = flash.utils.getTimer();
			
			_timer = new Timer(_refreshTime);
			_timer.addEventListener(TimerEvent.TIMER, onTick);
			_timer.start();
		}
		
		public function pauseTalk() : void
		{
			_timer.stop();
			_pauseTime = flash.utils.getTimer();
		}
		
		public function resumeTalk() : void
		{
			var timeRunning:Number = _pauseTime - _startTime;
			_startTime = flash.utils.getTimer() - timeRunning;
			_timer.start();
		}
		
		public function stopTalk() : void
		{
			_timer.stop();
			_timer.reset();
			_talking = false;
			_pBar.setProgress(0,1);
			_roleBox.text = "";
		}
		
		private function onTick(event:TimerEvent) : void
		{
			var currentTime:Number = (flash.utils.getTimer() - _startTime) / 1000;
			
			if ( currentTime >= _duration )
			{
				_pBar.setProgress(0, _duration);
				_talking = false;
				_roleBox.text = "";
				_timer.stop();
				_timer.reset();
			}
			else
				_pBar.setProgress(currentTime, _duration);	
		}
		
		private function CreateBG( bgWidth:Number, bgHeight:Number ):void
		{
			_bg.graphics.clear();
			
			_bg.graphics.beginFill(getSkinColor(BORDER_COLOR));
			_bg.graphics.drawRoundRect(0, 0, width, height, getSkinColor(CORNER_RADIUS), getSkinColor(CORNER_RADIUS));
			_bg.graphics.endFill();
			if ( !_highlight )
				_bg.graphics.beginFill(getSkinColor(BG_COLOR));
			else
				_bg.graphics.beginFill(getSkinColor(HL_COLOR));
			_bg.graphics.drawRoundRect(getSkinColor(BORDER_WEIGHT), getSkinColor(BORDER_WEIGHT), width - (2*getSkinColor(BORDER_WEIGHT)), height - (2*getSkinColor(BORDER_WEIGHT)), getSkinColor(CORNER_RADIUS), getSkinColor(CORNER_RADIUS));
			_bg.graphics.endFill();
		}
		
		public function set highlight(flag:Boolean) : void
		{
			_highlight = flag;
			refresh();
		}
	}
}