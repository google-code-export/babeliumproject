package modules.search
{
	import flash.events.MouseEvent;
	
	import model.DataModel;
	
	import spark.components.Button;
	import spark.components.HGroup;

	public class VideoPaginator
	{

		[Bindable]
		[Embed(source="resources/images/first.png")]
		public static var firstI:Class;
		[Bindable]
		[Embed(source="resources/images/previous.png")]
		public static var previousI:Class;
		[Bindable]
		[Embed(source="resources/images/next.png")]
		public static var nextI:Class;
		[Bindable]
		[Embed(source="resources/images/last.png")]
		public static var lastI:Class;

		public static function createPaginationMenu(totalItemCount:int, itemsPerPage:int, currentPageNumber:int, displayedPageCount:int, container:HGroup, buttonClickHandler:Function):void
		{
			var maxPageButtonsInPagination:int=displayedPageCount;
			var limit:int=(maxPageButtonsInPagination + 1) / 2;
			var itemsPerPage:int=itemsPerPage;
			var itemCount:int=totalItemCount;
			var currentPage:int=currentPageNumber;
			var neededPageButtons:int=(itemCount % itemsPerPage == 0) ? (itemCount / itemsPerPage) : itemCount / itemsPerPage + 1;

			//Destroy the previous navigation menu
			destroyPaginationMenu(container);

			if (itemCount / itemsPerPage > 1)
			{
				//Create the first and previous page buttons if needed
				if (currentPage > 1)
				{
					container.addElement(createControlButton(1, firstI, buttonClickHandler));
					container.addElement(createControlButton(currentPage - 1, previousI, buttonClickHandler));
				}

				//Create the numbered page buttons
				if (neededPageButtons > maxPageButtonsInPagination)
				{
					if (currentPage <= limit)
					{
						for (var i:int=1; (i <= neededPageButtons && i <= maxPageButtonsInPagination); i++)
						{
							container.addElement(createPageButton(i, buttonClickHandler));
						}
					}
					else if (currentPage > neededPageButtons - limit)
					{
						for (var j:int=neededPageButtons - maxPageButtonsInPagination + 1; j <= neededPageButtons; j++)
						{
							container.addElement(createPageButton(j, buttonClickHandler));
						}
					}
					else
					{
						for (var k:int=currentPage - limit + 1; k <= currentPage + limit - 1; k++)
						{
							container.addElement(createPageButton(k, buttonClickHandler));
						}
					}
				}
				else
				{
					for (var h:int=1; (h <= neededPageButtons && h <= maxPageButtonsInPagination); h++)
					{
						container.addElement(createPageButton(h, buttonClickHandler));
					}
				}

				//Create the last and next page buttons if needed
				if (currentPage < neededPageButtons)
				{
					container.addElement(createControlButton(currentPage + 1, nextI, buttonClickHandler));
					container.addElement(createControlButton(neededPageButtons, lastI, buttonClickHandler));
				}
			}
			for (var z:uint = 0; z<container.numElements; z++)
			{
				var tmpButton:Button = container.getElementAt(z) as Button;
				if (tmpButton.id == currentPage.toString())
				{
					(container.getElementAt(z) as Button).enabled=false;
					break;
				}
			}
		}

		private static function destroyPaginationMenu(container:HGroup):void
		{
			container.removeAllElements();
		}

		private static function createPageButton(label:int, clickHandler:Function):Button
		{
			var navButton:Button = new Button();
			navButton.styleName = "paginationButton";
			navButton.id=label.toString();
			navButton.label=label.toString();
//			navButton.minWidth=18;
//			navButton.measuredWidth=18;
			navButton.height=40;
		
			navButton.addEventListener(MouseEvent.CLICK, clickHandler);

			return navButton;
		}

		private static function createControlButton(target:int, icon:Class, clickHandler:Function):Button
		{
			var ctrlButton:Button=new Button();
			ctrlButton.id=target.toString();
			ctrlButton.styleName = "paginationControlButton";
			ctrlButton.setStyle('icon', icon);
			ctrlButton.width=40;
			ctrlButton.height=40;

			ctrlButton.addEventListener(MouseEvent.CLICK, clickHandler);

			return ctrlButton;
		}
	}
}