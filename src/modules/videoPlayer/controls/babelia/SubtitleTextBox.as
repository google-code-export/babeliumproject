package modules.videoPlayer.controls.babelia
{
	import flash.display.BlendMode;
	import flash.display.Sprite;
	import flash.filters.DropShadowFilter;
	import flash.text.TextField;
	import flash.text.TextFormat;
	
	import modules.videoPlayer.controls.SkinableComponent;
	
	import spark.components.HGroup;
	import spark.components.Label;
	import spark.layouts.HorizontalAlign;
	import spark.layouts.VerticalAlign;

	public class SubtitleTextBox extends SkinableComponent
	{
		/**
		 * SKIN CONSTANTS
		 */
		public static const TEXT_COLOR:String="textColor";
		public static const BOX_COLOR:String="boxColor";
		public static const BORDER_COLOR:String="borderColor";
		public static const BG_COLOR:String="bgColor";

		//private var _box:Sprite;
		//private var _bg:Sprite;
		private var _textBox:TextField;
		private var _textFormat:TextFormat;
		private var _boxWidth:Number=100;
		private var _boxHeight:Number=40;
		private var _defaultWidth:Number = 100;
		private var _defaultHeight:Number=40;
		
		private var _dropShadowFilter:DropShadowFilter;
		private var _group:HGroup;

		public function SubtitleTextBox()
		{
			super("SubtitleTextBox"); // Required for setup skinable component
			
			height = _defaultHeight;
			width = _defaultWidth;
			
			_dropShadowFilter = new DropShadowFilter();
			_dropShadowFilter.alpha = 1;
			_dropShadowFilter.distance = 0;
			_dropShadowFilter.color = 0x000000;
			_dropShadowFilter.strength = 15;
			_dropShadowFilter.blurX = 6;
			_dropShadowFilter.blurY = 6;
			
//			_group = new HGroup();
//			_group.verticalAlign = VerticalAlign.BOTTOM;
//			_group.horizontalAlign = HorizontalAlign.CENTER;
//			_group.paddingBottom = 6;

			_textFormat = new TextFormat();
			_textFormat.align = "center";
			_textFormat.font = "Arial";
			_textFormat.bold = true;
			_textFormat.size = 14;
			
			_textBox=new TextField();
			_textBox.multiline = true;
			_textBox.wordWrap = true;
			_textBox.selectable = false;
			_textBox.filters = [_dropShadowFilter];
			_textBox.setTextFormat(_textFormat);
			
	
			
//			_group.addElement(_textBox);
//			
//			addChild(_group);
			addChild(_textBox);

			//resize(_boxWidth, _boxHeight);
		}

		override public function availableProperties(obj:Array=null):void
		{
			super.availableProperties([BG_COLOR, BORDER_COLOR, BOX_COLOR, TEXT_COLOR]);
		}
		
		override protected function updateDisplayList(unscaledWidth:Number, unscaledHeight:Number):void
		{
			super.updateDisplayList( unscaledWidth, unscaledHeight );
			
			if( height == 0 ) 
				height = _defaultHeight;
			if( width == 0 ) 
				width = _defaultWidth;
			
			_textFormat.color = getSkinColor(TEXT_COLOR);
			_textBox.width = width*0.9;
			_textBox.x = this.width / 2 - _textBox.width/2;
			_textBox.y = this.height - (_textBox.textHeight) - 6;
			
			//_textBox.width=_group.width*0.9;
			_textBox.setTextFormat(_textFormat);
			
			
		}

		public function resize(width:Number, height:Number):void
		{
			this.width=width;
			this.height=height;
			
//			_group.width = width;
//			_group.height = height;
//			
//			_textFormat.color = getSkinColor(TEXT_COLOR);
//			_textBox.width = width*0.9;
//			_textBox.x = this.width / 2 - _textBox.width/2;
//			_textBox.y = this.height - _textBox.textHeight - 9;
//			
//			//_textBox.width=_group.width*0.9;
//			_textBox.setTextFormat(_textFormat);
		}

		public function setText(text:String,textColor:uint=0xffffff):void
		{
			_textFormat.color = textColor;
			_textBox.text=text;
			_textBox.setTextFormat(_textFormat);
			_textBox.y = this.height - (_textBox.textHeight) - 6;
			
		}
	}
}