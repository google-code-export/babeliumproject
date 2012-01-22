
/**
 * ViewEvaluationModuleCommand
 */
var ViewEvaluationModuleCommand = Cairngorm.Command.extend(
{
	execute : function ()
	{
		var _this = this;
		
		BP.CMS.prepareMainContent("evaluation module", function ()
		{
			BP.EvaluationDelegate.viewEvaluationModule(_this);
		});
	},
	
	onResult : function ( response )
	{
		BP.SM.pushState("Evaluate - Babelium Project", {module : "evaluate"});
		BP.CMS.innerMainContent(response);
	},
	
	onFault : function ()
	{
		BP.CMS.abortLoading();
		alert("Error loading evaluation module");
	}
});